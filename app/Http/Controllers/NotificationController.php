<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\SystemNotification;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Exibe a Central Unificada de Notificações & SMS.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $hoje = Carbon::now()->startOfDay();

        // 1. Notificações do Sistema (com filtros)
        $notifQuery = SystemNotification::with(['patient', 'user', 'lidoPor'])
            ->paraUsuario($userId);

        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $notifQuery->where('tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            if ($request->status === 'nao_lidos') {
                $notifQuery->where('lido', false);
            } elseif ($request->status === 'lidos') {
                $notifQuery->where('lido', true);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $notifQuery->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('mensagem', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('nome_completo', 'like', "%{$search}%")
                         ->orWhere('documento_bi', 'like', "%{$search}%");
                  });
            });
        }

        $notifications = $notifQuery->orderBy('lido', 'asc')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'notif_page')
            ->withQueryString();

        // Contagens rápidas de notificações
        $notifStats = [
            'total' => SystemNotification::paraUsuario($userId)->count(),
            'nao_lidas' => SystemNotification::paraUsuario($userId)->naoLidos()->count(),
            'alertas' => SystemNotification::paraUsuario($userId)->where('tipo', 'alerta_clinico')->count(),
            'faltosas' => SystemNotification::paraUsuario($userId)->where('tipo', 'consulta_faltosa')->count(),
        ];

        // 2. Dados da Central de SMS (Pacientes Faltosas)
        $faltosasQuery = Consultation::with('patient')
            ->whereIn('status', ['agendada', 'confirmada'])
            ->where('data_consulta', '<', $hoje->format('Y-m-d'));

        if ($request->filled('search_faltosa')) {
            $searchF = $request->search_faltosa;
            $faltosasQuery->whereHas('patient', function ($q) use ($searchF) {
                $q->where('nome_completo', 'like', "%{$searchF}%")
                  ->orWhere('documento_bi', 'like', "%{$searchF}%")
                  ->orWhere('contacto', 'like', "%{$searchF}%");
            });
        }

        $faltosas = $faltosasQuery->orderBy('data_consulta', 'asc')->paginate(15, ['*'], 'faltosas_page');

        // 3. Histórico de Logs de SMS
        $smsLogs = DB::table('sms_logs')
            ->leftJoin('patients', 'sms_logs.patient_id', '=', 'patients.id')
            ->select('sms_logs.*', 'patients.nome_completo as paciente_nome')
            ->orderBy('sms_logs.created_at', 'desc')
            ->paginate(15, ['*'], 'logs_page');

        // Métricas de SMS
        $totalFaltosas = Consultation::whereIn('status', ['agendada', 'confirmada'])
            ->where('data_consulta', '<', $hoje->format('Y-m-d'))
            ->count();

        $inicioMes = Carbon::now()->startOfMonth();
        $totalEnviadosMes = DB::table('sms_logs')->where('created_at', '>=', $inicioMes)->count();
        $sucessosMes = DB::table('sms_logs')->where('created_at', '>=', $inicioMes)->where('status', 'enviado')->count();
        $taxaSucesso = $totalEnviadosMes > 0 ? round(($sucessosMes / $totalEnviadosMes) * 100, 1) : 100;

        $allPatients = Patient::orderBy('nome_completo')
            ->select('id', 'nome_completo', 'documento_bi', 'contacto', 'contacto_emergencia')
            ->get();

        // Templates MISAU
        $templates = [
            'exames' => [
                'icon' => 'flask',
                'titulo' => 'Resultado de Exame Pronto',
                'texto' => 'Estimada {nome}, informamos que o resultado do seu exame clínico de {servico} já se encontra disponível no Centro de Saúde de Quelimane Urbano. Compareça para levantamento.',
            ],
            'faltosa' => [
                'icon' => 'user-clock',
                'titulo' => 'Recuperação de Faltosa',
                'texto' => 'Estimada {nome}, notou-se a sua ausência na consulta pré-natal agendada para {data}. Dirija-se ao Centro de Saúde de Quelimane Urbano para reagendar e manter o seu bebê seguro.',
            ],
            'lembrete' => [
                'icon' => 'calendar-check',
                'titulo' => 'Lembrete de Consulta ANC',
                'texto' => 'Estimada {nome}, lembramos que a sua consulta de acompanhamento pré-natal no Centro de Saúde está agendada para {data}. Cuide de si e do seu bebê.',
            ],
            'vacinacao' => [
                'icon' => 'syringe',
                'titulo' => 'Aviso de Vacinação & IPTp-SP',
                'texto' => 'Estimada {nome}, a sua dose de vacina/prevenção contra malária (IPTp) está pronta no Centro de Saúde de Quelimane Urbano. Compareça para proteção.',
            ],
            'geral' => [
                'icon' => 'comment-dots',
                'titulo' => 'Notificação Geral de Serviço',
                'texto' => 'Estimada {nome}, solicitamos a sua comparência no Centro de Saúde de Quelimane Urbano para o serviço de {servico}.',
            ]
        ];

        return view('notifications.index', compact(
            'notifications',
            'notifStats',
            'faltosas',
            'smsLogs',
            'totalFaltosas',
            'totalEnviadosMes',
            'taxaSucesso',
            'templates',
            'allPatients'
        ));
    }

    /**
     * API para fornecer as notificações ao dropdown da navbar.
     */
    public function apiList(Request $request): JsonResponse
    {
        $user = auth()->user();
        $data = NotificationService::getNavbarNotifications($user, 10);
        return response()->json($data);
    }

    /**
     * Marcar notificação individual como lida.
     */
    public function markRead(Request $request, SystemNotification $notification): JsonResponse
    {
        $notification->marcarComoLido(auth()->user());

        $unreadCount = SystemNotification::paraUsuario(auth()->id())->naoLidos()->count();

        return response()->json([
            'status' => 'ok',
            'message' => 'Notificação marcada como lida.',
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Marcar todas as notificações do usuário como lidas.
     */
    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        $updated = NotificationService::markAllAsRead(auth()->user());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'message' => "{$updated} notificações marcadas como lidas.",
                'unreadCount' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    /**
     * Eliminar notificação.
     */
    public function destroy(Request $request, SystemNotification $notification): JsonResponse|RedirectResponse
    {
        $notification->delete();

        if ($request->wantsJson() || $request->ajax()) {
            $unreadCount = SystemNotification::paraUsuario(auth()->id())->naoLidos()->count();
            return response()->json([
                'status' => 'ok',
                'message' => 'Notificação removida.',
                'unreadCount' => $unreadCount,
            ]);
        }

        return redirect()->back()->with('success', 'Notificação removida com sucesso.');
    }

    /**
     * Retorna contagem de notificações não lidas.
     */
    public function unreadCount(): JsonResponse
    {
        $count = SystemNotification::paraUsuario(auth()->id())->naoLidos()->count();
        return response()->json(['count' => $count]);
    }
}

