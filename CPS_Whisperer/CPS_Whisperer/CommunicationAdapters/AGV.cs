using System;
using Newtonsoft.Json.Linq;

class AGV : ICommunication {
  private string _jsonString;
  public int? battery; //eh?
  private HttpClient _client;
  private static readonly Lazy<AGV> agv_instance = new Lazy<AGV>(() => new AGV());

  private AGV(){ // singleton 
    _client = new HttpClient();
    _client.BaseAddress = new Uri("http://localhost:8082/");
    _client.DefaultRequestHeaders.Add("Accept", "application/json");
  }
  
  public static AGV Instance { get { return agv_instance.Value; } } // get instance of singleton
  
  public MachineState State {get; set;}

  public async Task Command(string cmd){
    string p = cmd.Substring(0, cmd.IndexOf("."));
    int s = Convert.ToInt32(cmd.Substring(cmd.IndexOf(".")+1));
    await Execute(p,s);

    Console.WriteLine(p + s); //for purpose of showing output, delete
  }

  public string Status() {
    UpdateState();
    UpdateBattery(); //??

    
    try {
      var obj = JObject.Parse(_jsonString);
      int s = Convert.ToInt32(obj.GetValue("state"));

      switch (s) {
                case 0: State = MachineState.Idle; break;
                case 1: State = MachineState.Executing; break;
                case 2: State = MachineState.Charging; break;
                default: break;
            }
            return State.ToString();
        }
        catch (Exception) {
            return "NO STATE";
        }
    
  }

  private async void UpdateState()
  {
    using HttpResponseMessage response = await _client.GetAsync("v1/status");
    response.EnsureSuccessStatusCode();

    _jsonString = await response.Content.ReadAsStringAsync();
  }

  // state = 2 to load, 3 to execute on actual agv
  public async Task<string> Execute(string programName, int state)
  {
    var body = new Dictionary<string, object>
    {
      {"Program name", programName},
      {"state", state}
    };

    using HttpResponseMessage response = await _client.PutAsJsonAsync("v1/status", body);
    response.EnsureSuccessStatusCode();

    return await response.Content.ReadAsStringAsync();
  }

//Needed??
  private int? UpdateBattery(){ 
      try {
        var obj = JObject.Parse(_jsonString);
        battery = Convert.ToInt32(obj.GetValue("battery"));
        return battery;
      }
      catch (Exception){
        battery = null;
        return battery;
      } 
  }

   // public async Task<string> GetStatus() {
  //   using HttpResponseMessage response = await _client.GetAsync("v1/status");
  //   response.EnsureSuccessStatusCode();
  //   return await response.Content.ReadAsStringAsync();
  // }
}
