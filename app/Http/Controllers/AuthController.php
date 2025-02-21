<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Helpers\CMail;
use Illuminate\Support\Facades\Hash;

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
        return view('back.layout.pages.auth.forgot', $data);
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

    

}  // End of Auth::attempt


}
public function setPasswordResetLink(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email'
    ], [
        'email.email' => 'Enter a valid email',
        'email.exists' => 'We cannot find a user with this email'
    ]);

    $user = User::where('email', $request->email)->first();
    $token = Str::random(64);

    // Store the token in password_reset_tokens table
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $token, 'created_at' => Carbon::now()]
    );

    // Generate the password reset link
    $actionLink = route('admin.reset-password', ['token' => $token]);

    // Prepare email data
    $data = [
        'actionlink' => $actionLink,
        'name' => $user->name,
    ];

    // Render email template
    $mailBody = view('email-templates.forgot-template', $data)->render();

    // Configure email
    $mailConfig = [
        'to' => [
            'address' => $user->email,
            'name' => $user->name
        ],
        'subject' => 'Password Reset',
        'body' => $mailBody
    ];

    // Send email
    if (CMail::send($mailConfig)) {
        return redirect()->route('admin.login')->with('success', 'We have sent a password reset link to your email. Please check your inbox.');
    } else {
        return redirect()->route('admin.forgot')->with('error', 'Failed to send password reset link. Please try again later.');
    }
}  // End of setPasswordResetLink






public function resetPasswordForm($token)
{
    // Check if token exists in password_reset_tokens table
    $tokenData = DB::table('password_reset_tokens')->where('token', $token)->first();

    if (!$tokenData) {
        return redirect()->route('admin.login')->with('error', 'Invalid password reset token. Please try again.');
    }

    // Check if token is expired (expires after 1 minute)
    $expirationTime = Carbon::parse($tokenData->created_at)->addMinutes(1);
    if (Carbon::now()->gt($expirationTime)) {
        // Delete expired token from the database
        DB::table('password_reset_tokens')->where('token', $token)->delete();

        // Redirect to login page instead of reset-password page to avoid loop
        return redirect()->route('admin.login')->with('error', 'Token has expired. Please request a new password reset link.');
    }

    // Return reset password view
    $data = [
        'PageTitle' => 'Reset Password',
        'token' => $token
    ];
    return view('back.layout.pages.auth.reset', $data);
}

 // End of resetPasswordForm



public function resetPasswordHandler(Request $request){
    $request->validate([
        'new_password' => 'required|min:5|required_with:new_password_confirmation|same:new_password_confirmation',
        'new_password_confirmation' => 'required'
    ]);

    // Check if token exists
    $tokenData = DB::table('password_reset_tokens')->where('token', $request->token)->first();
    if (!$tokenData) {
        return redirect()->route('admin.reset-password', ['token' => $request->token])
            ->with('error', 'Invalid or expired token.');
    }

    // Check if user exists
    $user = User::where('email', $tokenData->email)->first();
    if (!$user) {
        return redirect()->route('admin.reset-password', ['token' => $request->token])
            ->with('error', 'User not found.');
    }

    // Update the user's password
    $user->password = Hash::make($request->new_password);
    $user->save();

    // Send email notification to user
    $data = [
        'name' => $user->name,
        'email' => $user->email,
        'new_password' => $request->new_password // Pass new password
    ];

    // Render email template
    $mailBody = view('email-templates.password-changes', $data)->render();

    // Configure email
    $mailConfig = [
        'to' => [
            'address' => $user->email,
            'name' => $user->name
        ],
        'subject' => 'Password Reset Success',
        'body' => $mailBody
    ];

    // Send email
    if (CMail::send($mailConfig)) {
        // Delete the token from password_reset_tokens table
        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return redirect()->route('admin.login')->with('success', 'Done!, Ypur Password changed  successfully. Use your new password to login into system.');
    } else {
        return redirect()->route('admin.reset-password', ['token' => $request->token])
            ->with('error', 'Failed to reset password. Please try again later.');
    }
}




}



