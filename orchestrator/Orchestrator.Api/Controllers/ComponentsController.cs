using Microsoft.AspNetCore.Mvc;
namespace Orchestrator.Api;

[ApiController]
[Route("api/[controller]")]
public class ComponentsController : ControllerBase {
    private readonly ComponentRegistry _registry;

    public ComponentsController(ComponentRegistry registry)
    {
        _registry = registry;
    }
    
    [HttpGet("commands")]
    public IActionResult GetComponents()
    {
        return Ok(_registry.GetAll());
    }

    [HttpPut("markfree/{componentId}")]
    public IActionResult MarkFree(string componentId)
    {
        _registry.MarkFree(componentId);
        return Ok();
    }
}
