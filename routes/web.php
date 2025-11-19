<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ActivityController;
use App\Http\Controllers\Web\CompanyController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\DealController;
use App\Http\Controllers\Web\LeadController;
use App\Http\Controllers\Web\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRM Routes
    Route::resource('leads', LeadController::class)->only(['index', 'create', 'show', 'edit']);
    Route::resource('contacts', ContactController::class)->only(['index', 'create', 'show', 'edit']);
    Route::resource('companies', CompanyController::class)->only(['index', 'create', 'show', 'edit']);
    Route::resource('deals', DealController::class)->only(['index', 'create', 'show', 'edit']);
    Route::resource('tasks', TaskController::class)->only(['index', 'create', 'show', 'edit']);
    Route::resource('activities', ActivityController::class)->only(['index', 'create', 'show', 'edit']);
});

require __DIR__.'/auth.php';
