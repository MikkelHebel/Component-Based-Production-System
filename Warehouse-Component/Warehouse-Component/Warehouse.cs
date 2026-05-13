namespace Warehouse_Component;
using Orchestrator.Core;

public class Warehouse : IComponent
{
    public string ComponentType => "Warehouse";
    public string Protocol => "SOAP";
    public string Host => "st4-warehouse";
    public ushort Port => 80;
    public List<CommandDefinition> SupportedCommands => new()
    {
        new CommandDefinition { Name = "PickItem", Parameters = new() { "trayId" } },
        new CommandDefinition { Name = "InsertItem", Parameters = new() { "trayId", "name" } },
        new CommandDefinition { Name = "GetInventory", Parameters = new() },
    };
}
