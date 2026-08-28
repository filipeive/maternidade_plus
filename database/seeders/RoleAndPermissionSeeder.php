<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Criar permissões
        $permissions = [
            'view_patients',
            'create_patients',
            'edit_patients',
            'delete_patients',
            'view_consultations',
            'create_consultations',
            'edit_consultations',
            'delete_consultations',
            'view_exams',
            'view_births',
            'create_births',
            'edit_births',
            'delete_births',
            'view_vaccines',
            'create_vaccines',
            'edit_vaccines',
            'delete_vaccines',
            'view_laboratory',
            'create_laboratory',
            'edit_laboratory',
            'delete_laboratory',
            'view_reports',
            'create_reports',
            'edit_reports',
            'delete_reports',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
            'view_dashboard',
            'manage_settings',
            'view_notifications',
            'manage_notifications',
            'create_exams',
            'edit_exams',
            'view_dashboard',
            'manage_users',
            'manage_alerts',
            'view_alerts'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar roles
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $medico = Role::firstOrCreate(['name' => 'Médico', 'guard_name' => 'web']);
        $enfermeiro = Role::firstOrCreate(['name' => 'Enfermeiro', 'guard_name' => 'web']);
        $laboratorista = Role::firstOrCreate(['name' => 'Laboratorista', 'guard_name' => 'web']);

        // Atribuir permissões aos roles
        $admin->syncPermissions(Permission::all());
        
        $medico->syncPermissions([
            'view_patients', 'create_patients', 'edit_patients',
            'view_consultations', 'create_consultations', 'edit_consultations',
            'view_exams', 'create_exams', 'edit_exams',
            'view_births', 'create_births', 'edit_births',
            'view_vaccines', 'view_dashboard', 'manage_alerts', 'view_alerts',
            'view_notifications', 'manage_notifications'
        ]);

        $enfermeiro->syncPermissions([
            'view_patients', 'create_patients', 'edit_patients',
            'view_consultations', 'create_consultations',
            'view_exams', 'view_births', 'create_births',
            'view_vaccines', 'create_vaccines', 'edit_vaccines',
            'view_dashboard', 'view_alerts',
            'view_notifications', 'manage_notifications'
        ]);

        $laboratorista->syncPermissions([
            'view_laboratory', 'create_laboratory', 'edit_laboratory',
            'view_exams', 'create_exams', 'edit_exams',
            'view_dashboard'
        ]);

        // Criar usuário admin padrão (se não existir)
        if (!User::where('email', 'admin@maternidade.mz')->exists()) {
            $adminUser = User::create([
                'name' => 'Administrador MISAU',
                'email' => 'admin@maternidade.mz',
                'password' => 'password',
                'email_verified_at' => now(),
                'especialidade' => 'Gestão de Saúde'
            ]);
            $adminUser->assignRole('Administrador');
        }

        // Criar utilizador médico exemplo (se não existir)
        if (!User::where('email', 'medico@maternidade.mz')->exists()) {
            $medicoUser = User::create([
                'name' => 'Dr. João Machel',
                'email' => 'medico@maternidade.mz',
                'password' => 'password',
                'email_verified_at' => now(),
                'especialidade' => 'Obstetrícia e Ginecologia'
            ]);
            $medicoUser->assignRole('Médico');
        }

        // Criar utilizador enfermeira exemplo (se não existir)
        if (!User::where('email', 'enfermeira@maternidade.mz')->exists()) {
            $enfermeiraUser = User::create([
                'name' => 'Enf. Maria Eugenia Simbine',
                'email' => 'enfermeira@maternidade.mz',
                'password' => 'password',
                'email_verified_at' => now(),
                'especialidade' => 'Enfermagem de Saúde Materno-Infantil'
            ]);
            $enfermeiraUser->assignRole('Enfermeiro');
        }
    }
}
