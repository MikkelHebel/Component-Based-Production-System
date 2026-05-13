namespace CPS_Whisperer;

public class RestAdapter : IProtocolAdapter
{
    public async Task<bool> Execute(WhisperCommandDto command)
    {
        using HttpClient client = new();
        client.BaseAddress = new Uri($"http://{command.Host}:{command.Port}/");

        var body = new Dictionary<string, object>
        {
            { "Program name", command.Command },
            { "state", 2 }
        };

        HttpResponseMessage response = await client.PutAsJsonAsync("v1/status", body);
        return response.IsSuccessStatusCode;
    }
}
