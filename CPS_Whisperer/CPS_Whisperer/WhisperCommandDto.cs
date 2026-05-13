namespace CPS_Whisperer;

public class WhisperCommandDto
{
    public required string ComponentId { get; init; }
    public required string Protocol { get; init; }
    public required string Host { get; init; }
    public ushort Port { get; init; }
    public required string Command { get; init; }
    public List<string> Parameters { get; init; } = new();
}
