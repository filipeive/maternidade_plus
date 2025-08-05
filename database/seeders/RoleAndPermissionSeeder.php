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
            'create_exams',
            'edit_exams',
            'delete_exams',
            'view_dashboard',
            'manage_users'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
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
