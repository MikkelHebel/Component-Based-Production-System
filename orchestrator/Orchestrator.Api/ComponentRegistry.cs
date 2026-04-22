using Orchestrator.Core;
namespace Orchestrator.Api;

public class ComponentRegistry
{
    private Dictionary<string, IComponent> _components;
    private readonly ILogger<ComponentRegistry> _logger;

    public ComponentRegistry(ILogger<ComponentRegistry> logger) {
        _logger = logger;
        _components = new Dictionary<string, IComponent>();
    }

    public void Register(IComponent component) {
        if (_components.ContainsKey(component.ComponentId)) {
            _logger.LogWarning($"Component {component.ComponentId} already registered.");
            return;
        }
        _components[component.ComponentId] = component;
        _logger.LogInformation($"Component {component.ComponentId} has been added to registry");
    }

    public void DeRegister(string path) {
        var component = _components.Values.FirstOrDefault(c => c.DLLPath == path);
        if (component == null) {
            _logger.LogWarning($"No component found for path {path}");
            return;
        }
        _components.Remove(component.ComponentId);
        _logger.LogInformation($"Component {component.ComponentId} has been removed from registry");
    }

    public IComponent FindAvailable(string type) {
        return _components.Values.FirstOrDefault(c => c.ComponentType == type);
    }
}
