<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function getCommands() {

        $response = Http:get('http://localhost:5159/api/batch/execute');

        if ($response->successful()) {
            return $response->json();
        }
        return response()->json(['error', => 'C# whisperer unreachable'], 500);
    }

    public function markAsFree($id) {

        log::info("Machine {$id} is now free");
        return response()->json(['message' => "Component {$id} is now free."]);
    }
}
