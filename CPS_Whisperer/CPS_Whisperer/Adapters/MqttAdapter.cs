using System.Text;
using System.Text.Json;
using MQTTnet;
using MQTTnet.Client;

namespace CPS_Whisperer;

public class MqttAdapter : IProtocolAdapter
{
    public async Task<bool> Execute(WhisperCommandDto command)
    {
        var factory = new MqttFactory();
        using var client = factory.CreateMqttClient();

        var options = new MqttClientOptionsBuilder()
            .WithTcpServer(command.Host, command.Port)
            .Build();

        var tcs = new TaskCompletionSource<bool>();

        client.ApplicationMessageReceivedAsync += e =>
        {
            if (e.ApplicationMessage.Topic == "emulator/checkhealth")
            {
                string payload = Encoding.UTF8.GetString(e.ApplicationMessage.PayloadSegment);
                // Payload uses Python-style single quotes: {'IsHealthy': true}
                bool healthy = payload.Contains("true", StringComparison.OrdinalIgnoreCase);
                tcs.TrySetResult(healthy);
            }
            return Task.CompletedTask;
        };

        await client.ConnectAsync(options, CancellationToken.None);

        // Subscribe before publishing so we don't miss the completion message
        await client.SubscribeAsync("emulator/checkhealth");

        string operationPayload = JsonSerializer.Serialize(new { ProcessID = 1 });
        var message = new MqttApplicationMessageBuilder()
            .WithTopic("emulator/operation")
            .WithPayload(operationPayload)
            .Build();

        await client.PublishAsync(message, CancellationToken.None);

        using var cts = new CancellationTokenSource(TimeSpan.FromSeconds(120));
        cts.Token.Register(() => tcs.TrySetResult(false));

        bool success = await tcs.Task;
        await client.DisconnectAsync();
        return success;
    }
}
