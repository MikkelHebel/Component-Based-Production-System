using System;
using WarehouseReference;
using Newtonsoft.Json.Linq;

class Warehouse : ICommunication {
    private string _inventoryJson;
    private EmulatorServiceClient _client;

    private static readonly Lazy<Warehouse> _wh_instance = new Lazy<Warehouse>(() => new Warehouse());
    
    private Warehouse(){
        _client = new EmulatorServiceClient(
            EmulatorServiceClient.EndpointConfiguration.BasicHttpBinding_IEmulatorService);
    }

    public static Warehouse Instance { get { return _wh_instance.Value; } }
    
    public MachineState State {get; set;}

    public async Task Command(string cmd) {
        if (cmd.Contains(".")) {
            int t = Convert.ToInt32(cmd.Substring(0, cmd.IndexOf(".")));
            string n = cmd.Substring(cmd.IndexOf(".")+1);
            await InsertItem(t,n);

            Console.WriteLine(t + n); //for purpose of showing output, delete
        }

        else {
            int t = Convert.ToInt32(cmd);
            await PickItem(t);

            Console.WriteLine(t); //for purpose of showing output, delete
        }
        
    }

    public string Status() {
        try {
            JObject json = JObject.Parse(_inventoryJson);
            int s = Convert.ToInt32(json.GetValue("State"));
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

    private async Task InsertItem(int trayId, string name) {
        try {
            Console.WriteLine($"Inserting '{name}' into tray {trayId}...");

            string response = await _client.InsertItemAsync(trayId, name);

            Console.WriteLine("Item inserted:");
            Console.WriteLine(response);
        }
        catch (Exception ex) {
            Console.WriteLine($"Insert failed: {ex.Message}");
        }
    }

    private async Task PickItem(int trayId) {
        try {
            Console.WriteLine($"Picking item from tray {trayId}...");

            string response = await _client.PickItemAsync(trayId);

            Console.WriteLine("Pick completed:");
            Console.WriteLine(response);
        }
        catch (Exception ex) {
            Console.WriteLine($"Pick failed: {ex.Message}");
        }
    }

    public async Task CheckInventory() {
        try {
            Console.WriteLine("Requesting Inventory..."); 
            _inventoryJson = await _client.GetInventoryAsync(); 
            Console.WriteLine("Success! Data received:"); 
            Console.WriteLine(_inventoryJson);
        }
        catch (Exception ex) {
            Console.WriteLine($"Error: {ex.Message}");
        }
    }
}
