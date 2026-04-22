using System.IO;
using System.Reflection;
using Orchestrator.Core;
namespace Orchestrator.Api;

public class ComponentLoader : BackgroundService
{
    private readonly ComponentRegistry _registry;
    private readonly ILogger<ComponentLoader> _logger;

    public ComponentLoader(ComponentRegistry registry, ILogger<ComponentLoader> logger) {
        _registry = registry;
        _logger = logger;
    }

    protected override async Task ExecuteAsync(CancellationToken stoppingToken) {
        // FileSystemWatcher watches for creation and deletion of files in components/
        string wd = AppDomain.CurrentDomain.BaseDirectory;
        string filePath = Path.GetFullPath(System.IO.Path.Combine(wd, @"../../Components/"));
        using var watcher = new FileSystemWatcher(filePath);
        
        watcher.NotifyFilter = NotifyFilters.FileName;

        watcher.Created += OnCreated;
        watcher.Deleted += OnDeleted;

        watcher.Filter = "*.dll";
        watcher.EnableRaisingEvents = true;
        await Task.Delay(Timeout.Infinite, stoppingToken);
    }

    private void OnCreated(object sender, FileSystemEventArgs e) {
        var path = e.FullPath;
        try {
              // Load the DLL
              var assembly = AssemblyLoadContext.Default.LoadFromAssemblyPath(path);
              // Use reflection to search for IComponent implementations
              var types = assembly.GetTypes().Where(t => typeof(IComponent).IsAssignableFrom(t) && !t.IsInterface);

              // Each type in the DLL that implements IComponent will be added to the component registry
              foreach (var type in types) {
                  if (Activator.CreateInstance(type) is not IComponent component) continue;
                  _registry.Register(component);
              }

        } catch (BadImageFormatException ex) {
              _logger.LogError($"DLL {path} is not a valid .NET assembly: {ex.Message}");
        } catch (FileLoadException ex) {
              _logger.LogError($"DLL {path} assembly already loaded or access denied: {ex.Message}");
        } catch (ReflectionTypeLoadException ex) {
              _logger.LogError($"DLL {path} loaded but its dependencies are missing: {ex.Message}");
        }
    }

    private void OnDeleted(object sender, FileSystemEventArgs e) {
        _registry.DeRegister(e.FullPath);
    }
}
