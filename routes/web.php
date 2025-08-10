<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\VaccineController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\HomeVisitController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Usuários
    Route::resource('users', UserController::class);
    Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');
    Route::get('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Gestantes (Patients) - Rotas expandidas
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('index');
        Route::get('/create', [PatientController::class, 'create'])->name('create');
        Route::post('/', [PatientController::class, 'store'])->name('store');
        Route::get('/{patient}', [PatientController::class, 'show'])->name('show');
        Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
        Route::patch('/{patient}', [PatientController::class, 'update'])->name('update');
        Route::delete('/{patient}', [PatientController::class, 'destroy'])->name('destroy');
        Route::get('/{patient}/history', [PatientController::class, 'history'])->name('history');
        
        // Nova rota para pesquisa AJAX
        Route::get('/search/ajax', [PatientController::class, 'search'])->name('search');
    });
    
    // Consultas
    Route::prefix('consultations')->name('consultations.')->group(function () {
        Route::get('/', [ConsultationController::class, 'index'])->name('index');
        Route::get('/create/{patient?}', [ConsultationController::class, 'create'])->name('create');
        Route::post('/', [ConsultationController::class, 'store'])->name('store');
        Route::get('/{consultation}', [ConsultationController::class, 'show'])->name('show');
        Route::get('/{consultation}/edit', [ConsultationController::class, 'edit'])->name('edit');
        Route::patch('/{consultation}', [ConsultationController::class, 'update'])->name('update');
        Route::delete('/{consultation}', [ConsultationController::class, 'destroy'])->name('destroy');
        
        // Ações específicas
        Route::patch('/{consultation}/confirm', [ConsultationController::class, 'confirm'])->name('confirm');
        Route::patch('/{consultation}/complete', [ConsultationController::class, 'complete'])->name('complete');
        Route::get('/patient/{patient}', [ConsultationController::class, 'byPatient'])->name('by-patient');
    });
    
    // Exames - Rotas expandidas
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
        Route::patch('/{exam}', [ExamController::class, 'update'])->name('update');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');

        // Rotas especiais
        Route::get('/pending-results', [ExamController::class, 'pendingResults'])->name('pending-results');
        Route::get('/report', [ExamController::class, 'generateReport'])->name('report');
        Route::get('/create/{consultation}', [ExamController::class, 'create'])->name('create.consultation');
        
        // Rotas de resultado
        Route::get('/{exam}/result', [ExamController::class, 'resultForm'])->name('result-form');
        Route::post('/{exam}/result', [ExamController::class, 'storeResult'])->name('store-result');
        
        // Rotas de consulta
        Route::get('/consultation/{consultation}', [ExamController::class, 'byConsultation'])->name('by-consultation');
        
        // Rotas de ação específica
        Route::patch('/{exam}/complete', [ExamController::class, 'markAsCompleted'])->name('mark-as-completed');
        Route::get('/attachments/{attachment}/download', [ExamController::class, 'downloadAttachment'])->name('attachment.download');
    });

    // Vacinas & IPTp - NOVO
    Route::prefix('vaccines')->name('vaccines.')->group(function () {
        Route::get('/', [VaccineController::class, 'index'])->name('index');
        Route::get('/create', [VaccineController::class, 'create'])->name('create');
        Route::post('/', [VaccineController::class, 'store'])->name('store');
        Route::get('/{vaccine}', [VaccineController::class, 'show'])->name('show');
        Route::get('/{vaccine}/edit', [VaccineController::class, 'edit'])->name('edit');
        Route::patch('/{vaccine}', [VaccineController::class, 'update'])->name('update');
        Route::delete('/{vaccine}', [VaccineController::class, 'destroy'])->name('destroy');
        
        // Rotas específicas para vacinas
        Route::get('/patient/{patient}', [VaccineController::class, 'byPatient'])->name('by-patient');
        Route::get('/alerts/pending', [VaccineController::class, 'pendingAlert'])->name('pending-alert');
        Route::patch('/{vaccine}/administer', [VaccineController::class, 'markAsAdministered'])->name('mark-as-administered');
        Route::patch('/{vaccine}/reschedule', [VaccineController::class, 'reschedule'])->name('reschedule');
        Route::get('/reports/coverage', [VaccineController::class, 'generateReport'])->name('coverage-report');
    });

    // Laboratório - NOVO
    Route::prefix('laboratory')->name('laboratory.')->group(function () {
        Route::get('/', [LaboratoryController::class, 'index'])->name('index');
        Route::get('/pending-queue', [LaboratoryController::class, 'pendingQueue'])->name('pending-queue');
        Route::post('/exams/{exam}/process', [LaboratoryController::class, 'processExam'])->name('process-exam');
        Route::post('/exams/bulk-process', [LaboratoryController::class, 'bulkProcess'])->name('bulk-process');
        Route::get('/workload', [LaboratoryController::class, 'workload'])->name('workload');
        Route::get('/quality-control', [LaboratoryController::class, 'qualityControl'])->name('quality-control');
        Route::get('/reports/daily', [LaboratoryController::class, 'generateDailyReport'])->name('daily-report');
        Route::get('/export/results', [LaboratoryController::class, 'exportResults'])->name('export-results');
        Route::get('/alerts/critical', [LaboratoryController::class, 'criticalAlerts'])->name('critical-alerts');
        Route::get('/api/statistics', [LaboratoryController::class, 'statisticsAPI'])->name('statistics-api');
    });

    // Visitas Domiciliárias - NOVO
    Route::prefix('home-visits')->name('home_visits.')->group(function () {
        Route::get('/', [HomeVisitController::class, 'index'])->name('index');
        Route::get('/create', [HomeVisitController::class, 'create'])->name('create');
        Route::post('/', [HomeVisitController::class, 'store'])->name('store');
        Route::get('/{homeVisit}/edit', [HomeVisitController::class, 'edit'])->name('edit');
        Route::patch('/{homeVisit}', [HomeVisitController::class, 'update'])->name('update');
        Route::delete('/{homeVisit}', [HomeVisitController::class, 'destroy'])->name('destroy');
        
        // Rotas específicas para visitas
        Route::get('/schedule/daily', [HomeVisitController::class, 'dailySchedule'])->name('daily-schedule');
        Route::get('/schedule/weekly', [HomeVisitController::class, 'weeklySchedule'])->name('weekly-schedule');
        Route::post('/{homeVisit}/complete', [HomeVisitController::class, 'complete'])->name('complete');
        Route::patch('/{homeVisit}/reschedule', [HomeVisitController::class, 'reschedule'])->name('reschedule');
        Route::patch('/{homeVisit}/not-found', [HomeVisitController::class, 'markAsNotFound'])->name('mark-as-not-found');
        Route::get('/patient/{patient}', [HomeVisitController::class, 'byPatient'])->name('by-patient');
        Route::get('/route-planning', [HomeVisitController::class, 'routePlanning'])->name('route-planning');
        Route::get('/reports/generate', [HomeVisitController::class, 'generateReport'])->name('generate-report');
        
        // Busca ativa
        Route::get('/active-search', [HomeVisitController::class, 'activeSearch'])->name('active-search');
        Route::post('/active-search/schedule', [HomeVisitController::class, 'scheduleActiveSearch'])->name('schedule-active-search');
        
        // API para mobile
        Route::get('/mobile/sync', [HomeVisitController::class, 'mobileSync'])->name('mobile-sync');
    });

    // Relatórios MISAU - NOVO
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        
        // Relatórios pré-natais
        Route::get('/prenatal/monthly', [ReportController::class, 'prenatalMonthly'])->name('prenatal-monthly');
        Route::get('/prenatal/quarterly', [ReportController::class, 'prenatalQuarterly'])->name('prenatal-quarterly');
        Route::get('/prenatal/annual', [ReportController::class, 'prenatalAnnual'])->name('prenatal-annual');
        
        // Relatórios de vacinas
        Route::get('/vaccines/coverage', [ReportController::class, 'vaccineCoverage'])->name('vaccine-coverage');
        Route::get('/vaccines/iptp', [ReportController::class, 'iptpReport'])->name('iptp-report');
        
        // Relatórios de laboratório
        Route::get('/laboratory/production', [ReportController::class, 'laboratoryProduction'])->name('laboratory-production');
        Route::get('/laboratory/quality', [ReportController::class, 'laboratoryQuality'])->name('laboratory-quality');
        
        // Relatórios de visitas domiciliárias
        Route::get('/home-visits/activity', [ReportController::class, 'homeVisitsActivity'])->name('home-visits-activity');
        Route::get('/home-visits/coverage', [ReportController::class, 'homeVisitsCoverage'])->name('home-visits-coverage');
        
        // Indicadores MISAU
        Route::get('/indicators/maternal', [ReportController::class, 'maternalIndicators'])->name('maternal-indicators');
        Route::get('/indicators/anc', [ReportController::class, 'ancIndicators'])->name('anc-indicators');
        
        // Exportações
        Route::post('/export/excel', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::post('/export/pdf', [ReportController::class, 'exportPDF'])->name('export-pdf');
        Route::post('/export/csv', [ReportController::class, 'exportCSV'])->name('export-csv');
        
        // Dashboard executivo
        Route::get('/dashboard/executive', [ReportController::class, 'executiveDashboard'])->name('executive-dashboard');
        Route::get('/api/dashboard-data', [ReportController::class, 'dashboardDataAPI'])->name('dashboard-data-api');
    });

    // Configurações do Sistema - NOVO
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::patch('/general', [SettingsController::class, 'updateGeneral'])->name('update-general');
        Route::patch('/notifications', [SettingsController::class, 'updateNotifications'])->name('update-notifications');
        Route::patch('/backup', [SettingsController::class, 'backupSettings'])->name('backup');
        Route::get('/system-info', [SettingsController::class, 'systemInfo'])->name('system-info');
        Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
    });

    // Sistema de Ajuda - NOVO
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [HelpController::class, 'index'])->name('index');
        Route::get('/manual', [HelpController::class, 'manual'])->name('manual');
        Route::get('/faq', [HelpController::class, 'faq'])->name('faq');
        Route::get('/videos', [HelpController::class, 'videos'])->name('videos');
        Route::get('/contact', [HelpController::class, 'contact'])->name('contact');
        Route::post('/feedback', [HelpController::class, 'submitFeedback'])->name('submit-feedback');
    });

    // APIs internas para dashboards e gráficos
    Route::prefix('api/internal')->name('api.')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard-stats');
        Route::get('/patients/stats', [PatientController::class, 'getPatientStats'])->name('patient-stats');
        Route::get('/consultations/calendar', [ConsultationController::class, 'getCalendarData'])->name('consultation-calendar');
        Route::get('/exams/pending-alerts', [ExamController::class, 'getPendingAlerts'])->name('exam-alerts');
        Route::get('/vaccines/alerts', [VaccineController::class, 'getVaccineAlerts'])->name('vaccine-alerts');
        Route::get('/visits/map-data', [HomeVisitController::class, 'getMapData'])->name('visits-map-data');
    });

    // Notificações do sistema
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/api/count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Integração com sistemas externos (futuro)
    Route::prefix('integrations')->name('integrations.')->group(function () {
        Route::get('/sisma', [IntegrationController::class, 'sismaSync'])->name('sisma-sync');
        Route::get('/dhis2', [IntegrationController::class, 'dhis2Export'])->name('dhis2-export');
        Route::post('/webhook/laboratory', [IntegrationController::class, 'laboratoryWebhook'])->name('laboratory-webhook');
    });

    // Auditoria e logs (para administradores)
    Route::prefix('audit')->middleware('role:Administrador')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/user-activities', [AuditController::class, 'userActivities'])->name('user-activities');
        Route::get('/system-logs', [AuditController::class, 'systemLogs'])->name('system-logs');
        Route::get('/data-changes', [AuditController::class, 'dataChanges'])->name('data-changes');
        Route::post('/export', [AuditController::class, 'exportLogs'])->name('export-logs');
    });

    // Backup e restauração (apenas para administradores)
    Route::prefix('backup')->middleware('role:Administrador')->name('backup.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/{backup}/download', [BackupController::class, 'download'])->name('download');
        Route::delete('/{backup}', [BackupController::class, 'destroy'])->name('destroy');
        Route::post('/{backup}/restore', [BackupController::class, 'restore'])->name('restore');
    });
});

