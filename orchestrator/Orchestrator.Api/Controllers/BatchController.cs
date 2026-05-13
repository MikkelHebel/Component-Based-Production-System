using Microsoft.AspNetCore.Mvc;
using System.Threading;
using Orchestrator.Core;
namespace Orchestrator.Api;

[ApiController]
[Route("api/[controller]")]
public class BatchController : ControllerBase {
    private readonly ComponentRegistry _registry;
    private readonly IHttpClientFactory _client;
    private readonly System.Threading.Lock _balanceLock = new();

    public BatchController(ComponentRegistry registry, IHttpClientFactory HttpClient)
    {
        _registry = registry;
        _client = HttpClient;
    }

    [HttpPost("execute")]
    public async Task<IActionResult> Execute([FromBody] StepCommandDto command) {
        IComponent? component;
        string componentId;
        lock (_balanceLock) {
            component = _registry.FindAvailable(command.ComponentType);
            if (component == null) return StatusCode(503);
            componentId = ComponentHasher.GetId(component);
            _registry.MarkBusy(componentId);
        }
        WhisperCommandDto whisperCommand = new WhisperCommandDto{
            ComponentId = componentId,
            Protocol = component.Protocol,
            Host = component.Host,
            Port = component.Port,
            Command = command.Command,
            Parameters = command.Parameters
        };

        HttpClient client = _client.CreateClient();
        HttpResponseMessage response = await client.PostAsJsonAsync("http://whisperer:5000/api/execute", whisperCommand);
        _registry.MarkFree(componentId);
        if (!response.IsSuccessStatusCode) {
            return StatusCode(502);
        }
        return Ok();
    }
}
