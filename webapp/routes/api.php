<?php

use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\BatchController;
use Illuminate\Support\Facades\Route;

Route::get('/component/commands', [ComponentController::class, 'getCommands']);

Route::post('/batch/execute', [BatchController::class, 'execute']);
