<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\AssemblyPoint;
use App\Models\EmergencyAlert;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class EvacuationController extends Controller
{
    public function index()
    {
        $activeAlert = EmergencyAlert::getActiveAlert();
        $assemblyPoint = AssemblyPoint::where('is_active', true)->first()
            ?? AssemblyPoint::first();

        // Default fallback if no assembly point in DB
        if (!$assemblyPoint) {
            $assemblyPoint = new AssemblyPoint([
                'name' => 'Padang ADTEC Kuantan',
                'latitude' => 3.966437,
                'longitude' => 103.373426,
                'description' => 'Zon Berkumpul Utama',
            ]);
        }

        $vapidPublicKey = AdminSetting::get('vapid_public_key');
        $sirenEnabled = AdminSetting::get('siren_sound_enabled', '1') === '1';

        return view('client.navigator', compact('activeAlert', 'assemblyPoint', 'vapidPublicKey', 'sirenEnabled'));
    }

    public function status()
    {
        $activeAlert = EmergencyAlert::getActiveAlert();
        $assemblyPoint = AssemblyPoint::where('is_active', true)->first()
            ?? AssemblyPoint::first();

        return response()->json([
            'is_emergency' => $activeAlert !== null,
            'alert' => $activeAlert ? [
                'id' => $activeAlert->id,
                'title' => $activeAlert->title,
                'message' => $activeAlert->message,
                'type' => $activeAlert->type,
                'severity' => $activeAlert->severity,
                'triggered_at' => $activeAlert->triggered_at?->toIso8601String(),
            ] : null,
            'assembly_point' => [
                'name' => $assemblyPoint?->name ?? 'Padang ADTEC Kuantan',
                'latitude' => (float) ($assemblyPoint?->latitude ?? 3.966437),
                'longitude' => (float) ($assemblyPoint?->longitude ?? 103.373426),
                'description' => $assemblyPoint?->description ?? 'Zon Berkumpul ADTEC Kuantan',
            ],
            'timestamp' => now()->timestamp,
        ]);
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
            'keys.p256dh' => ['nullable', 'string'],
            'keys.auth' => ['nullable', 'string'],
            'encoding' => ['nullable', 'string'],
        ]);

        $sub = PushSubscription::updateOrCreate(
            ['endpoint' => $validated['endpoint']],
            [
                'public_key' => $validated['keys']['p256dh'] ?? null,
                'auth_token' => $validated['keys']['auth'] ?? null,
                'content_encoding' => $validated['encoding'] ?? 'aes128gcm',
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Langganan notifikasi kecemasan berjaya didaftarkan.',
            'id' => $sub->id,
        ]);
    }

    public function vapidPublicKey()
    {
        return response()->json([
            'publicKey' => AdminSetting::get('vapid_public_key'),
        ]);
    }
}
