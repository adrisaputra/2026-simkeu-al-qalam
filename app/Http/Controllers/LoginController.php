<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::user() == TRUE) {
            return redirect('/dashboard');
        } else {
            return view('auth.login');
        }
    }

    public function authenticate(Request $request)
    {
        
        $request->validate([
            'name' => 'required',
            'password' => 'required',
            'cf-turnstile-response' => 'required',
        ], [
            'cf-turnstile-response.required' => 'Silakan selesaikan verifikasi keamanan.'
        ]);

        // Verifikasi Cloudflare Turnstile
        $response = Http::asForm()->post(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            [
                'secret'   => config('services.turnstile.secret_key'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]
        );

        $turnstile = $response->json();

        if (!($turnstile['success'] ?? false)) {
            return back()
                ->withInput()
                ->withErrors([
                    'captcha' => 'Verifikasi keamanan gagal. Silakan coba lagi.'
                ]);
        }

        $user = User::whereHas('group.group_application', function($query){
                $query->where('application_id',3);
            })
            ->whereIn('group_id',[6])
            ->where(function ($query) use ($request) {
                $query->where('name', $request->name)
                    ->orWhere('email', $request->name);
            })
            ->first();

        if (!$user) {
            return back()->with('status2', 'User Tidak Terdaftar!');
        }

        if ($user->status != 'Active') {
            return back()->with('status2', 'User Tidak Aktif, Silahkan Hubungi Admin!');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('status2', 'Nama User atau Password Tidak Sesuai!');
        }

        $remember = $request->boolean('remember');

        Auth::login($user, $remember);

        $request->session()->regenerate();

        activity()->log('Login');

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        activity()->log('Log Out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'Terimakasih!');
    }
}
