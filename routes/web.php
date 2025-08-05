<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestantes (Patients)
    Route::resource('patients', PatientController::class);
    Route::get('/patients/{patient}/history', [PatientController::class, 'history'])->name('patients.history');
    
    // Consultas
    Route::resource('consultations', ConsultationController::class);
    Route::get('/consultations/create/{patient?}', [ConsultationController::class, 'create'])->name('consultations.create');
    
    // Exames
    Route::resource('exams', ExamController::class);
    Route::get('/consultations/{consultation}/exams', [ExamController::class, 'byConsultation'])->name('exams.by-consultation');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
