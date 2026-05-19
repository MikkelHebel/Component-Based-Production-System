using System.Net;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging.Abstractions;
using Moq;
using NUnit.Framework;
using Orchestrator.Api;
using Orchestrator.Core;

namespace Orchestrator.Tests;

[TestFixture]
public class BatchControllerTests
{
    private ComponentRegistry _registry = null!;
    private StepCommandDto _command = null!;

    [SetUp]
    public void SetUp()
    {
        _registry = new ComponentRegistry(NullLogger<ComponentRegistry>.Instance);

        _command = new StepCommandDto
        {
            BatchId       = 1,
            RecipeStepId  = 1,
            ComponentType = "AGV",
            Command       = "MoveToAssemblyOperation",
            Parameters    = new List<string>(),
        };
    }

    [Test]
    public async Task Execute_Returns503_WhenNoComponentAvailable()
    {
        var controller = new BatchController(_registry, new Mock<IHttpClientFactory>().Object);

        var result = await controller.Execute(_command);

        Assert.That(result, Is.InstanceOf<StatusCodeResult>());
        Assert.That(((StatusCodeResult)result).StatusCode, Is.EqualTo(503));
    }

    [Test]
    public async Task Execute_Returns200AndFreesComponent_WhenWhispererSucceeds()
    {
        _registry.Register(new TestComponent("AGV", 8082));

        var handler     = new StubHttpMessageHandler(HttpStatusCode.OK);
        var httpClient  = new HttpClient(handler);
        var factoryMock = new Mock<IHttpClientFactory>();
        factoryMock.Setup(f => f.CreateClient(It.IsAny<string>())).Returns(httpClient);

        var controller = new BatchController(_registry, factoryMock.Object);

        var result = await controller.Execute(_command);

        Assert.That(result, Is.InstanceOf<OkResult>());
        Assert.That(_registry.FindAvailable("AGV"), Is.Not.Null);
    }

    private class StubHttpMessageHandler : HttpMessageHandler
    {
        private readonly HttpStatusCode _statusCode;

        public StubHttpMessageHandler(HttpStatusCode statusCode) => _statusCode = statusCode;

        protected override Task<HttpResponseMessage> SendAsync(
            HttpRequestMessage request, CancellationToken cancellationToken) =>
            Task.FromResult(new HttpResponseMessage(_statusCode));
    }
}
