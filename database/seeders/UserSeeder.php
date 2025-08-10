<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrador',
                'email' => 'admin@maternidade.mz',
                'password' => 'password',
                'role' => 'Administrador'
            ],
            [
                'name' => 'Dr. João Silva',
                'email' => 'medico@maternidade.mz',
                'password' => 'password',
                'role' => 'Médico'
            ],
            [
                'name' => 'Enf. Maria Costa',
                'email' => 'enfermeiro@maternidade.mz',
                'password' => 'password',
                'role' => 'Enfermeiro'
            ],
        ];

        foreach ($users as $user) {
            $existing = DB::table('users')->where('email', $user['email'])->first();
            if (!$existing) {
                $newUser = User::create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make($user['password']),
                ]);
                $newUser->assignRole($user['role']);
            }
        }
    }
}
