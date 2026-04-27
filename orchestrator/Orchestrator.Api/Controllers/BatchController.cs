using Microsoft.AspNetCore.Mvc;
using Orchestrator.Core;
namespace Orchestrator.Api;

[ApiController]
[Route("api/[controller]")]
public class BatchController : ControllerBase {
    private readonly ComponentRegistry _registry;
    private readonly IHttpClientFactory _client,

    public BatchController(ComponentRegistry registry, IHttpClientFactory HttpClient)
    {
        _registry = registry;
        _client = HttpClient;
    }

    [HttpPost("execute")]
    public IActionResult Execute([FromBody] StepCommandDto command) {
        var component = _registry.FindAvailable(command.ComponentType);
        if (component == null) return StatusCode(503);
        return Ok();
    }
}
