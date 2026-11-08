<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\AssemblyPoint;
use App\Models\EmergencyAlert;
use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected WebPushService $webPush;

    public function __construct(WebPushService $webPush)
    {
        $this->webPush = $webPush;
    }

    public function dashboard()
    {
        $activeAlert = EmergencyAlert::getActiveAlert();
        $assemblyPoints = AssemblyPoint::orderBy('is_active', 'desc')->get();
        $recentAlerts = EmergencyAlert::with('assemblyPoint')->latest()->take(10)->get();
        $subscriberCount = PushSubscription::count();
        $emergencyPin = AdminSetting::get('emergency_pin', '9999');
        $sirenEnabled = AdminSetting::get('siren_sound_enabled', '1');

        $activeAssemblyPoint = AssemblyPoint::where('is_active', true)->first()
            ?? $assemblyPoints->first();

        return view('admin.dashboard', compact(
            'activeAlert',
            'assemblyPoints',
            'activeAssemblyPoint',
            'recentAlerts',
            'subscriberCount',
            'emergencyPin',
            'sirenEnabled'
        ));
    }

    public function triggerAlert(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:REAL,DRILL'],
            'severity' => ['required', 'in:CRITICAL,WARNING,INFO'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'assembly_point_id' => ['required', 'exists:assembly_points,id'],
        ]);

        // Resolve any existing active alerts first
        EmergencyAlert::where('status', 'ACTIVE')->update([
            'status' => 'SUPERSEDED',
            'resolved_at' => now(),
        ]);

        $triggeredBy = Auth::user() ? Auth::user()->name : 'Pegawai Keselamatan';
        if (session('login_via') === 'PIN') {
            $triggeredBy .= ' (Akses PIN Pantas)';
        }

        $alert = EmergencyAlert::create([
            'status' => 'ACTIVE',
            'type' => $validated['type'],
            'severity' => $validated['severity'],
            'title' => $validated['title'],
            'message' => $validated['message'],
            'assembly_point_id' => $validated['assembly_point_id'],
            'triggered_by' => $triggeredBy,
            'triggered_at' => now(),
        ]);

        // Set the chosen assembly point as active
        AssemblyPoint::query()->update(['is_active' => false]);
        AssemblyPoint::where('id', $validated['assembly_point_id'])->update(['is_active' => true]);

        // Broadcast Web Push to all devices
        $pushed = $this->webPush->sendNotification(
            title: '🚨 ' . $alert->title,
            message: $alert->message,
            url: route('evacuation.client'),
            extra: [
                'alert_id' => $alert->id,
                'type' => $alert->type,
                'assembly_point' => $alert->assemblyPoint?->name,
            ]
        );

        $msg = "AMARAN KECEMASAN TELAH DIAKTIFKAN!";
        if ($pushed > 0) {
            $msg .= " Web Push dihantar ke {$pushed} peranti berdaftar.";
        }

        return redirect()->route('admin.dashboard')->with('status_emergency', $msg);
    }

    public function resolveAlert(Request $request)
    {
        $activeAlerts = EmergencyAlert::where('status', 'ACTIVE')->get();

        foreach ($activeAlerts as $alert) {
            $alert->update([
                'status' => 'ALL_CLEAR',
                'resolved_at' => now(),
            ]);
        }

        // Send All-Clear Push notification
        $pushed = $this->webPush->sendNotification(
            title: '🟢 KEADAAN SELAMAT (ALL CLEAR)',
            message: 'Amaran kecemasan telah ditamatkan oleh pegawai keselamatan. Keadaan kembali terkawal.',
            url: route('evacuation.client')
        );

        return redirect()->route('admin.dashboard')
            ->with('status', 'Amaran kecemasan telah berjaya dibatalkan/ditamatkan (All Clear).');
    }

    public function storeAssemblyPoint(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['is_active'])) {
            AssemblyPoint::query()->update(['is_active' => false]);
        }

        AssemblyPoint::create([
            'name' => $validated['name'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'description' => $validated['description'] ?? null,
            'is_active' => !empty($validated['is_active']),
        ]);

        return redirect()->route('admin.dashboard')
            ->with('status', 'Titik Zon Berkumpul baharu berjaya ditambah.');
    }

    public function updateAssemblyPoint(Request $request, AssemblyPoint $point)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['is_active'])) {
            AssemblyPoint::where('id', '!=', $point->id)->update(['is_active' => false]);
        }

        $point->update([
            'name' => $validated['name'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'description' => $validated['description'] ?? null,
            'is_active' => !empty($validated['is_active']),
        ]);

        return redirect()->route('admin.dashboard')
            ->with('status', "Titik Berkumpul '{$point->name}' berjaya dikemaskini.");
    }

    public function setActiveAssemblyPoint(AssemblyPoint $point)
    {
        AssemblyPoint::query()->update(['is_active' => false]);
        $point->update(['is_active' => true]);

        return redirect()->route('admin.dashboard')
            ->with('status', "Zon aktif ditukar kepada: '{$point->name}'.");
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'emergency_pin' => ['required', 'string', 'min:4', 'max:10'],
            'siren_sound_enabled' => ['nullable', 'boolean'],
        ]);

        AdminSetting::set('emergency_pin', $validated['emergency_pin']);
        AdminSetting::set('siren_sound_enabled', !empty($validated['siren_sound_enabled']) ? '1' : '0');

        return redirect()->route('admin.dashboard')
            ->with('status', 'Konfigurasi sistem keselamatan berjaya dikemaskini.');
    }
}
