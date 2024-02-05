<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AccountProfileController extends Controller
{
    public function index()
    {
        return view('account_profile.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $userId = Auth()->user()->user_id;
        $confpass = Hash::make($data['confirmpassword']);

        $users = User::where('user_id', '=', $userId)->first();

        if (!is_null($users)) {
            $data_update = array(
                'password' => $confpass
            );

            $update = User::where('user_id', '=', $userId)->update($data_update);

            if ($update) {
                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect('/')->with('succes_change', 'Successfully changed the password, Please Log In Again.');
            } else {
                return back()->with('alert', $update);
            }
        } else {
            return back()->with('alert', 'User not found!');
        }
    }
}
