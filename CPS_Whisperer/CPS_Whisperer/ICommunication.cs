using System;

interface ICommunication{
    public MachineState State { get; set; }

    async Task Command(string cmd){}
    //async Task Command(T cmd); //object or something instead of string??
    string Status();
}
