<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\SystemNotification;
use App\Models\User;
use App\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Obter notificações formatadas para o dropdown da navbar.
     */
    public static function getNavbarNotifications(?User $user = null, int $limit = 10): array
    {
        $userId = $user?->id ?? auth()->id();

        // Se a tabela estiver com poucos registros ou vazia, sincronizar eventos clínicos recentes
        if (SystemNotification::count() < 3) {
            self::syncRecentClinicalEventsToNotifications();
        }

        $notifications = SystemNotification::paraUsuario($userId)
            ->orderBy('lido', 'asc')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $unreadCount = SystemNotification::paraUsuario($userId)
            ->naoLidos()
            ->count();

        $items = $notifications->map(function (SystemNotification $n) {
            return [
                'id' => $n->id,
                'title' => $n->titulo,
                'message' => $n->mensagem,
                'icon' => $n->icone ?: 'bell',
                'color' => $n->cor ?: 'info',
                'time' => $n->created_at ? $n->created_at->diffForHumans() : 'recente',
                'unread' => !$n->lido,
                'url' => $n->url ?: route('notifications.index'),
            ];
        })->values()->toArray();

        return [
            'unreadCount' => $unreadCount,
            'notifications' => $items,
        ];
    }

    /**
     * Sincronizar eventos clínicos reais como notificações do sistema caso ainda não existam.
     */
    public static function syncRecentClinicalEventsToNotifications(): void
    {
        try {
            // 1. Alertas Clínicos Ativos de Alto Nível
            $alertasAltos = Alerta::with('patient')
                ->where('nivel', Alerta::NIVEL_ALTO)
                ->whereIn('status', [Alerta::STATUS_ATIVO, Alerta::STATUS_EM_SEGUIMENTO])
                ->latest()
                ->take(5)
                ->get();

            foreach ($alertasAltos as $alerta) {
                $nome = $alerta->patient?->nome_completo ?? 'Gestante';
                $titulo = "Alerta Clínico: {$alerta->tipo_label}";
                $mensagem = "{$nome} — {$alerta->mensagem}";
                $url = route('alertas.index');

                // Evitar duplicação
                $exists = SystemNotification::where('tipo', 'alerta_clinico')
                    ->where('mensagem', $mensagem)
                    ->exists();

                if (!$exists) {
                    SystemNotification::create([
                        'patient_id' => $alerta->patient_id,
                        'tipo' => 'alerta_clinico',
                        'titulo' => $titulo,
                        'mensagem' => $mensagem,
                        'icone' => 'triangle-exclamation',
                        'cor' => 'danger',
                        'url' => $url,
                        'lido' => (bool)$alerta->lido,
                        'created_at' => $alerta->created_at ?? now(),
                    ]);
                }
            }

            // 2. Consultas Faltosas Recentes
            $hoje = Carbon::now()->startOfDay();
            $faltosas = Consultation::with('patient')
                ->whereIn('status', ['agendada', 'confirmada'])
                ->where('data_consulta', '<', $hoje->format('Y-m-d'))
                ->latest('data_consulta')
                ->take(5)
                ->get();

            foreach ($faltosas as $consulta) {
                $nome = $consulta->patient?->nome_completo ?? 'Gestante';
                $data = $consulta->data_consulta ? Carbon::parse($consulta->data_consulta)->format('d/m/Y') : 'data anterior';
                $titulo = "Gestante Faltosa";
                $mensagem = "{$nome} faltou à consulta agendada para {$data}. Envie um SMS de recuperação.";
                $url = route('sms.index');

                $exists = SystemNotification::where('tipo', 'consulta_faltosa')
                    ->where('mensagem', $mensagem)
                    ->exists();

                if (!$exists) {
                    SystemNotification::create([
                        'patient_id' => $consulta->patient_id,
                        'tipo' => 'consulta_faltosa',
                        'titulo' => $titulo,
                        'mensagem' => $mensagem,
                        'icone' => 'user-clock',
                        'cor' => 'warning',
                        'url' => $url,
                        'lido' => false,
                        'created_at' => $consulta->created_at ?? now(),
                    ]);
                }
            }

            // 3. Exames Concluídos / Prontos
            $examesProntos = Exam::with('patient')
                ->where('status', 'concluido')
                ->latest('updated_at')
                ->take(3)
                ->get();

            foreach ($examesProntos as $exame) {
                $nome = $exame->patient?->nome_completo ?? 'Gestante';
                $titulo = "Resultado de Exame Disponível";
                $mensagem = "{$nome} — Exame de {$exame->tipo_exame} concluído com resultado disponível.";
                $url = route('exams.show', $exame->id);

                $exists = SystemNotification::where('tipo', 'exame_pronto')
                    ->where('mensagem', $mensagem)
                    ->exists();

                if (!$exists) {
                    SystemNotification::create([
                        'patient_id' => $exame->patient_id,
                        'tipo' => 'exame_pronto',
                        'titulo' => $titulo,
                        'mensagem' => $mensagem,
                        'icone' => 'flask',
                        'cor' => 'info',
                        'url' => $url,
                        'lido' => false,
                        'created_at' => $exame->updated_at ?? now(),
                    ]);
                }
            }

            // 4. Vacinas / IPTp Atrasadas
            $vacinasAtrasadas = Vaccine::with('patient')
                ->where('status', 'atrasada')
                ->orWhere(function ($q) use ($hoje) {
                    $q->where('status', 'agendada')->where('data_prevista', '<', $hoje->format('Y-m-d'));
                })
                ->latest('data_prevista')
                ->take(3)
                ->get();

            foreach ($vacinasAtrasadas as $vacina) {
                $nome = $vacina->patient?->nome_completo ?? 'Gestante';
                $titulo = "Vacina / IPTp em Atraso";
                $mensagem = "{$nome} — Dose de {$vacina->tipo_vacina} vencida no calendário profilático.";
                $url = route('vaccines.index');

                $exists = SystemNotification::where('tipo', 'vacina_atraso')
                    ->where('mensagem', $mensagem)
                    ->exists();

                if (!$exists) {
                    SystemNotification::create([
                        'patient_id' => $vacina->patient_id,
                        'tipo' => 'vacina_atraso',
                        'titulo' => $titulo,
                        'mensagem' => $mensagem,
                        'icone' => 'syringe',
                        'cor' => 'warning',
                        'url' => $url,
                        'lido' => false,
                        'created_at' => $vacina->updated_at ?? now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Silently handle any exception during background sync
        }
    }

    /**
     * Criar uma notificação específica.
     */
    public static function create(array $data): SystemNotification
    {
        return SystemNotification::create([
            'user_id' => $data['user_id'] ?? null,
            'patient_id' => $data['patient_id'] ?? null,
            'tipo' => $data['tipo'] ?? 'sistema',
            'titulo' => $data['titulo'],
            'mensagem' => $data['mensagem'],
            'icone' => $data['icone'] ?? 'bell',
            'cor' => $data['cor'] ?? 'info',
            'url' => $data['url'] ?? null,
            'lido' => false,
        ]);
    }

    /**
     * Marcar uma notificação como lida.
     */
    public static function markAsRead(int $id, ?User $user = null): bool
    {
        $notification = SystemNotification::find($id);
        if ($notification) {
            $notification->marcarComoLido($user);
            return true;
        }
        return false;
    }

    /**
     * Marcar todas como lidas para o usuário.
     */
    public static function markAllAsRead(?User $user = null): int
    {
        $userId = $user?->id ?? auth()->id();
        return SystemNotification::paraUsuario($userId)
            ->naoLidos()
            ->update([
                'lido' => true,
                'lido_em' => now(),
                'lido_por' => $userId,
            ]);
    }
}

