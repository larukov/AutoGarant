<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'email'      => 'required|email|unique:users,email',
            'name'       => 'required|string|max:255',
            'lastname'   => 'required|string|max:255',
            'patronomic' => 'nullable|string|max:255',
            'password'   => 'required|min:6|confirmed',
            'tel'        => 'nullable|string|max:20',
        ], [
            'email.unique'         => 'Этот email уже зарегистрирован',
            'password.confirmed'   => 'Пароли не совпадают',
            'password.min'         => 'Пароль должен содержать минимум 6 символов',
        ]);

        $validateData['password'] = Hash::make($validateData['password']);

        $user = User::create($validateData);

        Auth::login($user);

        return redirect()->back()->with('toast_success', 'Добро пожаловать, ' . $user->name . '! Регистрация прошла успешно.');
    }
}
