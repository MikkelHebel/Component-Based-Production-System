using Orchestrator.Core;

namespace Orchestrator.Tests;

public class TestComponent : IComponent
{
    public TestComponent(string componentType = "AGV", ushort port = 8082)
    {
        ComponentType = componentType;
        Port = port;
    }

    public string ComponentType { get; }
    public string Protocol { get; } = "REST";
    public string Host { get; } = "localhost";
    public ushort Port { get; }
    public List<CommandDefinition> SupportedCommands { get; } = new();
}
