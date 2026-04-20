interface ICommunication{
    public MachineState state {get; set;} //can't be private

    public int Command(string cmd);
    public string Status();
}