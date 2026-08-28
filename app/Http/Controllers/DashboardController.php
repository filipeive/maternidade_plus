<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Exam;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function home()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('login');
    }

    public function index()
    {
        // Estatísticas gerais
        $totalGestantes = Patient::where('ativo', true)->count();
        
        $consultasEstaSemana = Consultation::whereBetween('data_consulta', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
        
        $consultasPendentes = Consultation::where('status', 'agendada')->count();
        $examesPendentes = Exam::where('status', 'solicitado')->count();
        
        // Próximas consultas (próximos 7 dias)
        $proximasConsultas = Consultation::with('patient')
            ->whereBetween('data_consulta', [now(), now()->addDays(7)])
            ->orderBy('data_consulta')
            ->limit(10)
            ->get();
        
        // Módulo de Alerta Precoce: Top 15 alertas ativos ordenados por severidade (Alto -> Médio -> Baixo)
        $alertasPrecoces = Alerta::with('patient')
            ->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])
            ->orderByRaw("CASE nivel WHEN 'alto' THEN 1 WHEN 'medio' THEN 2 WHEN 'baixo' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        // Alertas de acompanhamento legado
        $alertas = collect();
        
        // Gestantes sem consulta há mais de 30 dias
        $gestantesSemConsulta = Patient::with('consultations')
            ->where('ativo', true)
            ->get()
            ->filter(function ($patient) {
                $ultimaConsulta = $patient->consultations()->latest('data_consulta')->first();
                return !$ultimaConsulta || $ultimaConsulta->data_consulta->lt(now()->subDays(30));
            });
        
        foreach ($gestantesSemConsulta as $gestante) {
            $alertas->push([
                'gestante' => $gestante->nome_completo,
                'mensagem' => 'Sem consulta há mais de 30 dias',
                'link' => route('patients.show', $gestante)
            ]);
        }
        
        // Gestantes próximas ao parto (< 4 semanas)
        $gestantesProximasParto = Patient::where('ativo', true)
            ->whereNotNull('data_provavel_parto')
            ->where('data_provavel_parto', '<=', now()->addWeeks(4))
            ->where('data_provavel_parto', '>=', now())
            ->get();
        
        foreach ($gestantesProximasParto as $gestante) {
            $diasRestantes = now()->diffInDays($gestante->data_provavel_parto);
            $alertas->push([
                'gestante' => $gestante->nome_completo,
                'mensagem' => "Parto previsto em {$diasRestantes} dias",
                'link' => route('patients.show', $gestante)
            ]);
        }
        
        return view('dashboard', compact(
            'totalGestantes',
            'consultasEstaSemana', 
            'consultasPendentes',
            'examesPendentes',
            'proximasConsultas',
            'alertas',
            'alertasPrecoces'
        ));
    }
}
