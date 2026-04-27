using Orchestrator.Core;
namespace Orchestrator.Api;

public class ComponentRegistry
{
    private Dictionary<string, IComponent> _components;
    private readonly ILogger<ComponentRegistry> _logger;
    private readonly HashSet<string> _busyComponents = new();

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

    public void DeRegister(string componentId) {
        if (!_components.ContainsKey(componentId)) {
            _logger.LogWarning($"Component {componentId} does not exist in registry");
            return;
        }
        _components.Remove(componentId);
        _logger.LogInformation($"Component {componentId} has been removed from registry");
    }

    public IComponent FindAvailable(string type) {
        return _components.Values.FirstOrDefault(c => c.ComponentType == type && !_busyComponents.Contains(c.ComponentId));
    }

    public List<IComponent> GetAll() {
        return _components.Values.ToList();
    }

    public void MarkBusy(string componentId) => _busyComponents.Add(componentId);
    public void MarkFree(string componentId) => _busyComponents.Remove(componentId);
}
