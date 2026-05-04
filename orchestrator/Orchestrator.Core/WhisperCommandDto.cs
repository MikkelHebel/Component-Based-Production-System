namespace Orchestrator.Core;

public class WhisperCommandDto {
    public string ComponentId { get; init; }
    public string Protocol { get; init; }
    public string Host { get; init; }
    public ushort Port { get; init; }
    public string Command { get; init; }
    public List<string> Parameters { get; init; }
}
