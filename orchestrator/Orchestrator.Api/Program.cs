using Orchestrator.Api;

var builder = WebApplication.CreateBuilder(args);
builder.Services.AddSingleton<ComponentRegistry>();
builder.Services.AddHostedService<ComponentLoader>();
builder.Services.AddHttpClient();
builder.Services.AddControllers();

var app = builder.Build();
app.MapControllers();
app.Run();
