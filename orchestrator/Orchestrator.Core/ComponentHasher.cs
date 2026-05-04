using System.Security.Cryptography;
using System.Text;

namespace Orchestrator.Core;

public static class ComponentHasher
{
    public static string GetId(IComponent component)
    {
        string input = $"{component.ComponentType}:{component.Protocol}:{component.Host}:{component.Port}";
        return Convert.ToHexString(SHA256.HashData(Encoding.UTF8.GetBytes(input))).ToLowerInvariant();
    }
}
