namespace CPS_Whisperer;

public interface IProtocolAdapter
{
    Task<bool> Execute(WhisperCommandDto command);
}