// Rotas de perfil do usuário
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});
//rotas de usuarios
Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');
Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
Route::get('/users/template', [UserController::class, 'template'])->name('users.template');
Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
Route::post('/users/bulk-activate', [UserController::class, 'bulkActivate']);
Route::post('/users/bulk-deactivate', [UserController::class, 'bulkDeactivate']);
Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete']);

// Rotas públicas (sem autenticação) - para emergências ou informações públicas
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/health-tips', [PublicController::class, 'healthTips'])->name('health-tips');
    Route::get('/emergency-contacts', [PublicController::class, 'emergencyContacts'])->name('emergency-contacts');
    Route::get('/hospitals', [PublicController::class, 'hospitals'])->name('hospitals');
    Route::get('/maternal-health-info', [PublicController::class, 'maternalHealthInfo'])->name('maternal-health-info');
});

// WebSocket para notificações em tempo real (se implementado)
if (config('broadcasting.default') !== 'null') {
    Route::middleware('auth')->group(function () {
        Route::get('/broadcasting/auth', function () {
            return auth()->user();
        });
    });
}

// Rotas de desenvolvimento (apenas em ambiente local)
if (app()->environment('local')) {
    Route::prefix('dev')->name('dev.')->group(function () {
        Route::get('/test-notifications', [DevController::class, 'testNotifications'])->name('test-notifications');
        Route::get('/generate-test-data', [DevController::class, 'generateTestData'])->name('generate-test-data');
        Route::get('/clear-all-cache', [DevController::class, 'clearAllCache'])->name('clear-all-cache');
        Route::get('/phpinfo', function () {
            return phpinfo();
        })->name('phpinfo');
    });
}

require __DIR__.'/auth.php';