<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function registration() {
        return view('user.register');
    }

    function login(Request $request) {
        $user = ['name' => $request->input('name'),
                 'password' => $request->input('password')];

        Auth::attempt($user);

        return redirect('/');
    }

    function logout() {
        Auth::logout();

        return redirect('/');
    }

    function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required',
            'password' => 'required|min:5|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/'
        ],

        $messages = [
            'regex' => 'Make sure your password has a capital letter, a lower case letter and a number.'
        ]);

        User::create([
            'name' => $validatedData['name'],
            'password' => Hash::make($validatedData['password'])
        ]);

        return redirect('/');
    }
}
