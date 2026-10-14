<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('backend.dashboard');
        }

        return view('backend.auth.login');
    }

    public function showForgotPassword(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('backend.dashboard');
        }

        return view('backend.auth.login', [
            'authMode' => 'forgot',
        ]);
    }

    public function showResetPassword(string $token, Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('backend.dashboard');
        }

        $email = (string) $request->query('email');

        abort_unless(filter_var($email, FILTER_VALIDATE_EMAIL), 404);

        $reset = DB::table('password_reset_tokens')->where('email', $email)->first();

        abort_unless(
            $reset
            && Hash::check($token, $reset->token)
            && Carbon::parse($reset->created_at)->gt(now()->subMinutes(60)),
            404
        );

        return view('backend.auth.login', [
            'authMode' => 'reset',
            'resetEmail' => $email,
            'resetToken' => $token,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $credentials['status'] = 'active';

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('backend.dashboard');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($data['email']));
        $user = User::where('email', $email)->where('status', 'active')->first();

        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert([
                'email' => $email,
            ], [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            Mail::send('emails.password-reset', [
                'user' => $user,
                'resetUrl' => route('password.reset', ['token' => $token, 'email' => $email]),
                'logoUrl' => url('frontend/assets/images/logo/logo-transperent.png'),
            ], function ($message) use ($user): void {
                $message->to($user->email, $user->name)
                    ->subject('Reset your Majix password');
            });
        }

        return back()->with('status', 'If this email exists, a password reset link has been sent.');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $email = strtolower(trim($data['email']));
        $reset = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (
            ! $reset
            || ! Hash::check($data['token'], $reset->token)
            || Carbon::parse($reset->created_at)->lte(now()->subMinutes(60))
        ) {
            throw ValidationException::withMessages([
                'email' => 'This password reset link is invalid or expired.',
            ]);
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->forceFill([
            'password' => $data['password'],
        ])->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully. You can sign in now.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
