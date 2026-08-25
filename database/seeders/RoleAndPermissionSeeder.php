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

        // Atribuir permissões aos roles
        $admin->syncPermissions(Permission::all());
        
        $medico->syncPermissions([
            'view_patients', 'create_patients', 'edit_patients',
            'view_consultations', 'create_consultations', 'edit_consultations',
            'view_exams', 'create_exams', 'edit_exams',
            'view_dashboard', 'manage_alerts', 'view_alerts'
        ]);

        $enfermeiro->syncPermissions([
            'view_patients', 'create_patients', 'edit_patients',
            'view_consultations', 'create_consultations',
            'view_exams', 'view_dashboard', 'view_alerts'
        ]);

        // Criar usuário admin padrão
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@maternidade.mz',
            'password' => bcrypt('admin123'),
            'email_verified_at' => now()
        ]);
        $adminUser->assignRole('Administrador');

        // Criar usuário médico exemplo
        $medico = User::create([
            'name' => 'Dr. João Machel',
            'email' => 'medico@maternidade.mz',
            'password' => bcrypt('medico123'),
            'email_verified_at' => now()
        ]);
        $medico->assignRole('Médico');
    }
}
