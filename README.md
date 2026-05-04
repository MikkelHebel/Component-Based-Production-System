# Component Based Production System
> [!IMPORTANT]
> Remember to update `DB_PASSWORD=` and `POSTGRES_PASSWORD=` in `.env`

## Creating a new component
Components are .dll files that implements the IComponent interface from the Orchestrator.

1. Step
To create a new component start out by creating a C# class library.
```bash
mkdir MyComponent
cd MyComponent
dotnet new classlib -n MyComponent
```

2. Step
Your component needs to know about the IComponent interface from Orchestrator.Core add a project reference (Update the path accordingly, the path below assumes you created your component in components/):
```bash
dotnet add reference ../orchestrator/Orchestrator.Core/Orchestrator.Core.csproj
```

3. Step
Implement IComponent in your component
```c#
using Orchestrator.Core;

public class MyComponent : IComponent
{
  public string ComponentType => "Mixer";
  public string Protocol => "MQTT";
  public string Host => "localhost";
  public ushort Port => 1234;
  public List<CommandDefinition> SupportedCommands => new()
  {
    new CommandDefinition { Name = "mix", Parameters = new() { "speed", "duration" } },
    new CommandDefinition { Name = "stop", Parameters = new() }
  };
}
```

4. Step
Build the component as a .dll
```bash
dotnet build -c Release
```
The build dll will be in bin/Release/net9.0/MyComponent.dll

5. Step
Drop your component into components/
