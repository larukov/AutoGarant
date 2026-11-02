<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->back()->with('toast_success', 'С возвращением, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ], 'login');
    }
}
