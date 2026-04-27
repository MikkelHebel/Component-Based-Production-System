using Orchestrator.Api;

var builder = WebApplication.CreateBuilder(args);
builder.Services.AddSingleton<ComponentRegistry>();
builder.Services.AddHostedService<ComponentLoader>();

var app = builder.Build();
app.Run();
