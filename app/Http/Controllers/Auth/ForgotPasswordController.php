<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;


class ForgotPasswordController extends Controller
{

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'email.exists'   => 'Email tidak terdaftar',
        ]);

        $token = Password::createToken(
            \App\Models\User::where('email', $request->email)->first()
        );

        Mail::to($request->email)->send(
            new ResetPasswordMail($token, $request->email)
        );

        return back()->with('status', 'Link reset password berhasil dikirim ke email Anda');
    }
}
