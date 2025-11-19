<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/activities/recent', [ActivityController::class, 'recent']);

    // CRM Resources
    Route::apiResource('leads', LeadController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('deals', DealController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('activities', ActivityController::class);

    // Custom actions
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert']);
    Route::patch('deals/{deal}/stage', [DealController::class, 'updateStage']);
    Route::patch('tasks/{task}/complete', [TaskController::class, 'complete']);
});
