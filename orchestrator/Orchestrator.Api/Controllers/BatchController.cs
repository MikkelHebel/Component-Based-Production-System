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
        lock (_balanceLock) {
            component = _registry.FindAvailable(command.ComponentType);
            if (component == null) return StatusCode(503);
            _registry.MarkBusy(component.ComponentId);
        }
        WhisperCommandDto whisperCommand = new WhisperCommandDto{
            ComponentId = component.ComponentId,
            Protocol = component.Protocol,
            Host = component.Host,
            Port = component.Port,
            Command = command.Command,
            Parameters = command.Parameters
        };

        var client = _client.CreateClient();
        var response = await client.PostAsJsonAsync("http://whisperer/api/execute", whisperCommand);
        if (!response.IsSuccessStatusCode) {
            _registry.MarkFree(component.ComponentId);
            return StatusCode(502);
        }
        return Ok();
    }
}
