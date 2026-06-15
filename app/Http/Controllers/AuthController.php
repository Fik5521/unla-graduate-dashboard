<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog; // <-- PENTING: Tambahkan ini di atas

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login'); // Sesuaikan dengan nama view login kamu
    }

    // Memproses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- CATAT LOG LOGIN ---
            AuditLog::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'aksi' => 'Login',
                'keterangan' => 'Pengguna berhasil login ke dalam sistem.'
            ]);

            // Redirect ke halaman dashboard
            return redirect()->intended('/'); 
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        // --- CATAT LOG LOGOUT SEBELUM SESI DIHAPUS ---
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'aksi' => 'Logout',
                'keterangan' => 'Pengguna keluar dari sistem.'
            ]);
        }

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login-admin');
    }
}