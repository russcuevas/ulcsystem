<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function LoginPage()
    {
        return view('auth.login');
    }

    public function LoginRequest(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $admin = DB::table('admins')->where('email', $email)->first();

        if ($admin && Hash::check($password, $admin->password)) {
            Session::put('user', $admin);
            Session::put('role', 'admin');

            return redirect()->route('admin.dashboard.page')
                ->with('success', 'Welcome back, Admin!');
        }

        $secretary = DB::table('secretaries')->where('email', $email)->first();

        if ($secretary && Hash::check($password, $secretary->password)) {
            Session::put('user', $secretary);
            Session::put('role', 'secretary');

            return redirect()->route('secretary.dashboard.page')
                ->with('success', 'Welcome back, Secretary!');
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function LogoutRequest()
    {
        Session::flush();
        return redirect('/login')->with('success', 'Logged out successfully');
    }
}
