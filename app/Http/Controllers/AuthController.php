<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginPage(){
        return view('adminlte::auth.login');
    }

    public function doLogin(Request $request){
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $remember = $request->get('remember');

        if(!Auth::attempt(['email' => $request->username, 'password' => $request->password], $remember)){
            if(!Auth::attempt(['username' => $request->username, 'password' => $request->password], $remember))
                return redirect('/')->withErrors(['username' => 'Invalid credentials','password' => 'Invalid credentials']);
        }

        return redirect()->route('showDashboard');

    }

    public function doLogout(){
        Auth::logout();
    }
}
