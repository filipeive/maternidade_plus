<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::with(['patient', 'user']);

        if ($request->boolean('hoje')) {
            $query->whereDate('data_consulta', now()->toDateString());
        }

        if ($request->boolean('atrasadas')) {
            $query->whereIn('status', ['agendada', 'confirmada'])
                  ->where('data_consulta', '<', now()->startOfDay());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_consulta', $request->tipo);
        }

        if ($request->filled('data')) {
            $query->whereDate('data_consulta', $request->data);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('nome_completo', 'like', "%{$search}%")
                  ->orWhere('documento_bi', 'like', "%{$search}%")
                  ->orWhere('contacto', 'like', "%{$search}%");
            });
        }

        $consultations = $query->orderBy('data_consulta', 'desc')->paginate(15);
        
        return view('consultations.index', compact('consultations'));
    }

    public function create(Patient $patient = null)
    {
        $patients = Patient::where('ativo', true)->orderBy('nome_completo')->get();
        
        $latestBirth = null;
        $diasPosParto = null;
        $sugeridoTipoConsulta = null;
        $etapaPuerperio = null;

        if ($patient) {
            $latestBirth = \App\Models\Birth::where('patient_id', $patient->id)->latest('data_hora_parto')->first();
            if ($latestBirth && $latestBirth->data_hora_parto) {
                $diasPosParto = Carbon::parse($latestBirth->data_hora_parto)->diffInDays(now());
                $sugeridoTipoConsulta = 'pos_parto';
                
                if ($diasPosParto <= 3) {
                    $etapaPuerperio = '1ª Consulta de Puerpério (48 horas pós-parto)';
                } elseif ($diasPosParto <= 10) {
                    $etapaPuerperio = '2ª Consulta de Puerpério (7 dias pós-parto)';
                } elseif ($diasPosParto <= 42) {
                    $etapaPuerperio = '3ª Consulta de Puerpério (28 a 42 dias pós-parto)';
                } else {
                    $etapaPuerperio = 'Consulta Pós-Parto Tardio / Planeamento Familiar';
                }
            } else {
                // Cálculo automático por semanas de gestação (Trimesters MISAU)
                $semanas = $patient->semanas_gestacao ?? $patient->getSemanasGestacionaisNaData(now());
                if ($semanas) {
                    if ($semanas <= 12) {
                        $sugeridoTipoConsulta = '1_trimestre';
                    } elseif ($semanas <= 27) {
                        $sugeridoTipoConsulta = '2_trimestre';
                    } else {
                        $sugeridoTipoConsulta = '3_trimestre';
                    }
                }
            }
        }
        
        return view('consultations.create', compact('patient', 'patients', 'latestBirth', 'diasPosParto', 'sugeridoTipoConsulta', 'etapaPuerperio'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'data_consulta' => 'required|date',
            'tipo_consulta' => 'required|in:1_trimestre,2_trimestre,3_trimestre,pos_parto,emergencia',
            'semanas_gestacao' => 'nullable|integer|min:1|max:45',
            'peso' => 'nullable|numeric|min:30|max:200',
            'pressao_arterial' => 'nullable|string|max:20',
            'batimentos_fetais' => 'nullable|integer|min:110|max:180',
            'altura_uterina' => 'nullable|numeric|min:10|max:50',
            'observacoes' => 'nullable|string',
            'orientacoes' => 'nullable|string',
            'proxima_consulta' => 'nullable|date|after:data_consulta',
            'status' => 'nullable|in:agendada,confirmada,realizada,cancelada'
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = $request->input('status', 'realizada');

        if (empty($validated['semanas_gestacao'])) {
            $patient = Patient::find($validated['patient_id']);
            if ($patient) {
                $validated['semanas_gestacao'] = $patient->getSemanasGestacionaisNaData(\Carbon\Carbon::parse($validated['data_consulta']));
            }
        }

        $consultation = Consultation::create($validated);

        $smsStatusNotice = '';
        if (!empty($validated['proxima_consulta'])) {
            $smsStatusNotice = $this->handleProximaConsulta($consultation, $validated['proxima_consulta'], true, true);
        }

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consulta criada com sucesso! ' . $smsStatusNotice);
    }

    public function show(Consultation $consultation)
    {
        $consultation->load(['patient', 'user', 'exams']);
        
        return view('consultations.show', compact('consultation'));
    }

    public function edit(Consultation $consultation)
    {
        $patients = Patient::where('ativo', true)->orderBy('nome_completo')->get();
        
        return view('consultations.edit', compact('consultation', 'patients'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'data_consulta' => 'required|date',
            'tipo_consulta' => 'required|in:1_trimestre,2_trimestre,3_trimestre,pos_parto,emergencia',
            'semanas_gestacao' => 'nullable|integer|min:1|max:45',
            'peso' => 'nullable|numeric|min:30|max:200',
            'pressao_arterial' => 'nullable|string|max:20',
            'batimentos_fetais' => 'nullable|integer|min:110|max:180',
            'altura_uterina' => 'nullable|numeric|min:10|max:50',
            'observacoes' => 'nullable|string',
            'orientacoes' => 'nullable|string',
            'proxima_consulta' => 'nullable|date|after:data_consulta',
            'status' => 'required|in:agendada,confirmada,realizada,cancelada'
        ]);

        if (empty($validated['semanas_gestacao'])) {
            $patient = Patient::find($validated['patient_id']);
            if ($patient) {
                $validated['semanas_gestacao'] = $patient->getSemanasGestacionaisNaData(\Carbon\Carbon::parse($validated['data_consulta']));
            }
        }

        $consultation->update($validated);

        $smsStatusNotice = '';
        if (!empty($validated['proxima_consulta'])) {
            $smsStatusNotice = $this->handleProximaConsulta($consultation, $validated['proxima_consulta'], true, true);
        }

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consulta atualizada com sucesso! ' . $smsStatusNotice);
    }

    public function destroy(Consultation $consultation)
    {
        $consultation->delete();
        
        return redirect()->route('consultations.index')
            ->with('success', 'Consulta removida com sucesso.');
    }

    // Método adicional para consultas por paciente
    public function byPatient(Patient $patient)
    {
        $consultations = $patient->consultations()
            ->with(['user', 'exams'])
            ->orderBy('data_consulta', 'desc')
            ->paginate(10);
            
        return view('consultations.by-patient', compact('patient', 'consultations'));
    }

    // Método para confirmar consulta
    public function confirm(Request $request, Consultation $consultation)
    {
        $data = $request->validate([
            'observacoes' => 'nullable|string',
            'orientacoes' => 'nullable|string',
            'proxima_consulta' => 'nullable|date',
            'agendar_proxima' => 'nullable|boolean',
            'enviar_sms' => 'nullable|boolean'
        ]);

        $updateData = ['status' => 'confirmada'];
        if (!empty($data['observacoes'])) $updateData['observacoes'] = $data['observacoes'];
        if (!empty($data['orientacoes'])) $updateData['orientacoes'] = $data['orientacoes'];
        if (!empty($data['proxima_consulta'])) $updateData['proxima_consulta'] = $data['proxima_consulta'];

        $consultation->update($updateData);

        $smsNotice = '';
        if (!empty($data['proxima_consulta'])) {
            $agendar = $request->boolean('agendar_proxima', true);
            $sms = $request->boolean('enviar_sms', true);
            $smsNotice = $this->handleProximaConsulta($consultation, $data['proxima_consulta'], $agendar, $sms);
        }

        return back()->with('success', 'Consulta confirmada com sucesso! ' . $smsNotice);
    }

    // Método para marcar como realizada
    public function complete(Request $request, Consultation $consultation)
    {
        $data = $request->validate([
            'observacoes' => 'nullable|string',
            'orientacoes' => 'nullable|string',
            'proxima_consulta' => 'nullable|date',
            'agendar_proxima' => 'nullable|boolean',
            'enviar_sms' => 'nullable|boolean'
        ]);

        $updateData = ['status' => 'realizada'];
        if (!empty($data['observacoes'])) $updateData['observacoes'] = $data['observacoes'];
        if (!empty($data['orientacoes'])) $updateData['orientacoes'] = $data['orientacoes'];
        if (!empty($data['proxima_consulta'])) $updateData['proxima_consulta'] = $data['proxima_consulta'];

        $consultation->update($updateData);

        $smsNotice = '';
        if (!empty($data['proxima_consulta'])) {
            $agendar = $request->boolean('agendar_proxima', true);
            $sms = $request->boolean('enviar_sms', true);
            $smsNotice = $this->handleProximaConsulta($consultation, $data['proxima_consulta'], $agendar, $sms);
        }

        return back()->with('success', 'Consulta realizada com sucesso! ' . $smsNotice);
    }

    /**
     * Helper privado para agendamento automático da próxima consulta e envio de SMS de lembrete
     */
    private function handleProximaConsulta(Consultation $consultation, string $proximaData, bool $agendarAutomatico = true, bool $enviarSms = true): string
    {
        $patient = $consultation->patient;
        if (!$patient) return '';

        $dataProx = Carbon::parse($proximaData);
        $dataFormatted = $dataProx->format('d/m/Y \à\s H:i');

        // 1. Agendamento automático no sistema
        if ($agendarAutomatico) {
            $exists = Consultation::where('patient_id', $patient->id)
                ->whereDate('data_consulta', $dataProx->toDateString())
                ->exists();

            if (!$exists) {
                // Determinar o próximo tipo de consulta
                $nextType = $consultation->tipo_consulta;
                if ($nextType !== 'pos_parto') {
                    $proxSemanas = $patient->semanas_gestacao ? ($patient->semanas_gestacao + 4) : null;
                    if ($proxSemanas) {
                        if ($proxSemanas <= 12) $nextType = '1_trimestre';
                        elseif ($proxSemanas <= 27) $nextType = '2_trimestre';
                        else $nextType = '3_trimestre';
                    }
                }

                Consultation::create([
                    'patient_id' => $patient->id,
                    'user_id' => auth()->id(),
                    'data_consulta' => $dataProx,
                    'tipo_consulta' => $nextType,
                    'status' => 'agendada',
                    'observacoes' => 'Próxima consulta agendada automaticamente após a consulta de ' . $consultation->data_consulta->format('d/m/Y')
                ]);
            }
        }

        // 2. Envio de SMS
        $notice = '';
        if ($enviarSms) {
            $phone = $patient->contacto ?? $patient->contacto_emergencia;
            if (!empty($phone)) {
                $primeiroNome = explode(' ', trim($patient->nome_completo))[0];
                $mensagem = "Maternidade+: Olá Sra. {$primeiroNome}! A sua consulta foi registada. A sua próxima consulta pré-natal/puerpério foi agendada para {$dataFormatted}. Por favor, compareça à unidade sanitária.";
                
                list($success, $msg) = \App\Services\SmsService::sendSmsAndLog($patient->id, $phone, $mensagem);
                if ($success) {
                    $notice = '📱 SMS de lembrete enviado para a paciente.';
                } else {
                    $notice = '⚠️ SMS não entregue: ' . $msg;
                }
            } else {
                $notice = '⚠️ Paciente sem contacto de telemóvel registado.';
            }
        }

        return $notice;
    }
}