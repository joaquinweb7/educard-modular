<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Plugin;
use App\Models\Semester;
use App\Models\StudentCodeSequence;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@educard.test'],
            ['name' => 'Administrador', 'password' => Hash::make('password'), 'role' => 'super_admin']
        );

        foreach (['Mecánica Automotriz', 'Contaduría General', 'Sistemas Informáticos'] as $career) {
            Career::firstOrCreate(['name' => $career], ['status' => 'active']);
        }

        for ($i = 1; $i <= 6; $i++) {
            Semester::firstOrCreate(['number' => $i], ['name' => $i.'° Semestre', 'status' => 'active']);
        }

        StudentCodeSequence::firstOrCreate(['prefix' => '202602'], ['last_number' => 99]);

        Plugin::updateOrCreate(
            ['name' => 'VerificationQr'],
            [
                'display_name' => 'Verificación QR',
                'description' => 'Plugin de ejemplo para verificación de carnets por QR.',
                'version' => '1.0.0',
                'author' => 'EduCard',
                'provider' => 'Plugins\\VerificationQr\\VerificationQrServiceProvider',
                'path' => 'plugins/VerificationQr',
                'status' => 'inactive',
                'installed_at' => now(),
            ]
        );
    }
}
