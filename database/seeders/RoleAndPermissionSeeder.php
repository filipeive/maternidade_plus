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
            'delete_exams',
            'view_dashboard',
            'manage_users'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar roles
        $admin = Role::create(['name' => 'Administrador']);
        $medico = Role::create(['name' => 'Médico']);
        $enfermeiro = Role::create(['name' => 'Enfermeiro']);

        // Atribuir permissões aos roles
        $admin->givePermissionTo(Permission::all());
        
        $medico->givePermissionTo([
            'view_patients', 'create_patients', 'edit_patients',
            'view_consultations', 'create_consultations', 'edit_consultations',
            'view_exams', 'create_exams', 'edit_exams',
            'view_dashboard'
        ]);

        $enfermeiro->givePermissionTo([
            'view_patients', 'create_patients', 'edit_patients',
            'view_consultations', 'create_consultations',
            'view_exams', 'view_dashboard'
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
