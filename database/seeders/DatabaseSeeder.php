<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
        RoleAndPermissionSeeder::class,  // Primeiro cria as roles
        UserSeeder::class,                // Depois cria usuários que usam essas roles
        PatientSeeder::class,
        HomeVisitSeeder::class,
        ConsultationSeeder::class,
        ExamSeeder::class,
        VaccineSeeder::class,
        AlertaSeeder::class,
    ]);

    }
}
