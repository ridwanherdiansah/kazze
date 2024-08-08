<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);    

        // Buat user baru
        try {
            User::create([
                'name' => $validated['name'],
                'username' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return redirect('/')->with('success', 'Registrasi berhasil! Silakan login.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function registrasi(Request $request){
        try {
            $title = "Registrasi";
            if (Auth::user()){
                return redirect('dashboard')->with('success', 'Anda harus logout terlebih dahulu');
            } else {
                return view('Pages.Auth.registrasi', compact('title'));
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function login(){
        try {
            $title = "Login"; 
            if(Auth::user()){
                return redirect('dashboard')->with('success','Anda Sudah Login');
            }else{
                return view('Pages.Auth.login', compact('title'));
            }   
        } catch (\Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function authenticate(Request $request){
        try {
            // Validasi Form
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ],
            [
                'email.required' => 'email harus diisi',
                'email.email' => 'inputan harus berupa email',
                'password.required' => 'password harus diisi'
            ]);
    
            // Validasi User Dari DB
            if(Auth::attempt($credentials,$request->input('remember-token'))){
                // Jika Berhasil diberi Session
                $request->session()->regenerate();
                // Diarakahkan ke dashboard
                return redirect()->intended('dashboard');
            }else{
                // Jika Gagal akan Mengembalikan Alert Error
                return back()->with('error','Gagal Login');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            // Logout
            Auth::logout();
            // Hapus sesi
            $request->session()->invalidate();
            // Regenerate Token
            $request->session()->regenerateToken();
                // Hapus "Remember Me" cookie
            if (Auth::check() && Auth::viaRemember()) {
                Auth::user()->setRememberToken(null);
                Auth::user()->save;
            }
            // Diarahkan ke login
            return redirect('login')->with('success','Berhasil Logout');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
