<?php

namespace App\Providers;

use App\Http\ViewComposers\AlertBadgeComposer;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\Vaccine;
use App\Observers\ConsultationObserver;
use App\Observers\ExamObserver;
use App\Observers\PatientObserver;
use App\Observers\VaccineObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Patient::observe(PatientObserver::class);
        Consultation::observe(ConsultationObserver::class);
        Exam::observe(ExamObserver::class);
        Vaccine::observe(VaccineObserver::class);

        View::composer('*', AlertBadgeComposer::class);
    }
}
