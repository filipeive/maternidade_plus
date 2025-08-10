<?php

namespace App\Http\Controllers;

use App\Models\Vaccine;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VaccineController extends Controller
{
    public function index(Request $request)
    {
        $query = Vaccine::with(['patient', 'user']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo_vacina')) {
            $query->where('tipo_vacina', $request->tipo_vacina);
        }

        if ($request->filled('patient_search')) {
            $query->whereHas('patient', function($q) use ($request) {
                $q->where('nome_completo', 'LIKE', '%' . $request->patient_search . '%');
            });
        }

        // Verificar doses vencidas/próximas
        if ($request->filled('alert_type')) {
            if ($request->alert_type === 'vencidas') {
                $query->where('status', 'pendente')
                      ->whereDate('proxima_dose', '<', now());
            } elseif ($request->alert_type === 'proximas') {
                $query->where('status', 'pendente')
                      ->whereDate('proxima_dose', '<=', now()->addDays(7));
            }
        }

        $vaccines = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estatísticas para dashboard
        $stats = [
            'total_administradas' => Vaccine::where('status', 'administrada')->count(),
            'doses_pendentes' => Vaccine::where('status', 'pendente')->count(),
            'doses_vencidas' => Vaccine::where('status', 'pendente')
                                     ->whereDate('proxima_dose', '<', now())->count(),
            'proximas_7_dias' => Vaccine::where('status', 'pendente')
                                       ->whereDate('proxima_dose', '<=', now()->addDays(7))
                                       ->count()
        ];

        return view('vaccines.index', compact('vaccines', 'stats'));
    }

    public function create(Request $request)
    {
        $patient = null;
        
        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->patient_id);
        }

        $patients = Patient::where('ativo', true)
                          ->orderBy('nome_completo')
                          ->get(['id', 'nome_completo', 'documento_bi']);

        $esquemas_vacinais = Vaccine::getVacinasPrenatal();

        return view('vaccines.create', compact('patient', 'patients', 'esquemas_vacinais'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tipo_vacina' => 'required|in:tetanica,hepatite_b,influenza,covid19,febre_amarela,iptp',
            'descricao' => 'nullable|string',
            'data_administracao' => 'required|date|before_or_equal:today',
            'dose_numero' => 'required|integer|min:1|max:5',
            'lote' => 'nullable|string|max:100',
            'fabricante' => 'nullable|string|max:100',
            'data_vencimento' => 'nullable|date|after:data_administracao',
            'local_aplicacao' => 'required|in:braco_esquerdo,braco_direito,coxa_esquerda,coxa_direita,gluteo',
            'observacoes' => 'nullable|string',
            'reacao_adversa' => 'nullable|string',
            'status' => 'required|in:administrada,pendente'
        ]);

        $validated['user_id'] = auth()->id();

        // Calcular próxima dose se aplicável
        if ($validated['status'] === 'administrada') {
            $vaccine = new Vaccine($validated);
            $proximaDose = $vaccine->calcularProximaDose();
            
            if ($proximaDose && $validated['dose_numero'] < 3) {
                $validated['proxima_dose'] = $proximaDose;
                
                // Criar registro para próxima dose
                Vaccine::create([
                    'patient_id' => $validated['patient_id'],
                    'user_id' => auth()->id(),
                    'tipo_vacina' => $validated['tipo_vacina'],
                    'dose_numero' => $validated['dose_numero'] + 1,
                    'data_administracao' => $proximaDose,
                    'status' => 'pendente',
                    'observacoes' => 'Dose agendada automaticamente'
                ]);
            }
        }

        Vaccine::create($validated);

        return redirect()->route('vaccines.index')
                        ->with('success', 'Vacina registrada com sucesso!');
    }

    public function show(Vaccine $vaccine)
    {
        $vaccine->load(['patient', 'user']);
        
        // Buscar outras doses da mesma vacina para esta gestante
        $outrasVacinas = Vaccine::where('patient_id', $vaccine->patient_id)
                               ->where('tipo_vacina', $vaccine->tipo_vacina)
                               ->where('id', '!=', $vaccine->id)
                               ->orderBy('dose_numero')
                               ->get();

        return view('vaccines.show', compact('vaccine', 'outrasVacinas'));
    }

    public function edit(Vaccine $vaccine)
    {
        $patients = Patient::where('ativo', true)
                          ->orderBy('nome_completo')
                          ->get(['id', 'nome_completo', 'documento_bi']);

        $esquemas_vacinais = Vaccine::getVacinasPrenatal();

        return view('vaccines.edit', compact('vaccine', 'patients', 'esquemas_vacinais'));
    }

    public function update(Request $request, Vaccine $vaccine)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tipo_vacina' => 'required|in:tetanica,hepatite_b,influenza,covid19,febre_amarela,iptp',
            'descricao' => 'nullable|string',
            'data_administracao' => 'required|date|before_or_equal:today',
            'dose_numero' => 'required|integer|min:1|max:5',
            'lote' => 'nullable|string|max:100',
            'fabricante' => 'nullable|string|max:100',
            'data_vencimento' => 'nullable|date|after:data_administracao',
            'local_aplicacao' => 'required|in:braco_esquerdo,braco_direito,coxa_esquerda,coxa_direita,gluteo',
            'observacoes' => 'nullable|string',
            'reacao_adversa' => 'nullable|string',
            'status' => 'required|in:administrada,pendente,vencida,reagenda'
        ]);

        $vaccine->update($validated);

        return redirect()->route('vaccines.show', $vaccine)
                        ->with('success', 'Dados da vacina atualizados com sucesso!');
    }

    public function destroy(Vaccine $vaccine)
    {
        $vaccine->delete();

        return redirect()->route('vaccines.index')
                        ->with('success', 'Registro de vacina removido com sucesso!');
    }

    // Métodos específicos para gestão de vacinas

    public function byPatient(Patient $patient)
    {
        $vaccines = $patient->vaccines()
                           ->with('user')
                           ->orderBy('data_administracao', 'desc')
                           ->paginate(10);

        $esquemaVacinal = $this->gerarEsquemaVacinal($patient);

        return view('vaccines.by-patient', compact('patient', 'vaccines', 'esquemaVacinal'));
    }

    public function pendingAlert()
    {
        $dosesVencidas = Vaccine::where('status', 'pendente')
                               ->whereDate('proxima_dose', '<', now())
                               ->with('patient')
                               ->get();

        $proximasDoses = Vaccine::where('status', 'pendente')
                               ->whereDate('proxima_dose', '<=', now()->addDays(7))
                               ->with('patient')
                               ->get();

        return view('vaccines.alerts', compact('dosesVencidas', 'proximasDoses'));
    }

    public function markAsAdministered(Request $request, Vaccine $vaccine)
    {
        $validated = $request->validate([
            'data_administracao' => 'required|date|before_or_equal:today',
            'lote' => 'nullable|string|max:100',
            'fabricante' => 'nullable|string|max:100',
            'local_aplicacao' => 'required|string',
            'observacoes' => 'nullable|string',
            'reacao_adversa' => 'nullable|string'
        ]);

        $vaccine->update(array_merge($validated, [
            'status' => 'administrada',
            'user_id' => auth()->id()
        ]));

        return redirect()->back()
                        ->with('success', 'Vacina marcada como administrada!');
    }

    public function reschedule(Request $request, Vaccine $vaccine)
    {
        $validated = $request->validate([
            'nova_data' => 'required|date|after:today',
            'motivo' => 'required|string'
        ]);

        $vaccine->update([
            'data_administracao' => $validated['nova_data'],
            'status' => 'reagenda',
            'observacoes' => $vaccine->observacoes . "\n\nReagendada: " . $validated['motivo']
        ]);

        return redirect()->back()
                        ->with('success', 'Vacina reagendada com sucesso!');
    }

    private function gerarEsquemaVacinal(Patient $patient)
    {
        $esquemas = Vaccine::getVacinasPrenatal();
        $vacinasAdministradas = $patient->vaccines->groupBy('tipo_vacina');
        
        $esquemaCompleto = [];
        
        foreach ($esquemas as $tipo => $info) {
            $esquemaCompleto[$tipo] = [
                'info' => $info,
                'doses_administradas' => $vacinasAdministradas->get($tipo, collect())->count(),
                'proxima_dose' => $vacinasAdministradas->get($tipo, collect())
                                                     ->where('status', 'pendente')
                                                     ->first(),
                'completo' => $vacinasAdministradas->get($tipo, collect())->count() >= $info['doses']
            ];
        }
        
        return $esquemaCompleto;
    }

    public function generateReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $report = [
            'periodo' => [
                'inicio' => Carbon::parse($startDate),
                'fim' => Carbon::parse($endDate)
            ],
            'vacinas_por_tipo' => Vaccine::whereBetween('created_at', [$startDate, $endDate])
                                        ->where('status', 'administrada')
                                        ->selectRaw('tipo_vacina, COUNT(*) as total')
                                        ->groupBy('tipo_vacina')
                                        ->get(),
            'cobertura_vacinal' => $this->calcularCoberturaVacinal($startDate, $endDate),
            'doses_pendentes' => Vaccine::where('status', 'pendente')->count(),
            'reacoes_adversas' => Vaccine::whereNotNull('reacao_adversa')
                                        ->whereBetween('created_at', [$startDate, $endDate])
                                        ->count()
        ];

        return view('vaccines.report', compact('report'));
    }

    private function calcularCoberturaVacinal($startDate, $endDate)
    {
        $totalGestantes = Patient::where('ativo', true)
                               ->whereBetween('created_at', [$startDate, $endDate])
                               ->count();

        if ($totalGestantes === 0) return [];

        $esquemas = Vaccine::getVacinasPrenatal();
        $cobertura = [];

        foreach ($esquemas as $tipo => $info) {
            $gestantesVacinadas = Patient::whereHas('vaccines', function($q) use ($tipo, $startDate, $endDate) {
                $q->where('tipo_vacina', $tipo)
                  ->where('status', 'administrada')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })->distinct()->count();

            $cobertura[$tipo] = [
                'nome' => $info['nome'],
                'cobertura' => $totalGestantes > 0 ? round(($gestantesVacinadas / $totalGestantes) * 100, 2) : 0,
                'gestantes_vacinadas' => $gestantesVacinadas,
                'total_gestantes' => $totalGestantes
            ];
        }

        return $cobertura;
    }
}