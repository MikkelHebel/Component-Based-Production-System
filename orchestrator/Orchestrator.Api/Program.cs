using Orchestrator.Api;

WebApplicationBuilder builder = WebApplication.CreateBuilder(args);
builder.Services.AddHealthChecks();
builder.Services.AddSingleton<ComponentRegistry>();
builder.Services.AddHostedService<ComponentLoader>();
builder.Services.AddHttpClient();
builder.Services.AddControllers();

WebApplication app = builder.Build();
app.MapHealthChecks("/health");
app.MapControllers();
app.Run();
