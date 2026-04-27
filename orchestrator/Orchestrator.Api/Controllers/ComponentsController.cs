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
}
