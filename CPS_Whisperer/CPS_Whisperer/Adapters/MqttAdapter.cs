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

        await client.ConnectAsync(options, CancellationToken.None);

        string payload = JsonSerializer.Serialize(new
        {
            command = command.Command,
            parameters = command.Parameters,
        });

        var message = new MqttApplicationMessageBuilder()
            .WithTopic($"commands/{command.ComponentId}")
            .WithPayload(payload)
            .Build();

        MqttClientPublishResult result = await client.PublishAsync(message, CancellationToken.None);
        await client.DisconnectAsync();

        return result.IsSuccess;
    }
}
