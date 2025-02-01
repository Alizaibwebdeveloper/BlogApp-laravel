<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\UserStatus;
use App\UserType;


class AuthController extends Controller
{
    public function loginForm(Request $request)
    {
        $data = [
            'PageTitle' => 'Login'
        ];
        return view('back.layout.pages.auth.login', $data);
    }

    public function forgotPasswordForm(Request $request)
    {
        $data = [
            'PageTitle' => 'Forgot Password'
        ];
        return view('back.layout.pages.auth.forgot-password', $data);
    }

    public function loginHandler(Request $request)
    {

        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if ($fieldType == 'email') {
            $request->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required|min:5'
            ],[
                'login_id.required' => 'Enter your email or username',
                'login_id.email' => 'Enter a valid email',
                'login_id.exists' => 'No account found with this email',
            ]);
        } else {
            $request->validate([
                'login_id' => 'required|exists:users,username',
                'password' => 'required|min:5'
            ],[
                'login_id.required' => 'Enter your email or username',
                'login_id.exists' => 'No account found for this username',
            ]);
        }
        $credentials = [
            $fieldType => $request->login_id,
            'password' => $request->password
        ];

       if (Auth::attempt($credentials)) {
    $user = Auth::user();

    // Check if the user is active
    if ($user->status == UserStatus::Active) {
        return redirect()->route('admin.dashboard');
    }


    // Check if the user's account is inactive
    if ($user->status == UserStatus::Inactive) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('error', 'Your account is currently inactive. Please contact support at (support@larablog.test) for further assistance.');
  
    }
    return redirect()->route('admin.login')->with('error', 'Invalid login credentials.');
    // Check if the user's account is pending
    if ($user->status == UserStatus::Pending) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('error', 'Your account is currently pending approval. Please check your email for more information.');
    }

    

}

}
}



