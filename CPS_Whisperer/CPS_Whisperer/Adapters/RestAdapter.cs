namespace CPS_Whisperer;

public class RestAdapter : IProtocolAdapter
{
    public async Task<bool> Execute(WhisperCommandDto command)
    {
        using HttpClient client = new();
        client.BaseAddress = new Uri($"http://{command.Host}:{command.Port}/");

        // Step 1: Load the program (State=1 required by AGV API before executing)
        var loadBody = new Dictionary<string, object>
        {
            { "Program name", command.Command },
            { "State", 1 }
        };
        HttpResponseMessage loadResponse = await client.PutAsJsonAsync("v1/status", loadBody);
        if (!loadResponse.IsSuccessStatusCode) return false;

        // Step 2: Start execution
        HttpResponseMessage executeResponse = await client.PutAsJsonAsync("v1/status", new { State = 2 });
        if (!executeResponse.IsSuccessStatusCode) return false;

        // Step 3: Poll until no longer executing (state != 2)
        using var cts = new CancellationTokenSource(TimeSpan.FromSeconds(120));
        while (!cts.IsCancellationRequested)
        {
            await Task.Delay(500, cts.Token);
            HttpResponseMessage statusResponse = await client.GetAsync("v1/status", cts.Token);
            if (!statusResponse.IsSuccessStatusCode) return false;
            var status = await statusResponse.Content.ReadFromJsonAsync<AgvStatus>(
                new System.Text.Json.JsonSerializerOptions { PropertyNameCaseInsensitive = true },
                cts.Token);
            if (status?.State != 2) return true;
        }
        return false;
    }

    private record AgvStatus(int State);
}
