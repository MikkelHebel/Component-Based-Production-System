using MQTTnet;
using MQTTnet.Client;
using MQTTnet.Protocol;
using System.Reflection;
using Newtonsoft.Json;

class AssemblyStation : ICommunication {
    public MachineState state {get; set;}

    public int Command(string cmd) {
        MethodInfo mi = this.GetType().GetMethod(cmd); //substring for parameter(s)?
        mi.Invoke(this, null); //Invoke the method (null means no parameter for the method call, or you can pass array of parameters)
        return 0; //return value?
    }

    public string Status() {
        return "bich";
    }

    public string CheckHealth(){
        return "biiiich";
    }

    //-----------------------

    private MqttFactory _mqttFactory = new MqttFactory();
    private IMqttClient _mqttClient;
    private MqttClientOptions _mqttClientOptions;
    private static readonly Lazy<AssemblyStation> _as_instance =
    new Lazy<AssemblyStation>(() => new AssemblyStation());

    private AssemblyStation(){
        _mqttClient = _mqttFactory.CreateMqttClient();
        _mqttClientOptions = new MqttClientOptionsBuilder().WithTcpServer("localhost", 1884).Build();
 
        // Setting up subscriber handler pre connection to ensure no messages are lost
        // Lambda used as an anonymous function. Circumvents defining unnecessary methods elsewhere
        _mqttClient.ApplicationMessageReceivedAsync += e => {
            Console.WriteLine($"MQTT: {e.ApplicationMessage.ConvertPayloadToString()}");
            return Task.CompletedTask; 
        };
    }

    public static AssemblyStation Instance { get { return _as_instance.Value; } }

    

    public async Task Connect() { // Connecting to client to broker
        await _mqttClient.ConnectAsync(_mqttClientOptions, CancellationToken.None);
        Console.WriteLine("The MQTT client is connected.");
    }

    public async Task Subscribe() { // Subscribing to the topics
        // MqttClientSubscribeOptions mqttSubscribeHealth = _mqttFactory.CreateSubscribeOptionsBuilder()
        //     .WithTopicFilter("emulator/checkhealth").Build();
        // MqttClientSubscribeOptions mqttSubscribeStatus = _mqttFactory.CreateSubscribeOptionsBuilder()
        //     .WithTopicFilter("emulator/status").Build();
        // MqttClientSubscribeOptions mqttSubscribeOperation = _mqttFactory.CreateSubscribeOptionsBuilder()
        //     .WithTopicFilter("emulator/operation").Build();

        // await _mqttClient.SubscribeAsync(mqttSubscribeHealth, CancellationToken.None);
        // await _mqttClient.SubscribeAsync(mqttSubscribeStatus, CancellationToken.None);
        // await _mqttClient.SubscribeAsync(mqttSubscribeOperation, CancellationToken.None);
        //  Console.WriteLine("The MQTT client subscribed to topics");

        Console.WriteLine("Connected.");
        SubscribeToTopic("emulator/status");
        SubscribeToTopic("emulator/checkhealth");
    }

    public async Task Disconnect() {
        MqttClientDisconnectOptions mqttClientDisconnectOptions = _mqttFactory.CreateClientDisconnectOptionsBuilder().Build();

        await _mqttClient.DisconnectAsync(mqttClientDisconnectOptions, CancellationToken.None);
        Console.WriteLine("The MQTT client has been disconnected");
    }

    public async Task Publish(){
        var msg = new MQTTMessage{ ProcessID = 1000 };

        var json = JsonConvert.SerializeObject(msg);

        var op = new MqttApplicationMessageBuilder()
            .WithTopic("emulator/operation")
            .WithPayload(json)
            .WithQualityOfServiceLevel(MqttQualityOfServiceLevel.AtLeastOnce)
            .Build();

        await _mqttClient.PublishAsync(op);
    }


        public async void SubscribeToTopic(string input)
        {
            //printout
            Console.WriteLine("Subscribing to : " + input);

            //define topics
            var topic = new MqttTopicFilterBuilder()
                .WithTopic(input)
                .Build();

            //subscribe
            await _mqttClient.SubscribeAsync(topic);
        }
}
    public class MQTTMessage
    {
        public int ProcessID { get; set; }
    }
