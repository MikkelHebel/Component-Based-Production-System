<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class BatchController extends Controller
{

    public function execute(Request $request) {
        $steps = $request->input('steps');

        // maybe better way?
        $response = Http:post('http://localhost:5159/api/batch/execute', [
            'batch' => $steps]);
        ]
        // unsure if it works this way

        return response()->json([
            'status' => 'dispatched',
            'whisperer_response' => $response->json()
        ]);
    }
}
