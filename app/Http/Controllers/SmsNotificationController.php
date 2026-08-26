<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmsNotificationController extends Controller
{
    public function index(Request $request)
    {
        $hoje = Carbon::now()->startOfDay();

        // 1. Consultas Atrasadas / Pacientes Faltosas
        $faltosasQuery = Consultation::with('patient')
            ->whereIn('status', ['agendada', 'confirmada'])
            ->where('data_consulta', '<', $hoje->format('Y-m-d'));

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $faltosasQuery->whereHas('patient', function ($q) use ($search) {
                $q->where('nome_completo', 'like', "%{$search}%")
                  ->orWhere('documento_bi', 'like', "%{$search}%")
                  ->orWhere('contacto', 'like', "%{$search}%");
            });
        }

        $faltosas = $faltosasQuery->orderBy('data_consulta', 'asc')->paginate(15, ['*'], 'faltosas_page');

        // 2. Histórico de Logs de SMS
        $smsLogs = DB::table('sms_logs')
            ->leftJoin('patients', 'sms_logs.patient_id', '=', 'patients.id')
            ->select('sms_logs.*', 'patients.nome_completo as paciente_nome')
            ->orderBy('sms_logs.created_at', 'desc')
            ->paginate(15, ['*'], 'logs_page');

        // 3. Métricas
        $totalFaltosas = Consultation::whereIn('status', ['agendada', 'confirmada'])
            ->where('data_consulta', '<', $hoje->format('Y-m-d'))
            ->count();

        $inicioMes = Carbon::now()->startOfMonth();
        $totalEnviadosMes = DB::table('sms_logs')
            ->where('created_at', '>=', $inicioMes)
            ->count();

        $sucessosMes = DB::table('sms_logs')
            ->where('created_at', '>=', $inicioMes)
            ->where('status', 'enviado')
            ->count();

        $taxaSucesso = $totalEnviadosMes > 0 ? round(($sucessosMes / $totalEnviadosMes) * 100, 1) : 100;

        // Templates MISAU
        $templates = [
            'faltosa' => [
                'titulo' => 'Recuperação de Faltosa (Atraso na Consulta)',
                'texto' => 'Estimada {nome}, notou-se a sua ausência na consulta pré-natal agendada para {data}. Dirija-se ao Centro de Saúde de Quelimane Urbano para reagendar e manter o seu bebê seguro.',
            ],
            'lembrete' => [
                'titulo' => 'Lembrete de Consulta Pré-Natal',
                'texto' => 'Olá {nome}, lembramos que a sua consulta de acompanhamento pré-natal no Centro de Saúde está agendada para amanhã ({data}). Cuide de si e do seu bebê.',
            ],
            'vacinacao' => [
                'titulo' => 'Aviso de Vacinação & IPTp-SP',
                'texto' => 'Olá {nome}, a sua próxima dose de vacina/prevenção contra malária (IPTp) está pronta no Centro de Saúde de Quelimane Urbano. Compareça para proteção.',
            ]
        ];

        return view('sms.index', compact('faltosas', 'smsLogs', 'totalFaltosas', 'totalEnviadosMes', 'taxaSucesso', 'templates'));
    }

    public function sendSingle(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'mensagem' => 'required|string|max:480',
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $phone = $patient->contacto ?? $patient->contacto_emergencia;

        if (empty($phone)) {
            return back()->with('error', "A paciente {$patient->nome_completo} não possui número de telefone registado.");
        }

        // Substituir variáveis no template
        $mensagemSubstituida = str_replace('{nome}', $patient->nome_completo, $request->mensagem);

        [$success, $statusMessage] = SmsService::sendSmsAndLog($patient->id, $phone, $mensagemSubstituida);

        if ($success) {
            return back()->with('success', "SMS enviado com sucesso para a paciente {$patient->nome_completo} ({$phone})!");
        }

        $errorMsg = auth()->user()->hasRole('Administrador') 
            ? "Falha ao enviar SMS para {$patient->nome_completo}: {$statusMessage}"
            : "Falha no envio do SMS para {$patient->nome_completo}. Utilize o botão 'Reenviar' no histórico de logs.";

        return back()->with('error', $errorMsg);
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'mensagem_template' => 'required|string|max:480',
        ]);

        $hoje = Carbon::now()->startOfDay();
        $faltosas = Consultation::with('patient')
            ->whereIn('status', ['agendada', 'confirmada'])
            ->where('data_consulta', '<', $hoje->format('Y-m-d'))
            ->get();

        if ($faltosas->isEmpty()) {
            return back()->with('info', 'Não existem pacientes faltosas pendentes no momento.');
        }

        $enviados = 0;
        $falhas = 0;

        foreach ($faltosas as $consulta) {
            $patient = $consulta->patient;
            if (!$patient) continue;

            $phone = $patient->contacto ?? $patient->contacto_emergencia;
            if (empty($phone)) {
                $falhas++;
                continue;
            }

            $msg = str_replace(
                ['{nome}', '{data}'],
                [$patient->nome_completo, $consulta->data_consulta?->format('d/m/Y') ?? 'recabada'],
                $request->mensagem_template
            );

            [$success] = SmsService::sendSmsAndLog($patient->id, $phone, $msg);
            if ($success) {
                $enviados++;
            } else {
                $falhas++;
            }
        }

        return back()->with('success', "Disparo concluído: {$enviados} SMS enviados com sucesso. ({$falhas} falhas ou sem número).");
    }
}
