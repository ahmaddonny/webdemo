<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');
    }

    public function authenticate(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $response = Http::post(env('API_GATEWAY') . 'login', [
            'email' => $email,
            'password' => $password
        ]);

        $status = $response->status();

        if ($status === 200) {
            $responseData = $response->json('Data');

            Session::put('name', $responseData['name']);
            Session::put('email', $responseData['email']);
            Session::put('userID', $responseData['UserId']);
            Session::put('companyCd', $responseData['Company_Cd']);
            Session::put('companyName', $responseData['Company_Name']);
            Session::put('userLevel', $responseData['UserLevel']);
            Session::put('hp', $responseData['handphone']);
            Session::put('pict', $responseData['pict']);
            Session::put('rowID', $responseData['rowID']);
            Session::put('is_login', true);

            $request->session()->regenerate();

            return redirect()->intended('dash');
        } else {
            return back()->with('alert', $response->json('Pesan'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('alert', 'Already logout!');
    }
}
