using Orchestrator.Core;
namespace Orchestrator.Api;

public class ComponentRegistry
{
    private Dictionary<string, IComponent> _components = new();
    private HashSet<string> _busyComponents = new();
    private readonly ILogger<ComponentRegistry> _logger;

    public ComponentRegistry(ILogger<ComponentRegistry> logger) {
        _logger = logger;
    }

    public void Register(IComponent component) {
        string id = ComponentHasher.GetId(component);
        if (_components.ContainsKey(id)) {
            _logger.LogWarning($"Component {id} already registered.");
            return;
        }
        _components[id] = component;
        _logger.LogInformation($"Component {id} ({component.ComponentType} @ {component.Host}:{component.Port}) registered.");
    }

    public void DeRegister(string componentId) {
        if (!_components.ContainsKey(componentId)) {
            _logger.LogWarning($"Component {componentId} does not exist in registry.");
            return;
        }
        _components.Remove(componentId);
        _busyComponents.Remove(componentId);
        _logger.LogInformation($"Component {componentId} removed from registry.");
    }

    public IComponent? FindAvailable(string type) {
        return _components
            .FirstOrDefault(kvp => kvp.Value.ComponentType == type && !_busyComponents.Contains(kvp.Key))
            .Value;
    }

    public string? FindAvailableId(string type) {
        return _components
            .FirstOrDefault(kvp => kvp.Value.ComponentType == type && !_busyComponents.Contains(kvp.Key))
            .Key;
    }

    public List<IComponent> GetAll() {
        return _components.Values.ToList();
    }

    public void MarkBusy(string componentId) => _busyComponents.Add(componentId);
    public void MarkFree(string componentId) => _busyComponents.Remove(componentId);
}
