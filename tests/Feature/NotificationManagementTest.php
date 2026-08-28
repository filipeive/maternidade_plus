<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\Patient;
use App\Models\SystemNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $doctorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $manageAlerts = Permission::firstOrCreate(['name' => 'manage_alerts', 'guard_name' => 'web']);
        $viewDashboard = Permission::firstOrCreate(['name' => 'view_dashboard', 'guard_name' => 'web']);
        $viewPatients = Permission::firstOrCreate(['name' => 'view_patients', 'guard_name' => 'web']);

        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $medicoRole = Role::firstOrCreate(['name' => 'Médico', 'guard_name' => 'web']);

        $adminRole->givePermissionTo(Permission::all());
        $medicoRole->givePermissionTo([$manageAlerts, $viewDashboard, $viewPatients]);

        $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Notificações',
            'email' => 'admin.notif@maternidade.mz',
            'email_verified_at' => now(),
        ]);
        $this->adminUser->assignRole('Administrador');

        $this->doctorUser = User::factory()->create([
            'name' => 'Dr. Médico Notificações',
            'email' => 'medico.notif@maternidade.mz',
            'email_verified_at' => now(),
        ]);
        $this->doctorUser->assignRole('Médico');
    }

    private function createPatient(): Patient
    {
        return Patient::create([
            'nome_completo' => 'Maria Notif ' . uniqid(),
            'data_nascimento' => '1996-03-20',
            'documento_bi' => '1101' . rand(10000000, 99999999) . 'Z',
            'contacto' => '+25884' . rand(1000000, 9999999),
            'email' => 'maria.' . uniqid() . '@example.com',
            'endereco' => 'Quelimane Centro',
            'tipo_sanguineo' => 'O+',
            'data_ultima_menstruacao' => Carbon::now()->subWeeks(16)->format('Y-m-d'),
            'data_provavel_parto' => Carbon::now()->addWeeks(24)->format('Y-m-d'),
            'status_atual' => Patient::STATUS_GESTANTE,
            'numero_gestacoes' => 1,
            'numero_partos' => 0,
            'numero_abortos' => 0,
            'ativo' => true,
        ]);
    }

    public function test_user_can_access_notifications_and_sms_hub(): void
    {
        $response = $this->actingAs($this->doctorUser)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Central de Notificações & Comunicação SMS');
        $response->assertSee('Notificações do Sistema');
        $response->assertSee('Pacientes Faltosas');
    }

    public function test_api_returns_notifications_list_and_unread_count(): void
    {
        $patient = $this->createPatient();

        SystemNotification::create([
            'patient_id' => $patient->id,
            'tipo' => 'alerta_clinico',
            'titulo' => 'Alerta de Teste',
            'mensagem' => 'Mensagem de teste para a API',
            'icone' => 'triangle-exclamation',
            'cor' => 'danger',
            'url' => '/alertas',
            'lido' => false,
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('notifications.api-list'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unreadCount',
            'notifications' => [
                '*' => ['id', 'title', 'message', 'icon', 'color', 'time', 'unread', 'url']
            ]
        ]);
        $response->assertJson([
            'unreadCount' => 1,
        ]);
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $notif = SystemNotification::create([
            'tipo' => 'sistema',
            'titulo' => 'Aviso do Sistema',
            'mensagem' => 'Manutenção programada',
            'icone' => 'info',
            'cor' => 'info',
            'lido' => false,
        ]);

        $response = $this->actingAs($this->doctorUser)->patch(route('notifications.mark-read', $notif->id));
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok', 'unreadCount' => 0]);

        $this->assertDatabaseHas('system_notifications', [
            'id' => $notif->id,
            'lido' => true,
            'lido_por' => $this->doctorUser->id,
        ]);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        SystemNotification::create([
            'tipo' => 'sistema',
            'titulo' => 'Aviso 1',
            'mensagem' => 'Msg 1',
            'lido' => false,
        ]);

        SystemNotification::create([
            'tipo' => 'sistema',
            'titulo' => 'Aviso 2',
            'mensagem' => 'Msg 2',
            'lido' => false,
        ]);

        $response = $this->actingAs($this->doctorUser)->post(route('notifications.mark-all-read'));
        $response->assertRedirect();

        $this->assertEquals(0, SystemNotification::naoLidos()->count());
    }

    public function test_user_can_mark_alert_as_read(): void
    {
        $patient = $this->createPatient();
        $alerta = Alerta::create([
            'patient_id' => $patient->id,
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'PA Grave',
            'status' => Alerta::STATUS_ATIVO,
            'lido' => false,
        ]);

        // Verificamos que o badge no dashboard mostra 1
        $dash1 = $this->actingAs($this->doctorUser)->get(route('dashboard'));
        $dash1->assertViewHas('alertasAltosCount', 1);

        // Marcamos o alerta como lido
        $response = $this->actingAs($this->doctorUser)->post(route('alertas.marcar-lido', $alerta->id));
        $response->assertRedirect();

        $this->assertDatabaseHas('alertas', [
            'id' => $alerta->id,
            'lido' => true,
        ]);

        // Agora o badge de alertas altos não lidos deve ser 0
        $dash2 = $this->actingAs($this->doctorUser)->get(route('dashboard'));
        $dash2->assertViewHas('alertasAltosCount', 0);
    }

    public function test_resolving_alert_marks_it_as_read_and_decrements_badge(): void
    {
        $patient = $this->createPatient();
        $alerta = Alerta::create([
            'patient_id' => $patient->id,
            'tipo' => 'pressao_arterial_grave',
            'nivel' => Alerta::NIVEL_ALTO,
            'mensagem' => 'PA Grave Crise',
            'status' => Alerta::STATUS_ATIVO,
            'lido' => false,
        ]);

        $response = $this->actingAs($this->doctorUser)->post("/alertas/{$alerta->id}/resolver", [
            'status' => Alerta::STATUS_RESOLVIDO,
            'nota' => 'Medicação anti-hipertensiva administrada e estabilizada.',
        ]);
        $response->assertRedirect();

        $alerta->refresh();
        $this->assertEquals(Alerta::STATUS_RESOLVIDO, $alerta->status);
        $this->assertTrue((bool)$alerta->lido);

        $dash = $this->actingAs($this->doctorUser)->get(route('dashboard'));
        $dash->assertViewHas('alertasAltosCount', 0);
    }
}
