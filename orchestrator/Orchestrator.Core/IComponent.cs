namespace Orchestrator.Core;

public interface IComponent
{
    public string ComponentType { get; }
    public string Protocol { get; }
    public string Host { get; }
    public ushort Port { get; }
    public List<CommandDefinition> SupportedCommands { get; }
}
