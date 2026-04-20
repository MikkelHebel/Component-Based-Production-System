using MQTTnet;
using MQTTnet.Client;
// main method for only for testing
class Program
{
    static async Task Main(string[] args) {

    Console.WriteLine("  AGV");
    AGV agv = AGV.Instance;
    try {
      string putResponse = await agv.Execute("MoveToAssemblyOperation", 1);
      Console.WriteLine($"PUT response: {putResponse}");
      await agv.Execute("MoveToAssemblyOperation", 1);
      string getResponse = await agv.GetStatus();
      Console.WriteLine($"GET response: {getResponse}");
      await agv.GetStatus();
    } 
    catch (HttpRequestException e) {
        Console.WriteLine($"Request failed: {e.Message}");
        Console.WriteLine($"Status code: {e.StatusCode}");
    }

    //------------------------
    Console.WriteLine("\n  Warehouse");
    await Warehouse.Instance.Run();


    //------------------------
    Console.WriteLine("\n  ASS station");
    await AssemblyStation.Instance.Connect();
    await AssemblyStation.Instance.Subscribe();
    await AssemblyStation.Instance.Publish();
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
