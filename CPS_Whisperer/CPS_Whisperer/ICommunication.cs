interface ICommunication{
    MachineState State {get; set;} //can't be private

    async Task Command(string cmd){}
    //async Task Command(T cmd); //object or something instead of string??
    string Status();
}