<?php

use App\Http\Controllers\UserController;
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
    //users
    Route::resource('users', UserController::class);
    Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');
    Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Gestantes (Patients)
    Route::resource('patients', PatientController::class);
    Route::get('/patients/{patient}/history', [PatientController::class, 'history'])->name('patients.history');
    
    // Consultas
    Route::get('/consultations/create/{patient?}', [ConsultationController::class, 'create'])->name('consultations.create');
    Route::patch('/consultations/{consultation}/confirm', [ConsultationController::class, 'confirm'])->name('consultations.confirm');
    Route::patch('/consultations/{consultation}/complete', [ConsultationController::class, 'complete'])->name('consultations.complete');
    Route::get('/patients/{patient}/consultations', [ConsultationController::class, 'byPatient'])->name('consultations.by-patient');
    Route::resource('consultations', ConsultationController::class);
    
    // Rotas de Exames
    Route::prefix('exams')->name('exams.')->group(function () {
    // Rotas CRUD básicas
    Route::get('/', [ExamController::class, 'index'])->name('index');
    Route::get('/create', [ExamController::class, 'create'])->name('create');
    Route::post('/', [ExamController::class, 'store'])->name('store');
    Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
    Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
    Route::patch('/{exam}', [ExamController::class, 'update'])->name('update');
    Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');

    // Rotas especiais
    Route::get('pending-results', [ExamController::class, 'pendingResults'])->name('pending-results');
    Route::get('report', [ExamController::class, 'generateReport'])->name('report');
    Route::get('create/{consultation}', [ExamController::class, 'create'])->name('create.consultation');
    
    // Rotas de resultado
    Route::get('{exam}/result', [ExamController::class, 'resultForm'])->name('result-form');
    Route::post('{exam}/result', [ExamController::class, 'storeResult'])->name('store-result');
    
    // Rotas de consulta
    Route::get('consultation/{consultation}', [ExamController::class, 'byConsultation'])->name('by-consultation');
    
    // Rotas de ação específica
    Route::patch('{exam}/complete', [ExamController::class, 'markAsCompleted'])->name('mark-as-completed');

    Route::get('attachments/{attachment}/download', [ExamController::class, 'downloadAttachment'])
    ->name('attachment.download');
});

    // Resource routes (principais CRUD)
    Route::resource('exams', ExamController::class)->except(['create']);

    // Rota relacionada às consultas
    Route::get('consultations/{consultation}/exams', [ExamController::class, 'byConsultation'])->name('exams.by-consultation');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
