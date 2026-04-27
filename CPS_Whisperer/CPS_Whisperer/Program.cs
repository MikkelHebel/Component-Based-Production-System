class Program
{
    static async Task Main(string[] args) {

        Console.WriteLine("  AGV");
        await AGV.Instance.Command("MoveToAssemblyOperation.1");

        //------------------------
        Console.WriteLine("\n  Warehouse");
        await Warehouse.Instance.Command("5.Item 1");
        await Warehouse.Instance.Command("1");
        await Warehouse.Instance.CheckInventory();

        //------------------------
        Console.WriteLine("\n  ASS station");
        await AssemblyStation.Instance.Connect();
        await AssemblyStation.Instance.Subscribe();

        Console.WriteLine("Write ProcessID: ");
        int processID = Convert.ToInt32(Console.ReadLine());
        await AssemblyStation.Instance.Command(processID);
        
        while (true) {
            Console.ReadLine(); 
            Console.WriteLine("AGV: " + AGV.Instance.Status());
            Console.WriteLine("WAREHOUSE: " + Warehouse.Instance.Status());
            Console.WriteLine("ASSEMBLY STATION: " + AssemblyStation.Instance.Status());
            Console.WriteLine("ASSEMBLY STATION HEALTH: " + AssemblyStation.Instance.CheckHealth());
        }
    }
}
 


























/* var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
// Learn more about configuring OpenAPI at https://aka.ms/aspnet/openapi
builder.Services.AddOpenApi();

var app = builder.Build();

// Configure the HTTP request pipeline.
if (app.Environment.IsDevelopment())
{
    app.MapOpenApi();
}

app.UseHttpsRedirection();

var summaries = new[]
{
    "Freezing", "Bracing", "Chilly", "Cool", "Mild", "Warm", "Balmy", "Hot", "Sweltering", "Scorching"
};

app.MapGet("/weatherforecast", () =>
{
    var forecast =  Enumerable.Range(1, 5).Select(index =>
        new WeatherForecast
        (
            DateOnly.FromDateTime(DateTime.Now.AddDays(index)),
            Random.Shared.Next(-20, 55),
            summaries[Random.Shared.Next(summaries.Length)]
        ))
        .ToArray();
    return forecast;
})
.WithName("GetWeatherForecast");

app.Run();

record WeatherForecast(DateOnly Date, int TemperatureC, string? Summary)
{
    public int TemperatureF => 32 + (int)(TemperatureC / 0.5556);
}*/
