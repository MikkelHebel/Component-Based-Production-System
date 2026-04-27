namespace Orchestrator.Core;

public class StepCommandDto {
    public uint BatchId { get; init; }
    public uint RecipeStepId { get; init; }
    public string ComponentType { get; init; }
    public string Command { get; init; }
    public List<string> Parameters { get; init; }
}
