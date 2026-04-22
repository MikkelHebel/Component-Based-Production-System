namespace Orchestrator.Core;

public interface IComponent
{
    public string ComponentType { get; }
    public string ComponentId { get; }
    public string DLLPath { get; }
    public string Protocol { get; }
    public string Host { get; }
    public int Port { get; }
    public List<CommandDefinition> SupportedCommands { get; }
}
