<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Criar novas permissões de visitas domiciliárias
        $homeVisitPermissions = [
            'view_home_visits',
            'create_home_visits',
            'edit_home_visits',
            'delete_home_visits',
        ];

        foreach ($homeVisitPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Criar ou garantir os papéis (Roles)
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $medico = Role::firstOrCreate(['name' => 'Médico', 'guard_name' => 'web']);
        $enfermeiro = Role::firstOrCreate(['name' => 'Enfermeiro', 'guard_name' => 'web']);
        $agenteComunitario = Role::firstOrCreate(['name' => 'Agente Comunitário', 'guard_name' => 'web']);

        // 3. Atribuir permissões
        $admin->givePermissionTo(Permission::all());

        $medico->givePermissionTo([
            'view_home_visits', 'create_home_visits', 'edit_home_visits'
        ]);

        $enfermeiro->givePermissionTo([
            'view_home_visits', 'create_home_visits', 'edit_home_visits'
        ]);

        $agenteComunitario->syncPermissions([
            'view_patients',
            'view_home_visits', 'create_home_visits', 'edit_home_visits',
            'view_dashboard',
            'view_notifications'
        ]);

        // 4. Criar utilizador activista comunitário padrão caso não exista
        if (!User::where('email', 'activista@maternidade.mz')->exists()) {
            $activistaUser = User::create([
                'name' => 'Activista Comunitária Rosa Sitoe',
                'email' => 'activista@maternidade.mz',
                'password' => 'password',
                'email_verified_at' => now(),
                'especialidade' => 'Saúde Comunitária & Busca Ativa (APE)'
            ]);
            $activistaUser->assignRole('Agente Comunitário');
        }
    }

    public function down(): void
    {
        // Limpar cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
