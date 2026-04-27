using MQTTnet;
using MQTTnet.Client;
using MQTTnet.Protocol;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

class AssemblyStation : ICommunication {
    private JObject _mqttMsg;
    private string _mqttString;
    private string _health;
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
            _mqttString = e.ApplicationMessage.ConvertPayloadToString();
            //Console.WriteLine(_mqttString);
            UpdateHealth();

            //below doesnt work for checkhealth as it's not correct JSON, but a string
            _mqttMsg = JsonConvert.DeserializeObject<JObject>(
               e.ApplicationMessage.ConvertPayloadToString())!;
            
            return Task.CompletedTask; 
        };
    }

    public static AssemblyStation Instance { get { return _as_instance.Value; } }
    
    public MachineState State {get; set;}

    public async Task Command(int cmd) {
        var msg = new MQTTMessage{ ProcessID = cmd };

        var json = JsonConvert.SerializeObject(msg);

        var op = new MqttApplicationMessageBuilder()
            .WithTopic("emulator/operation")
            .WithPayload(json)
            .WithQualityOfServiceLevel(MqttQualityOfServiceLevel.AtLeastOnce)
            .Build();

        await _mqttClient.PublishAsync(op);
        //return value?
    }

    public string Status() {
        try {
            int s = Convert.ToInt32(JObject.FromObject(_mqttMsg).GetValue("State"));
            switch (s) {
                case 0: State = MachineState.Idle; break;
                case 1: State = MachineState.Executing; break;
                case 2: State = MachineState.Error; break;
                default: break;
            }
            return State.ToString();
        }
        catch (Exception) {
            return "NO STATE";
        }
    }

    public string CheckHealth() {        
        if (_health != null){
            return _health;
        }
        else {
            return "No operation has finished";
        }
    }
    

    public async Task Connect() { // Connecting to client to broker
        await _mqttClient.ConnectAsync(_mqttClientOptions, CancellationToken.None);
        Console.WriteLine("The MQTT client is connected.");
    }

    public async Task Subscribe() { // Subscribing to the topics
        await SubscribeToTopic("emulator/status");
        await SubscribeToTopic("emulator/checkhealth");
    }

    public async Task Disconnect() {
        MqttClientDisconnectOptions mqttClientDisconnectOptions = _mqttFactory.CreateClientDisconnectOptionsBuilder().Build();

        await _mqttClient.DisconnectAsync(mqttClientDisconnectOptions, CancellationToken.None);
        Console.WriteLine("The MQTT client has been disconnected");
    }


    public async Task SubscribeToTopic(string input)
    {
        //printout
        Console.WriteLine("Subscribing to: " + input);

        //define topics
        var topic = new MqttTopicFilterBuilder()
            .WithTopic(input)
            .Build();

        //subscribe
        await _mqttClient.SubscribeAsync(topic);
    }

    private void UpdateHealth(){
        if (_mqttString.Contains("true")){
            _health = "Healthy";
        }
        if (_mqttString.Contains("false")){
            _health = "Unhealthy";
        }
    }
}
public class MQTTMessage {
    public int ProcessID { get; set; }
}
