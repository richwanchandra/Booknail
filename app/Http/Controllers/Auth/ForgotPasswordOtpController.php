<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ForgotPasswordOtpController extends Controller
{
    /**
     * Display the OTP request view.
     */
    public function showRequestForm(): View
    {
        return view('auth.forgot-password-otp');
    }

    /**
     * Generate and send OTP to user's email.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Alamat email ini tidak terdaftar dalam sistem kami.',
        ]);

        // Generate 6 digit OTP
        $otp = (string) rand(100000, 999999);

        // Update or insert OTP record into database
        DB::table('password_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Send OTP email
        Mail::to($request->email)->send(new SendOtpMail($otp));

        return redirect()->route('password.otp.verify', ['email' => $request->email])
            ->with('status', 'Kode OTP 6-digit telah dikirimkan ke email Anda. Berlaku selama 10 menit.');
    }

    /**
     * Display the OTP verification and reset form.
     */
    public function showVerifyForm(Request $request): View
    {
        return view('auth.verify-otp', [
            'email' => $request->query('email', old('email')),
        ]);
    }

    /**
     * Verify OTP and reset user's password.
     */
    public function verifyAndReset(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'otp.size' => 'Kode OTP harus berjumlah 6 digit.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
        ]);

        // Verify OTP code and expiry
        $record = DB::table('password_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $record) {
            return back()->withInput($request->only('email', 'otp'))
                ->withErrors(['otp' => 'Kode OTP salah atau telah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Update user password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete used OTP
        DB::table('password_otps')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru.');
    }
}
