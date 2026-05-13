# Component Documentation

## AGV
**Protocol:** REST | **Host:** `st4-agv` | **Port:** 80

| Command | Parameters | Example |
|---|---|---|
| `MoveToChargerOperation` | — | — |
| `MoveToAssemblyOperation` | — | — |
| `MoveToStorageOperation` | — | — |
| `PutAssemblyOperation` | — | — |
| `PickAssemblyOperation` | — | — |
| `PickWarehouseOperation` | — | — |
| `PutWarehouseOperation` | — | — |

## Warehouse
**Protocol:** SOAP | **Host:** `st4-warehouse` | **Port:** 80

| Command | Parameters | Example |
|---|---|---|
| `PickItem` | `trayId` | `trayId: 1` |
| `InsertItem` | `trayId`, `name` | `trayId: 1, name: "PartA"` |
| `GetInventory` | — | — |

## Assembly Station
**Protocol:** MQTT | **Host:** `mqtt` | **Port:** 1883

| Command | Parameters | Example |
|---|---|---|
| `StartProcess` | `processId` | `processId: 3` |
