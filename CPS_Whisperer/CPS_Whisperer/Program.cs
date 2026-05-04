using System;

class Program
{
    static async Task Main(string[] args) {

        Console.WriteLine("  AGV");
        await AGV.Instance.Command("MoveToAssemblyOperation.1");

        //------------------------
        Console.WriteLine("\n  Warehouse");
        await Warehouse.Instance.Command("1");
        await Warehouse.Instance.Command("1.Item 1");
        await Warehouse.Instance.CheckInventory();

        //------------------------
        Console.WriteLine("\n  Assembly station");
        await AssemblyStation.Instance.Connect();
        await AssemblyStation.Instance.Subscribe();

        Console.WriteLine("Write ProcessID: ");
        int processID = Convert.ToInt32(Console.ReadLine());
        await AssemblyStation.Instance.Command(processID);
        
        while (true) {
            Console.ReadLine(); 
            Console.WriteLine("AGV: " + AGV.Instance.Status());
            Console.WriteLine("Battery: " + AGV.Instance.battery + "%");
            Console.WriteLine("WAREHOUSE: " + Warehouse.Instance.Status());
            Console.WriteLine("ASSEMBLY STATION: " + AssemblyStation.Instance.Status());
            Console.WriteLine("ASSEMBLY STATION HEALTH: " + AssemblyStation.Instance.CheckHealth());
        }
    }
}

