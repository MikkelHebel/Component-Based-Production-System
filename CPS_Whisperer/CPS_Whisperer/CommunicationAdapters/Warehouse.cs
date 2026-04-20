using WarehouseReference;
using System.Reflection;

class Warehouse : ICommunication {
    public MachineState state {get; set;}

    public int Command(string cmd) {
        MethodInfo mi = this.GetType().GetMethod(cmd); //substring for parameter(s)?
        mi.Invoke(this, null); //Invoke the method (null means no parameter for the method call, or you can pass array of parameters)
        return 0; //return value?
    }

    public string Status() {
        return "bich";
    }

    private void InsertItem(){
        
    }

    private void PickItem(){
        
    }

    //-----------------------

    private EmulatorServiceClient _client;

    private static readonly Lazy<Warehouse> _wh_instance = 
        new Lazy<Warehouse>(() => new Warehouse());
    
    private Warehouse(){
        _client = new EmulatorServiceClient(
            EmulatorServiceClient.EndpointConfiguration.BasicHttpBinding_IEmulatorService);
    }

    public static Warehouse Instance { get { return _wh_instance.Value; } }

    public async Task Run()
    {
        // Just a plain tester for the connection
        await CheckInventory();

        await InsertItem(1, "ItemA");

        await PickItem(1);
    }

    async Task CheckInventory() {
        try {
            Console.WriteLine("Requesting Inventory..."); 
            string inventoryJson = await _client.GetInventoryAsync(); 
            Console.WriteLine("Success! Data received:"); 
            Console.WriteLine(inventoryJson);
        }
        catch (Exception ex) {
            Console.WriteLine($"Error: {ex.Message}");
        }
    }

    async Task InsertItem(int trayId, string name) {
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

    async Task PickItem(int trayId) {
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

    //needed?
    /*public class WarehouseData {
        public List<Dictionary<string, string>> Inventory { get; set; }
        public int State { get; set; }
        public string TimeStamp { get; set; }
    }*/
}