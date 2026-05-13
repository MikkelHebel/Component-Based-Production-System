using Microsoft.AspNetCore.Mvc;

namespace CPS_Whisperer.Controllers;

[ApiController]
[Route("api")]
public class WhisperController : ControllerBase
{
    [HttpPost("execute")]
    public async Task<IActionResult> Execute([FromBody] WhisperCommandDto command)
    {
        IProtocolAdapter adapter = command.Protocol.ToUpperInvariant() switch
        {
            "REST"  => new RestAdapter(),
            "SOAP"  => new SoapAdapter(),
            "MQTT"  => new MqttAdapter(),
            _       => throw new NotSupportedException($"Protocol '{command.Protocol}' is not supported.")
        };

        bool success = await adapter.Execute(command);
        return success ? Ok() : StatusCode(502);
    }
}
