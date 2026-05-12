<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function getCommands() {

        $response = Http:get('http://localhost:5001/api/components/commands');

        if ($response->successful()) {
            return $response->json();
        }
        return response()->json(['error', => 'Orchestrator failed.'], 500);
    }
}
