<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function loginEmail(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))
                ->with('status', 'Log masuk berjaya sebagai Pegawai Keselamatan.');
        }

        return back()->withErrors([
            'email' => 'Emel atau kata laluan yang dimasukkan tidak sah.',
        ])->withInput($request->only('email'));
    }

    public function loginPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string'],
        ]);

        $correctPin = AdminSetting::get('emergency_pin', '9999');

        if ($request->input('pin') === $correctPin) {
            // Find or use admin user
            $admin = User::first() ?? User::create([
                'name' => 'Pegawai Keselamatan (PIN)',
                'email' => 'admin@adtec.edu.my',
                'password' => Hash::make('password123'),
            ]);

            Auth::login($admin);
            $request->session()->regenerate();
            $request->session()->put('login_via', 'PIN');

            return redirect()->route('admin.dashboard')
                ->with('status', 'Akses Pantas Kod PIN berjaya disahkan.');
        }

        return back()->withErrors([
            'pin' => 'Kod PIN Kecemasan tidak tepat. Sila cuba lagi.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda telah log keluar daripada sistem.');
    }
}

