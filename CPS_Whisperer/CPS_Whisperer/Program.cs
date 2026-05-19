WebApplicationBuilder builder = WebApplication.CreateBuilder(args);
builder.Services.AddHealthChecks();
builder.Services.AddControllers();
WebApplication app = builder.Build();
app.MapHealthChecks("/health");
app.MapControllers();
app.Run();
