<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
   
    public function showResetForm($token)
    {
        return view('auth.reset-password', [
            'token' => $token
        ]);
    }

    public function reset(Request $request)
    {

        $messages = [
            'token.required'    => 'Token reset password tidak valid',
            'email.required'    => 'Email wajib diisi',
            'email.email'       => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min'      => 'Password minimal 6 karakter',
            'password.confirmed'=> 'Konfirmasi password tidak cocok',
        ];

        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], $messages);

        
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenData) {
            return back()->withErrors([
                'email' => 'Link reset password tidak valid atau sudah kedaluwarsa'
            ]);
        }

        $createdAt = Carbon::parse($tokenData->created_at);
        $expiredAt = $createdAt->addMinutes(10);

        if (now()->greaterThan($expiredAt)) {

            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return back()->withErrors([
                'email' => 'Link reset password sudah kedaluwarsa (10 menit)'
            ]);
        }

 
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {

                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('success', 'Password berhasil direset. Silakan login kembali');
        }

        return back()->withErrors([
            'email' => 'Link reset password tidak valid atau sudah kedaluwarsa'
        ]);
    }
}