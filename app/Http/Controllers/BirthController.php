<?php

namespace App\Http\Controllers;

use App\Models\Birth;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BirthController extends Controller
{
    public function index()
    {
        $births = Birth::with(['patient', 'user'])
                     ->orderBy('data_hora_parto', 'desc')
                     ->paginate(15);
                     
        return view('births.index', compact('births'));
    }

    public function create(Patient $patient)
    {
        // Verificar se a paciente pode dar à luz
        if (!$patient->podeRegistrarParto()) {
            return redirect()->route('patients.show', $patient)
                ->with('error', 'Esta paciente não está em condições de registrar parto.');
        }

        return view('births.create', compact('patient'));
    }

    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'data_hora_parto' => 'required|date|before_or_equal:now',
            'tipo_parto' => 'required|in:normal,cesariana,forceps,vacuum,outros',
            'local_parto' => 'nullable|string|max:255',
            'hospital_unidade' => 'nullable|string|max:255',
            'profissional_obstetra' => 'nullable|string|max:255',
            'profissional_enfermeiro' => 'nullable|string|max:255',
            'peso_mae_preparto' => 'nullable|numeric|min:30|max:200',
            'complicacoes_maternas' => 'nullable|string',
            
            // Dados do bebê
            'sexo_bebe' => 'nullable|in:masculino,feminino',
            'peso_nascimento' => 'required|numeric|min:300|max:6000',
            'altura_nascimento' => 'required|numeric|min:25|max:60',
            'apgar_1min' => 'required|integer|min:0|max:10',
            'apgar_5min' => 'required|integer|min:0|max:10',
            'apgar_10min' => 'nullable|integer|min:0|max:10',
            'status_bebe' => 'required|in:vivo_saudavel,vivo_complicacoes,obito_fetal,obito_neonatal',
            'observacoes_rn' => 'nullable|string',
            
            // Outros dados
            'parto_multiplo' => 'boolean',
            'numero_bebes' => 'required|integer|min:1|max:5',
            'observacoes_gerais' => 'nullable|string',
            'medicamentos_utilizados' => 'nullable|string',
            'condicoes_pos_parto' => 'nullable|string',
            'alta_hospitalar' => 'nullable|date|after_or_equal:data_hora_parto'
        ]);

        // Calcular idade gestacional no momento do parto
        if ($patient->data_ultima_menstruacao) {
            $dum = Carbon::parse($patient->data_ultima_menstruacao);
            $dataParto = Carbon::parse($validated['data_hora_parto']);
            $validated['idade_gestacional_parto'] = $dum->diffInWeeks($dataParto);
        }

        // Adicionar usuário que registrou
        $validated['user_id'] = auth()->id();

        // Iniciar transação para garantir consistência dos dados
        DB::transaction(function () use ($patient, $validated) {
            // Registrar o parto
            $birth = $patient->births()->create($validated);
            
            // Atualizar status da paciente
            $patient->update([
                'status_atual' => 'pos_parto',
                'numero_partos' => $patient->numero_partos + 1,
                'data_provavel_parto' => null, // Limpa a DPP
                'data_ultima_menstruacao' => null // Limpa a DUM
            ]);
            
            return $birth;
        });

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Parto registrado com sucesso! A paciente foi movida para status pós-parto.');
    }

    public function show(Birth $birth)
    {
        $birth->load(['patient', 'user']);
        return view('births.show', compact('birth'));
    }

    public function edit(Birth $birth)
    {
        return view('births.edit', compact('birth'));
    }

    public function update(Request $request, Birth $birth)
    {
        $validated = $request->validate([
            'data_hora_parto' => 'required|date|before_or_equal:now',
            'tipo_parto' => 'required|in:normal,cesariana,forceps,vacuum,outros',
            'local_parto' => 'nullable|string|max:255',
            'hospital_unidade' => 'nullable|string|max:255',
            'profissional_obstetra' => 'nullable|string|max:255',
            'profissional_enfermeiro' => 'nullable|string|max:255',
            'peso_mae_preparto' => 'nullable|numeric|min:30|max:200',
            'complicacoes_maternas' => 'nullable|string',
            
            'sexo_bebe' => 'nullable|in:masculino,feminino',
            'peso_nascimento' => 'required|numeric|min:300|max:6000',
            'altura_nascimento' => 'required|numeric|min:25|max:60',
            'apgar_1min' => 'required|integer|min:0|max:10',
            'apgar_5min' => 'required|integer|min:0|max:10',
            'apgar_10min' => 'nullable|integer|min:0|max:10',
            'status_bebe' => 'required|in:vivo_saudavel,vivo_complicacoes,obito_fetal,obito_neonatal',
            'observacoes_rn' => 'nullable|string',
            
            'parto_multiplo' => 'boolean',
            'numero_bebes' => 'required|integer|min:1|max:5',
            'observacoes_gerais' => 'nullable|string',
            'medicamentos_utilizados' => 'nullable|string',
            'condicoes_pos_parto' => 'nullable|string',
            'alta_hospitalar' => 'nullable|date|after_or_equal:data_hora_parto'
        ]);

        $birth->update($validated);

        return redirect()->route('births.show', $birth)
            ->with('success', 'Dados do parto atualizados com sucesso!');
    }

    // Marcar nova gestação para paciente pós-parto
    public function novaGestacao(Request $request, Patient $patient)
    {
        if ($patient->status_atual !== 'pos_parto') {
            return redirect()->back()
                ->with('error', 'Paciente deve estar em status pós-parto.');
        }

        $validated = $request->validate([
            'data_ultima_menstruacao' => 'required|date|before_or_equal:today|after:' . 
                                        $patient->ultimoParto?->data_hora_parto?->format('Y-m-d')
        ]);

        $patient->iniciarNovaGestacao($validated['data_ultima_menstruacao']);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Nova gestação registrada com sucesso!');
    }

    // Relatório de partos
    public function relatorio(Request $request)
    {
        $query = Birth::with(['patient']);

        // Filtros
        if ($request->filled('data_inicio')) {
            $query->where('data_hora_parto', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('data_hora_parto', '<=', $request->data_fim);
        }

        if ($request->filled('tipo_parto')) {
            $query->where('tipo_parto', $request->tipo_parto);
        }

        $partos = $query->orderBy('data_hora_parto', 'desc')->paginate(20);

        // Estatísticas
        $stats = [
            'total_partos' => Birth::count(),
            'partos_normais' => Birth::where('tipo_parto', 'normal')->count(),
            'cesarianas' => Birth::where('tipo_parto', 'cesariana')->count(),
            'bebes_saudaveis' => Birth::where('status_bebe', 'vivo_saudavel')->count(),
            'taxa_cesariana' => Birth::count() > 0 ? 
                round((Birth::where('tipo_parto', 'cesariana')->count() / Birth::count()) * 100, 1) : 0
        ];

        return view('births.relatorio', compact('partos', 'stats'));
    }
}