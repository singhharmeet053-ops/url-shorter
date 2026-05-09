<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    public function Login()
    {
        return view('login');
    }

    public function Loggedin(Request $request)
    {
        //echo "<pre>";print_r($request->all());die();
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials))
        {
            $request->session()->regenerate();

            if(Auth::user()->role == 'superadmin')
            {
                return redirect()->route('superadmin.dashboard');
            }
            elseif(Auth::user()->role == 'admin')
            {
                return redirect()->route('admin.dashboard');
            }
            elseif(Auth::user()->role == 'member')
            {
                return redirect()->route('member.dashboard');
            }
            else{
                Auth::logout();
                return redirect()->route('login')->with('error', 'You are logged out');
            }
        }
        else{

            return redirect()->route('login')->with('error','Invalid email or password');
        }
    }

    public function Logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}