namespace AGV_Component;
using Orchestrator.Core;

public class AGV : IComponent 
{
  public string ComponentType => "AGV";
  public string Protocol => "REST";
  public string Host => "st4-agv";
  public ushort Port => 80;
  public List<CommandDefinition> SupportedCommands => new()
  {
    new CommandDefinition { Name = "MoveToChargerOperation", Parameters = new() },
    new CommandDefinition { Name = "MoveToAssemblyOperation", Parameters = new() },
    new CommandDefinition { Name = "MoveToStorageOperation", Parameters = new() },
    new CommandDefinition { Name = "PutAssemblyOperation", Parameters = new() },
    new CommandDefinition { Name = "PickAssemblyOperation", Parameters = new() },
    new CommandDefinition { Name = "PickWarehouseOperation", Parameters = new() },
    new CommandDefinition { Name = "PutWarehouseOperation", Parameters = new() }
  };
}
