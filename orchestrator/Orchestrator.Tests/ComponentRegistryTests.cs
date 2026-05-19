using Microsoft.Extensions.Logging.Abstractions;
using NUnit.Framework;
using Orchestrator.Api;
using Orchestrator.Core;

namespace Orchestrator.Tests;

[TestFixture]
public class ComponentRegistryTests
{
    private ComponentRegistry _registry = null!;

    [SetUp]
    public void SetUp()
    {
        _registry = new ComponentRegistry(NullLogger<ComponentRegistry>.Instance);
    }

    [Test]
    public void FindAvailable_ReturnsNull_WhenEmptyOrAllBusy()
    {
        Assert.That(_registry.FindAvailable("AGV"), Is.Null);

        var component = new TestComponent("AGV", 8082);
        _registry.Register(component);
        _registry.MarkBusy(ComponentHasher.GetId(component));

        Assert.That(_registry.FindAvailable("AGV"), Is.Null);
    }

    [Test]
    public void FindAvailable_ReturnsComponent_WhenOneIsFree()
    {
        _registry.Register(new TestComponent("AGV", 8082));

        Assert.That(_registry.FindAvailable("AGV"), Is.Not.Null);
    }

    [Test]
    public void MarkFree_AllowsComponentToBeFoundAgain()
    {
        var component = new TestComponent("AGV", 8082);
        string id = ComponentHasher.GetId(component);

        _registry.Register(component);
        _registry.MarkBusy(id);
        Assert.That(_registry.FindAvailable("AGV"), Is.Null);

        _registry.MarkFree(id);
        Assert.That(_registry.FindAvailable("AGV"), Is.Not.Null);
    }
}
