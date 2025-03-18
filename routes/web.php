<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\PresenceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/etudiants/create', [EtudiantController::class, 'create'])->name('etudiants.create');
Route::post('/etudiants', [EtudiantController::class, 'store'])->name('etudiants.store');

Route::get('/cours/create', [CoursController::class, 'create'])->name('cours.create');
Route::post('/cours', [CoursController::class, 'store'])->name('cours.store');

Route::get('/presences/create', [PresenceController::class, 'create'])->name('presences.create');
Route::post('/presences/select-course', [PresenceController::class, 'selectCourse'])->name('presences.selectCourse');
Route::post('/presences/store', [PresenceController::class, 'storePresence'])->name('presences.storePresence');
Route::get('/presences/reset-selection', [PresenceController::class, 'resetSelection'])->name('presences.resetSelection');
Route::post('/presences/apply-filter', [PresenceController::class, 'applyFilter'])->name('presences.applyFilter');