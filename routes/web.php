<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\PresenceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/cours/create', [CoursController::class, 'create'])->name('cours.create');
    Route::post('/cours', [CoursController::class, 'store'])->name('cours.store');
    Route::get('/etudiants/create', [EtudiantController::class, 'create'])->name('etudiants.create');
    Route::post('/etudiants', [EtudiantController::class, 'store'])->name('etudiants.store');
    Route::get('/presences', [PresenceController::class, 'create'])->name('presences.create');
    Route::post('/presences/select-course', [PresenceController::class, 'selectCourse'])->name('presences.selectCourse');
    Route::get('/presences/reset-selection', [PresenceController::class, 'resetSelection'])->name('presences.resetSelection');
    Route::post('/presences/filter', [PresenceController::class, 'applyFilter'])->name('presences.applyFilter');
    Route::post('/presences/store-presence', [PresenceController::class, 'storePresence'])->name('presences.storePresence');
});

require __DIR__.'/auth.php';