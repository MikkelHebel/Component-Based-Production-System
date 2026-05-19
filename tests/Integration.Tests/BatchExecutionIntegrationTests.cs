using System.Net.Http.Json;
using NUnit.Framework;

namespace Integration.Tests;

[TestFixture]
public class BatchExecutionIntegrationTests
{
    private readonly HttpClient _orchestrator = new() { BaseAddress = new Uri("http://localhost:5001") };

    private string _componentType = null!;
    private string _command = null!;

    private record ComponentDto(string ComponentType, List<CommandDto> SupportedCommands);
    private record CommandDto(string Name, List<string> Parameters);

    [OneTimeSetUp]
    public async Task DiscoverComponents()
    {
        for (int i = 0; i < 30; i++)
        {
            try
            {
                var res = await _orchestrator.GetAsync("/api/components/commands");
                if (res.IsSuccessStatusCode)
                {
                    var components = await res.Content.ReadFromJsonAsync<List<ComponentDto>>();
                    if (components?.Count > 0)
                    {
                        _componentType = components[0].ComponentType;
                        _command       = components[0].SupportedCommands[0].Name;
                        return;
                    }
                }
            }
            catch { }
            await Task.Delay(1000);
        }
        Assert.Fail("Orchestrator did not load components within 30 seconds.");
    }

    [OneTimeTearDown]
    public void TearDown() => _orchestrator.Dispose();

    [Test]
    public async Task Execute_Returns503_ForUnknownComponentType()
    {
        var command = new
        {
            BatchId       = 1,
            RecipeStepId  = 1,
            ComponentType = "NonExistent",
            Command       = "Test",
            Parameters    = Array.Empty<string>(),
        };

        var response = await _orchestrator.PostAsJsonAsync("/api/batch/execute", command);

        Assert.That((int)response.StatusCode, Is.EqualTo(503));
    }

    [Test]
    public async Task Execute_ForwardsToWhisperer_WhenComponentExists()
    {
        var command = new
        {
            BatchId       = 1,
            RecipeStepId  = 1,
            ComponentType = _componentType,
            Command       = _command,
            Parameters    = Array.Empty<string>(),
        };

        var response = await _orchestrator.PostAsJsonAsync("/api/batch/execute", command);

        // 503 = no component found — chain never started.
        // 200 or 502 both confirm the orchestrator forwarded to the whisperer.
        Assert.That((int)response.StatusCode, Is.Not.EqualTo(503));
    }
}
