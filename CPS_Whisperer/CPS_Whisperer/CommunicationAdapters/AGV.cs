using System.Reflection;
class AGV : ICommunication {
  private HttpClient client;
  private static readonly Lazy<AGV> agv_instance =
    new Lazy<AGV>(() => new AGV());

  private AGV(){ // singleton 
    client = new HttpClient();
    client.BaseAddress = new Uri("http://localhost:8082/");
    client.DefaultRequestHeaders.Add("Accept", "application/json");
  }
  
  public static AGV Instance { get { return agv_instance.Value; } } // get instance of singleton
  //public int battery; //eh?
  public MachineState state {get; set;}
  public int Command(string cmd) {
      MethodInfo mi = this.GetType().GetMethod(cmd); //substring for parameter(s)?
      mi.Invoke(this, null); //Invoke the method (null means no parameter for the method call, or you can pass array of parameters)
      return 0; //return value?
  }

  public string Status()
  {
    return "bleh";
  }

  public async Task<string> GetStatus() {
    using HttpResponseMessage response = await client.GetAsync("v1/status");
    response.EnsureSuccessStatusCode();

    return await response.Content.ReadAsStringAsync();
  }

  // state = 2 to load, 3 to execute on actual agv
  public async Task<string> Execute(string commandName, int state)
  {
    var body = new Dictionary<string, object>
    {
      {"Program name", commandName},
      {"state", state}
    };

    using HttpResponseMessage response = await client.PutAsJsonAsync("v1/status", body);
    response.EnsureSuccessStatusCode();

    return await response.Content.ReadAsStringAsync();
  }
}