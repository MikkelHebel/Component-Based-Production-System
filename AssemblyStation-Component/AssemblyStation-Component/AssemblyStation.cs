namespace AssemblyStation_Component;
using Orchestrator.Core;

public class AssemblyStation : IComponent
{
    public string ComponentType => "AssemblyStation";
    public string Protocol => "MQTT";
    public string Host => "mqtt";
    public ushort Port => 1883;
    public List<CommandDefinition> SupportedCommands => new()
    {
        new CommandDefinition { Name = "StartProcess", Parameters = new() { "processId" } },
    };
}
