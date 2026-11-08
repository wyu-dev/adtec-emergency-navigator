<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use App\Models\AssemblyPoint;
use App\Models\EmergencyAlert;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@adtec.edu.my'],
            [
                'name' => 'Pegawai Keselamatan ADTEC',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Default Assembly Point
        $assemblyPoint = AssemblyPoint::firstOrCreate(
            ['name' => 'Padang ADTEC Kuantan'],
            [
                'latitude' => 3.966437,
                'longitude' => 103.373426,
                'description' => 'Zon Berkumpul Utama - Padang Kawad / Padang Sukan ADTEC JTM Kuantan',
                'is_active' => true,
            ]
        );

        // 3. Admin Settings
        $settings = [
            'emergency_pin' => '9999',
            'vapid_public_key' => env('VAPID_PUBLIC_KEY', 'BLDJqw-Z8xd-_P932XFrAbdT223y4KkmNlh7mK5yo7hPfIy0c6uSyIrV87Uk5v7oYiG4zl_ZWQ8ZEfjyjrKQ83I'),
            'vapid_private_key' => env('VAPID_PRIVATE_KEY', null),
            'vapid_subject' => env('VAPID_SUBJECT', 'mailto:admin@adtec.edu.my'),
            'siren_sound_enabled' => '1',
        ];

        foreach ($settings as $key => $value) {
            AdminSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 4. Initial Standby Emergency Alert
        EmergencyAlert::firstOrCreate(
            ['status' => 'STANDBY'],
            [
                'type' => 'REAL',
                'severity' => 'CRITICAL',
                'title' => 'SISTEM SIAP SEDIA',
                'message' => 'Sistem amaran pengungsian berada dalam mod siap sedia (Standby). Tiada kecemasan dikesan.',
                'triggered_by' => 'System',
                'assembly_point_id' => $assemblyPoint->id,
            ]
        );
    }
}
