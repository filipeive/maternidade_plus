<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaboratoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['consultation.patient', 'consultation.user']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo_exame')) {
            $query->where('tipo_exame', $request->tipo_exame);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_solicitacao', [
                $request->data_inicio,
                $request->data_fim
            ]);
        }

        if ($request->filled('patient_search')) {
            $query->whereHas('consultation.patient', function($q) use ($request) {
                $q->where('nome_completo', 'LIKE', '%' . $request->patient_search . '%');
            });
        }

        // Ordenação específica para laboratório
        $orderBy = $request->get('order_by', 'data_solicitacao');
        $orderDirection = $request->get('order_direction', 'desc');
        
        if ($orderBy === 'urgencia') {
            // Priorizar exames urgentes
            $query->orderByRaw("
                CASE 
                    WHEN tipo_exame IN ('teste_hiv', 'teste_sifilis', 'glicemia_jejum') THEN 1
                    WHEN status = 'pendente' AND DATEDIFF(NOW(), data_solicitacao) > 7 THEN 2
                    ELSE 3
                END
            ");
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $exams = $query->paginate(20);

        // Estatísticas do laboratório
        $stats = [
            'exames_pendentes' => Exam::where('status', 'pendente')->count(),
            'exames_realizados_hoje' => Exam::where('status', 'realizado')
                                           ->whereDate('data_realizacao', today())
                                           ->count(),
            'exames_atrasados' => Exam::where('status', 'pendente')
                                     ->where('data_solicitacao', '<', now()->subDays(7))
                                     ->count(),
            'total_este_mes' => Exam::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count()
        ];

        // Tipos de exame mais solicitados
        $tiposExamePopulares = Exam::selectRaw('tipo_exame, COUNT(*) as total')
                                  ->whereMonth('created_at', now()->month)
                                  ->groupBy('tipo_exame')
                                  ->orderBy('total', 'desc')
                                  ->limit(5)
                                  ->get();

        return view('laboratory.index', compact('exams', 'stats', 'tiposExamePopulares'));
    }

    public function pendingQueue()
    {
        $examsPendentes = Exam::where('status', 'pendente')
                             ->with(['consultation.patient', 'consultation.user'])
                             ->orderByRaw("
                                 CASE 
                                     WHEN tipo_exame IN ('teste_hiv', 'teste_sifilis') THEN 1
                                     WHEN DATEDIFF(NOW(), data_solicitacao) > 7 THEN 2
                                     ELSE 3
                                 END
                             ")
                             ->orderBy('data_solicitacao')
                             ->paginate(15);

        return view('laboratory.pending-queue', compact('examsPendentes'));
    }

    public function processExam(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'data_realizacao' => 'required|date|before_or_equal:today',
            'resultado' => 'required|string',
            'observacoes' => 'nullable|string',
            'valores_referencia' => 'nullable|string',
            'interpretacao' => 'nullable|string',
            'recomendacoes' => 'nullable|string'
        ]);

        $exam->update([
            'data_realizacao' => $validated['data_realizacao'],
            'resultado' => $validated['resultado'],
            'observacoes' => $validated['observacoes'] ?? $exam->observacoes,
            'status' => 'realizado'
        ]);

        // Log da atividade
        activity()
            ->performedOn($exam)
            ->causedBy(auth()->user())
            ->log('Resultado do exame processado no laboratório');

        return redirect()->route('laboratory.index')
                        ->with('success', 'Resultado do exame processado com sucesso!');
    }

    public function bulkProcess(Request $request)
    {
        $validated = $request->validate([
            'exam_ids' => 'required|array',
            'exam_ids.*' => 'exists:exams,id',
            'data_realizacao' => 'required|date|before_or_equal:today'
        ]);

        $processedCount = 0;

        foreach ($validated['exam_ids'] as $examId) {
            $exam = Exam::find($examId);
            if ($exam && $exam->status === 'pendente') {
                $exam->update([
                    'data_realizacao' => $validated['data_realizacao'],
                    'status' => 'realizado'
                ]);
                $processedCount++;
            }
        }

        return redirect()->back()
                        ->with('success', "Processados {$processedCount} exames com sucesso!");
    }

    public function workload()
    {
        $today = now();
        
        // Carga de trabalho por dia da semana
        $workloadByDay = Exam::selectRaw('DAYOFWEEK(data_solicitacao) as day, COUNT(*) as total')
                            ->whereMonth('data_solicitacao', $today->month)
                            ->whereYear('data_solicitacao', $today->year)
                            ->groupBy('day')
                            ->orderBy('day')
                            ->get()
                            ->mapWithKeys(function($item) {
                                $days = ['', 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
                                return [$days[$item->day] => $item->total];
                            });

        // Tempo médio de processamento
        $avgProcessingTime = Exam::whereNotNull('data_realizacao')
                                ->whereMonth('created_at', $today->month)
                                ->selectRaw('AVG(DATEDIFF(data_realizacao, data_solicitacao)) as avg_days')
                                ->value('avg_days');

        // Exames por tipo no mês
        $examsByType = Exam::selectRaw('tipo_exame, COUNT(*) as total, 
                                      SUM(CASE WHEN status = "realizado" THEN 1 ELSE 0 END) as realizados')
                          ->whereMonth('created_at', $today->month)
                          ->groupBy('tipo_exame')
                          ->orderBy('total', 'desc')
                          ->get();

        return view('laboratory.workload', compact('workloadByDay', 'avgProcessingTime', 'examsByType'));
    }

    public function qualityControl()
    {
        $today = now();
        
        // Exames com resultados alterados
        $alteredResults = Exam::where('status', 'realizado')
                             ->whereMonth('data_realizacao', $today->month)
                             ->where(function($query) {
                                 $query->where('resultado', 'LIKE', '%positivo%')
                                       ->orWhere('resultado', 'LIKE', '%alterado%')
                                       ->orWhere('resultado', 'LIKE', '%anormal%');
                             })
                             ->with('consultation.patient')
                             ->orderBy('data_realizacao', 'desc')
                             ->paginate(20);

        // Indicadores de qualidade
        $qualityMetrics = [
            'taxa_reprocessamento' => $this->calculateReprocessingRate(),
            'tempo_medio_entrega' => round($this->calculateAverageDeliveryTime(), 1),
            'exames_criticos' => $this->countCriticalResults(),
            'satisfacao_cliente' => 95.2 // Simulado - poderia vir de pesquisas
        ];

        return view('laboratory.quality-control', compact('alteredResults', 'qualityMetrics'));
    }

    public function generateDailyReport(Request $request)
    {
        $date = $request->get('date', today());
        $date = Carbon::parse($date);

        $report = [
            'data' => $date,
            'exames_solicitados' => Exam::whereDate('data_solicitacao', $date)->count(),
            'exames_realizados' => Exam::whereDate('data_realizacao', $date)->count(),
            'exames_pendentes' => Exam::where('status', 'pendente')
                                     ->whereDate('data_solicitacao', '<=', $date)
                                     ->count(),
            'por_tipo' => Exam::whereDate('data_solicitacao', $date)
                             ->selectRaw('tipo_exame, COUNT(*) as total')
                             ->groupBy('tipo_exame')
                             ->get(),
            'resultados_alterados' => Exam::whereDate('data_realizacao', $date)
                                         ->where('status', 'realizado')
                                         ->where(function($query) {
                                             $query->where('resultado', 'LIKE', '%positivo%')
                                                   ->orWhere('resultado', 'LIKE', '%alterado%');
                                         })
                                         ->with('consultation.patient')
                                         ->get()
        ];

        return view('laboratory.daily-report', compact('report'));
    }

    public function exportResults(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $exams = Exam::whereBetween('data_realizacao', [$startDate, $endDate])
                    ->where('status', 'realizado')
                    ->with(['consultation.patient'])
                    ->orderBy('data_realizacao')
                    ->get();

        // Aqui você implementaria a exportação (Excel, PDF, etc.)
        // Por agora, retornamos uma view
        return view('laboratory.export-results', compact('exams', 'startDate', 'endDate'));
    }

    public function criticalAlerts()
    {
        // Exames críticos que precisam de atenção imediata
        $criticalExams = Exam::where('status', 'realizado')
                            ->where(function($query) {
                                $query->where('resultado', 'LIKE', '%HIV positivo%')
                                      ->orWhere('resultado', 'LIKE', '%Sífilis positivo%')
                                      ->orWhere('resultado', 'LIKE', '%Diabetes%')
                                      ->orWhere('resultado', 'LIKE', '%Anemia grave%');
                            })
                            ->whereDate('data_realizacao', '>=', now()->subDays(7))
                            ->with('consultation.patient')
                            ->orderBy('data_realizacao', 'desc')
                            ->get();

        return view('laboratory.critical-alerts', compact('criticalExams'));
    }

    public function statisticsAPI(Request $request)
    {
        $period = $request->get('period', '7days');
        
        switch ($period) {
            case '7days':
                $startDate = now()->subDays(7);
                break;
            case '30days':
                $startDate = now()->subDays(30);
                break;
            case '3months':
                $startDate = now()->subMonths(3);
                break;
            default:
                $startDate = now()->subDays(7);
        }

        $stats = [
            'exames_por_dia' => Exam::whereBetween('data_solicitacao', [$startDate, now()])
                                   ->selectRaw('DATE(data_solicitacao) as date, COUNT(*) as total')
                                   ->groupBy('date')
                                   ->orderBy('date')
                                   ->get(),
            'tipos_mais_solicitados' => Exam::whereBetween('data_solicitacao', [$startDate, now()])
                                           ->selectRaw('tipo_exame, COUNT(*) as total')
                                           ->groupBy('tipo_exame')
                                           ->orderBy('total', 'desc')
                                           ->limit(5)
                                           ->get(),
            'performance_tempo' => [
                'media_processamento' => $this->calculateAverageDeliveryTime($startDate),
                'exames_no_prazo' => $this->countOnTimeDeliveries($startDate),
                'exames_atrasados' => $this->countLateDeliveries($startDate)
            ]
        ];

        return response()->json($stats);
    }

    // Métodos auxiliares privados
    private function calculateReprocessingRate()
    {
        $totalExams = Exam::whereMonth('created_at', now()->month)->count();
        $reprocessed = Exam::whereMonth('created_at', now()->month)
                          ->where('observacoes', 'LIKE', '%reprocessado%')
                          ->count();
        
        return $totalExams > 0 ? round(($reprocessed / $totalExams) * 100, 2) : 0;
    }

    private function calculateAverageDeliveryTime($startDate = null)
    {
        $query = Exam::whereNotNull('data_realizacao');
        
        if ($startDate) {
            $query->where('data_solicitacao', '>=', $startDate);
        } else {
            $query->whereMonth('data_solicitacao', now()->month);
        }
        
        return $query->selectRaw('AVG(DATEDIFF(data_realizacao, data_solicitacao)) as avg_days')
                    ->value('avg_days') ?? 0;
    }

    private function countCriticalResults()
    {
        return Exam::where('status', 'realizado')
                  ->whereMonth('data_realizacao', now()->month)
                  ->where(function($query) {
                      $query->where('resultado', 'LIKE', '%positivo%')
                            ->orWhere('resultado', 'LIKE', '%crítico%')
                            ->orWhere('resultado', 'LIKE', '%urgente%');
                  })
                  ->count();
    }

    private function countOnTimeDeliveries($startDate)
    {
        return Exam::whereBetween('data_solicitacao', [$startDate, now()])
                  ->whereNotNull('data_realizacao')
                  ->whereRaw('DATEDIFF(data_realizacao, data_solicitacao) <= 3')
                  ->count();
    }

    private function countLateDeliveries($startDate)
    {
        return Exam::whereBetween('data_solicitacao', [$startDate, now()])
                  ->whereNotNull('data_realizacao')
                  ->whereRaw('DATEDIFF(data_realizacao, data_solicitacao) > 3')
                  ->count();
    }
}