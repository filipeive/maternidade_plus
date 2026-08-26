<?php

namespace App\Http\Controllers;

use App\Models\Birth;
use App\Models\Patient;
use App\Models\Alerta;
use App\Models\Consultation;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $dataPartoObj = Carbon::parse($validated['data_hora_parto']);

        // Iniciar transação para garantir consistência dos dados
        DB::transaction(function () use ($patient, $validated, $dataPartoObj) {
            // 1. Registrar o parto
            $birth = $patient->births()->create($validated);
            
            // 2. Atualizar status da paciente para pós-parto
            $patient->update([
                'status_atual' => 'pos_parto',
                'numero_partos' => $patient->numero_partos + 1,
                'data_provavel_parto' => null, // Limpa a DPP
                'data_ultima_menstruacao' => null // Limpa a DUM
            ]);

            // 3. Resolver automaticamente alertas ativos da gestação encerrada
            Alerta::where('patient_id', $patient->id)
                ->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])
                ->update([
                    'status' => Alerta::STATUS_RESOLVIDO,
                    'nota_resolucao' => 'Parto registado com sucesso. Paciente transferida para acompanhamento pós-parto (puerpério).',
                    'resolvido_por' => auth()->id(),
                    'data_resolucao' => now(),
                ]);

            // 4. Gerar Consultas de Puerpério MISAU Moçambique
            // Consulta 1: Puerpério 48 horas (2 dias)
            Consultation::create([
                'patient_id' => $patient->id,
                'user_id' => auth()->id(),
                'data_consulta' => $dataPartoObj->copy()->addDays(2),
                'tipo_consulta' => 'pos_parto',
                'status' => 'agendada',
                'observacoes' => '1ª Consulta de Puerpério MISAU (48 horas pós-parto / antes da alta). Avaliação de involução uterina, lochia e aleitamento materno.',
            ]);

            // Consulta 2: Puerpério 7 dias
            Consultation::create([
                'patient_id' => $patient->id,
                'user_id' => auth()->id(),
                'data_consulta' => $dataPartoObj->copy()->addDays(7),
                'tipo_consulta' => 'pos_parto',
                'status' => 'agendada',
                'observacoes' => '2ª Consulta de Puerpério MISAU (7 dias pós-parto). Exame físico da puérpera, cicatrização e triagem do recém-nascido.',
            ]);

            // Consulta 3: Puerpério 28 dias / 6 semanas
            Consultation::create([
                'patient_id' => $patient->id,
                'user_id' => auth()->id(),
                'data_consulta' => $dataPartoObj->copy()->addDays(28),
                'tipo_consulta' => 'pos_parto',
                'status' => 'agendada',
                'observacoes' => '3ª Consulta de Puerpério MISAU (28 dias / 6 semanas). Planeamento Familiar pós-parto, vacinação do RN e alta puerperal.',
            ]);

            return $birth;
        });

        // 5. Enviar notificação SMS para a paciente (se possuir telemóvel registado)
        if (!empty($patient->telefone)) {
            $msgSms = "Maternidade+: Parabens Sra. {$patient->nome_completo}! O parto foi registado. Lembramos da sua 1a Consulta de Puerperio em 48h na Unidade Sanitaria.";
            SmsService::sendSms($patient->telefone, $msgSms);
        }

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Parto registrado com sucesso! Paciente movida para Pós-Parto e agenda de Puerpério MISAU (48h, 7d, 28d) gerada.');
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