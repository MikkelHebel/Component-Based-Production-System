namespace CPS_Whisperer;

public class SoapAdapter : IProtocolAdapter
{
    public async Task<bool> Execute(WhisperCommandDto command)
    {
        string soapBody = BuildEnvelope(command.Command, command.Parameters);

        using HttpClient client = new();
        using StringContent content = new(soapBody, System.Text.Encoding.UTF8, "text/xml");
        content.Headers.Add("SOAPAction", $"\"{command.Command}\"");

        HttpResponseMessage response = await client.PostAsync(
            $"http://{command.Host}:{command.Port}/Service.asmx",
            content);

        return response.IsSuccessStatusCode;
    }

    private static string BuildEnvelope(string methodName, List<string> parameters)
    {
        // Parameters are expected in "key=value" format; each becomes a named XML element.
        var paramElements = string.Concat(
            parameters
                .Select(p => p.Split('=', 2))
                .Where(parts => parts.Length == 2)
                .Select(parts => $"<{parts[0]}>{System.Security.SecurityElement.Escape(parts[1])}</{parts[0]}>")
        );

        return $"""
            <?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <soap:Body>
                <{methodName} xmlns="http://tempuri.org/">
                  {paramElements}
                </{methodName}>
              </soap:Body>
            </soap:Envelope>
            """;
    }
}
