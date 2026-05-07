<?php

use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\BatchController;
use Illuminate\Support\Facades\Route;

Route::get('/component/commands', [ComppnentController::class, 'getCommands']);

Route::post('batch/execute', [BatchController::class, 'execute']);

Route::put('/component/markfree/{id}', [ComponentController::class, 'markAsFree']);
