<?php

namespace App\Http\Controllers;

use App\Models\HomeVisit;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Helpers\VisitTypes;
use Carbon\Carbon;

class HomeVisitController extends Controller
{
    public function index(Request $request)
    {
        $query = HomeVisit::with(['patient', 'user']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo_visita')) {
            $query->where('tipo_visita', $request->tipo_visita);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_visita', [
                $request->data_inicio,
                $request->data_fim
            ]);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('patient_search')) {
            $query->whereHas('patient', function($q) use ($request) {
                $q->where('nome_completo', 'LIKE', '%' . $request->patient_search . '%');
            });
        }

        $visits = $query->orderBy('data_visita', 'desc')->paginate(20);

        // Estatísticas
        $stats = [
            'agendadas_hoje' => HomeVisit::scheduledToday()->count(),
            'realizadas_semana' => HomeVisit::thisWeek()->where('status', 'realizada')->count(),
            'atrasadas' => HomeVisit::overdue()->count(),
            'total_mes' => HomeVisit::whereMonth('data_visita', now()->month)->count()
        ];

        // Usuários para filtro
        $users = \App\Models\User::whereHas('homeVisits')->get(['id', 'name']);

        return view('home_visits.index', compact('visits', 'stats', 'users'));
    }

    public function create(Request $request)
    {
        $patient = null;
        
        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->patient_id);
        }

        $patients = Patient::where('ativo', true)
                          ->orderBy('nome_completo')
                          ->get(['id', 'nome_completo', 'documento_bi', 'endereco', 'contacto']);

        $tiposVisita = HomeVisit::getTiposVisita();

        return view('home_visits.create', compact('patient', 'patients', 'tiposVisita'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'data_visita' => 'required|date|after_or_equal:today',
            'motivo_visita' => 'required|string',
            'tipo_visita' => 'required|in:rotina,pos_parto,alto_risco,faltosa,emergencia,educacao,seguimento',
            'endereco_visita' => 'nullable|string',
            'observacoes_gerais' => 'nullable|string'
        ]);

        // Se não forneceu endereço, usar o endereço da gestante
        if (empty($validated['endereco_visita'])) {
            $patient = Patient::find($validated['patient_id']);
            $validated['endereco_visita'] = $patient->endereco;
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'agendada';

        HomeVisit::create($validated);

        return redirect()->route('home_visits.index')
                        ->with('success', 'Visita domiciliária agendada com sucesso!');
    }

    public function show(HomeVisit $homeVisit)
    {
        $homeVisit->load(['patient', 'user']);
        
        // Buscar outras visitas desta gestante
        $outrasVisitas = HomeVisit::where('patient_id', $homeVisit->patient_id)
                                 ->where('id', '!=', $homeVisit->id)
                                 ->with('user')
                                 ->orderBy('data_visita', 'desc')
                                 ->limit(5)
                                 ->get();

        return view('home_visits.show', compact('homeVisit', 'outrasVisitas'));
    }

    public function edit(HomeVisit $homeVisit)
    {
        if (!$homeVisit->canBeCompleted()) {
            return redirect()->route('home_visits.show', $homeVisit)
                           ->with('error', 'Esta visita não pode ser editada no status atual.');
        }

        $patients = Patient::where('ativo', true)
                          ->orderBy('nome_completo')
                          ->get(['id', 'nome_completo', 'documento_bi']);

        $tiposVisita = HomeVisit::getTiposVisita();

        return view('home_visits.edit', compact('homeVisit', 'patients', 'tiposVisita'));
    }

    public function update(Request $request, HomeVisit $homeVisit)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'data_visita' => 'required|date',
            'motivo_visita' => 'required|string',
            'tipo_visita' => 'required|in:rotina,pos_parto,alto_risco,faltosa,emergencia,educacao,seguimento',
            'endereco_visita' => 'required|string',
            'observacoes_gerais' => 'nullable|string'
        ]);

        $homeVisit->update($validated);

        return redirect()->route('home_visits.show', $homeVisit)
                        ->with('success', 'Visita domiciliária atualizada com sucesso!');
    }

    public function destroy(HomeVisit $homeVisit)
    {
        if ($homeVisit->status === 'realizada') {
            return redirect()->route('home_visits.show', $homeVisit)
                           ->with('error', 'Não é possível excluir uma visita já realizada.');
        }

        $homeVisit->delete();

        return redirect()->route('home_visits.index')
                        ->with('success', 'Visita domiciliária excluída com sucesso!');
    }

    // Métodos específicos para visitas domiciliárias

    public function dailySchedule(Request $request)
    {
        $date = $request->get('date', today());
        $userId = $request->get('user_id', auth()->id());

        $visits = HomeVisit::whereDate('data_visita', $date)
                          ->where('user_id', $userId)
                          ->with('patient')
                          ->orderBy('data_visita')
                          ->get();

        $stats = [
            'total_agendadas' => $visits->where('status', 'agendada')->count(),
            'realizadas' => $visits->where('status', 'realizada')->count(),
            'pendentes' => $visits->where('status', 'agendada')->count(),
            'tempo_estimado' => $visits->count() * 45 // 45 min por visita
        ];

        return view('home_visits.daily-schedule', compact('visits', 'stats', 'date'));
    }

   
    public function complete(Request $request, HomeVisit $homeVisit)
    {
        if (!$homeVisit->canBeCompleted()) {
            return redirect()->back()
                        ->with('error', 'Esta visita não pode ser completada no status atual.');
        }

        $validated = $request->validate([
            'observacoes_ambiente' => 'required|string',
            'condicoes_higiene' => 'required|in:bom,regular,ruim',
            'apoio_familiar' => 'required|in:adequado,parcial,inadequado',
            'estado_nutricional' => 'nullable|string',
            'sinais_vitais' => 'nullable|array',
            'sinais_vitais.pressao_arterial' => 'nullable|string',
            'sinais_vitais.frequencia_cardiaca' => 'nullable|string',
            'sinais_vitais.temperatura' => 'nullable|string',
            'sinais_vitais.peso' => 'nullable|string',
            'queixas_principais' => 'nullable|string',
            'orientacoes_dadas' => 'required|string',
            'materiais_entregues' => 'nullable|array',
            'materiais_entregues.*' => 'nullable|string',
            'proxima_visita' => 'nullable|date|after:today',
            'acompanhante_presente' => 'boolean',
            'necessita_referencia' => 'boolean',
            'coordenadas_gps' => 'nullable|array'
        ]);

        // Processar sinais vitais
        if (isset($validated['sinais_vitais'])) {
            $validated['sinais_vitais'] = array_filter($validated['sinais_vitais'], function($value) {
                return $value !== null && trim($value) !== '';
            });
            
            if (empty($validated['sinais_vitais'])) {
                $validated['sinais_vitais'] = null;
            }
        }

        // Processar materiais entregues
        if (isset($validated['materiais_entregues'])) {
            $validated['materiais_entregues'] = array_values(array_unique($validated['materiais_entregues']));
            
            if (empty($validated['materiais_entregues'])) {
                $validated['materiais_entregues'] = null;
            }
        }

        $validated['status'] = 'realizada';

        $homeVisit->update($validated);

        if ($request->boolean('necessita_referencia')) {
            $this->createReferenceNotification($homeVisit);
        }

        if (!empty($validated['proxima_visita'])) {
            HomeVisit::create([
                'patient_id' => $homeVisit->patient_id,
                'user_id' => auth()->id(),
                'data_visita' => $validated['proxima_visita'],
                'motivo_visita' => 'Seguimento da visita anterior',
                'tipo_visita' => 'seguimento',
                'endereco_visita' => $homeVisit->endereco_visita,
                'status' => 'agendada'
            ]);
        }

        return redirect()->route('home_visits.show', $homeVisit)
                    ->with('success', 'Visita domiciliária completada com sucesso!');
    }

    public function reschedule(Request $request, HomeVisit $homeVisit)
        {
        $validated = $request->validate([
            'nova_data_visita' => 'required|date|after:today',
            'nova_hora_visita' => 'required',
            'motivo_reagendamento' => 'required|string|min:10'
        ]);

        $homeVisit->update([
            'data_visita' => $validated['nova_data_visita'] . ' ' . $validated['nova_hora_visita'],
            'status' => 'reagendada',
            'observacoes_gerais' => trim($homeVisit->observacoes_gerais . 
                "\n\nReagendada: " . $validated['motivo_reagendamento'])
        ]);

        return redirect()->back()
            ->with('success', 'Visita reagendada com sucesso!');
    }


    public function markAsNotFound(HomeVisit $homeVisit)
    {
        $homeVisit->update([
            'status' => 'nao_encontrada',
            'observacoes_gerais' => $homeVisit->observacoes_gerais . 
                                   "\n\nVisita não realizada - Gestante não encontrada no endereço"
        ]);

        // Reagendar automaticamente para nova tentativa
        HomeVisit::create([
            'patient_id' => $homeVisit->patient_id,
            'user_id' => auth()->id(),
            'data_visita' => now()->addDays(3),
            'motivo_visita' => 'Nova tentativa - gestante não encontrada anteriormente',
            'tipo_visita' => $homeVisit->tipo_visita,
            'endereco_visita' => $homeVisit->endereco_visita,
            'status' => 'agendada'
        ]);

        return redirect()->back()
                        ->with('success', 'Marcada como não encontrada. Nova visita agendada automaticamente.');
    }

    public function byPatient(Patient $patient)
    {
        $visits = $patient->homeVisits()
                         ->with('user')
                         ->orderBy('data_visita', 'desc')
                         ->paginate(10);

        $stats = [
            'total_visitas' => $patient->homeVisits()->count(),
            'realizadas' => $patient->homeVisits()->where('status', 'realizada')->count(),
            'agendadas' => $patient->homeVisits()->where('status', 'agendada')->count(),
            'ultima_visita' => $patient->homeVisits()->where('status', 'realizada')
                                                  ->orderBy('data_visita', 'desc')
                                                  ->first()
        ];

        return view('home_visits.by-patient', compact('patient', 'visits', 'stats'));
    }

    public function routePlanning(Request $request)
    {
        $date = $request->get('date', today());
        $userId = $request->get('user_id', auth()->id());

        $visits = HomeVisit::whereDate('data_visita', $date)
                          ->where('user_id', $userId)
                          ->where('status', 'agendada')
                          ->with('patient')
                          ->get();

        // Simular otimização de rota (aqui você integraria com APIs de mapas)
        $optimizedRoute = $this->optimizeRoute($visits);

        return view('home_visits.route-planning', compact('visits', 'optimizedRoute', 'date'));
    }

    public function generateReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $userId = $request->get('user_id');

        $query = HomeVisit::whereBetween('data_visita', [$startDate, $endDate]);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $visits = $query->with(['patient', 'user'])->get();

        $report = [
            'periodo' => [
                'inicio' => Carbon::parse($startDate),
                'fim' => Carbon::parse($endDate)
            ],
            'total_visitas' => $visits->count(),
            'por_status' => $visits->groupBy('status')->map->count(),
            'por_tipo' => $visits->groupBy('tipo_visita')->map->count(),
            'por_usuario' => $visits->groupBy('user.name')->map->count(),
            'taxa_realizacao' => $visits->count() > 0 ? 
                round(($visits->where('status', 'realizada')->count() / $visits->count()) * 100, 2) : 0,
            'tempo_medio_visita' => $this->calculateAverageVisitTime($visits),
            'gestantes_visitadas' => $visits->pluck('patient_id')->unique()->count(),
            'referencias_geradas' => $visits->where('necessita_referencia', true)->count()
        ];

        return view('home_visits.report', compact('report', 'visits'));
    }

    public function weeklySchedule(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfWeek());
        $endDate = Carbon::parse($startDate)->endOfWeek();
        $userId = $request->get('user_id', auth()->id());

        $visits = HomeVisit::whereBetween('data_visita', [$startDate, $endDate])
                          ->where('user_id', $userId)
                          ->with('patient')
                          ->orderBy('data_visita')
                          ->get()
                          ->groupBy(function($visit) {
                              return Carbon::parse($visit->data_visita)->format('Y-m-d');
                          });

        $weekDays = [];
        $currentDate = Carbon::parse($startDate);
        
        while ($currentDate <= $endDate) {
            $weekDays[$currentDate->format('Y-m-d')] = [
                'date' => $currentDate->copy(),
                'visits' => $visits->get($currentDate->format('Y-m-d'), collect())
            ];
            $currentDate->addDay();
        }

        return view('home_visits.weekly-schedule', compact('weekDays', 'startDate', 'endDate'));
    }

    public function activeSearch()
    {
        // Buscar gestantes que faltaram às consultas e precisam de visita
        $faltosas = Patient::whereHas('consultations', function($query) {
                              $query->where('status', 'agendada')
                                    ->where('data_consulta', '<', now()->subDays(3));
                          })
                          ->whereDoesntHave('homeVisits', function($query) {
                              $query->where('tipo_visita', 'faltosa')
                                    ->where('created_at', '>', now()->subDays(7));
                          })
                          ->with(['consultations' => function($query) {
                              $query->where('status', 'agendada')
                                    ->where('data_consulta', '<', now())
                                    ->orderBy('data_consulta');
                          }])
                          ->get();

        return view('home_visits.active-search', compact('faltosas'));
    }

    public function scheduleActiveSearch(Request $request)
    {
        $patientIds = $request->validate([
            'patient_ids' => 'required|array',
            'patient_ids.*' => 'exists:patients,id',
            'data_visita' => 'required|date|after_or_equal:today'
        ])['patient_ids'];

        $scheduledCount = 0;

        foreach ($patientIds as $patientId) {
            // Verificar se já não tem visita agendada
            $existingVisit = HomeVisit::where('patient_id', $patientId)
                                    ->where('tipo_visita', 'faltosa')
                                    ->where('status', 'agendada')
                                    ->exists();

            if (!$existingVisit) {
                $patient = Patient::find($patientId);
                
                HomeVisit::create([
                    'patient_id' => $patientId,
                    'user_id' => auth()->id(),
                    'data_visita' => $request->data_visita,
                    'motivo_visita' => 'Busca ativa - gestante faltosa às consultas',
                    'tipo_visita' => 'faltosa',
                    'endereco_visita' => $patient->endereco,
                    'status' => 'agendada'
                ]);

                $scheduledCount++;
            }
        }

        return redirect()->route('home_visits.index')
                        ->with('success', "Agendadas {$scheduledCount} visitas de busca ativa!");
    }

    public function mobileSync(Request $request)
    {
        $userId = auth()->id();
        $date = $request->get('date', today());

        $visits = HomeVisit::whereDate('data_visita', $date)
                          ->where('user_id', $userId)
                          ->with(['patient:id,nome_completo,contacto,endereco,documento_bi'])
                          ->get()
                          ->map(function($visit) {
                              return [
                                  'id' => $visit->id,
                                  'patient' => $visit->patient,
                                  'data_visita' => $visit->data_visita->format('H:i'),
                                  'tipo_visita' => $visit->tipo_visita_formatada,
                                  'endereco' => $visit->endereco_visita,
                                  'status' => $visit->status,
                                  'motivo' => $visit->motivo_visita
                              ];
                          });

        return response()->json([
            'visits' => $visits,
            'total' => $visits->count(),
            'pending' => $visits->where('status', 'agendada')->count()
        ]);
    }

    // Métodos auxiliares privados
    private function createReferenceNotification(HomeVisit $visit)
    {
        // Aqui você criaria uma notificação para o sistema
        // indicando que a gestante precisa ser referenciada
        
        // Por agora, vamos apenas adicionar uma observação
        activity()
            ->performedOn($visit)
            ->causedBy(auth()->user())
            ->log('Gestante necessita referência médica - identificado durante visita domiciliária');
    }

    private function optimizeRoute($visits)
    {
        // Algoritmo simples de otimização de rota
        // Em produção, você usaria APIs como Google Maps Directions API
        
        $optimized = $visits->sortBy(function($visit) {
            // Simular ordenação por proximidade
            return rand(1, 100);
        });

        return [
            'visits' => $optimized->values(),
            'total_distance' => rand(15, 50) . ' km',
            'estimated_time' => rand(4, 8) . ' horas',
            'fuel_cost' => 'MZN ' . rand(200, 500)
        ];
    }

    private function calculateAverageVisitTime($visits)
    {
        $realizedVisits = $visits->where('status', 'realizada');
        
        if ($realizedVisits->isEmpty()) {
            return 0;
        }

        $totalTime = $realizedVisits->sum(function($visit) {
            return $visit->getDuration() ?? 45; // 45 min default
        });

        return round($totalTime / $realizedVisits->count());
    }
}