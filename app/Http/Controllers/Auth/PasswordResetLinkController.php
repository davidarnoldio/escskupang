<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use App\Models\PasswordResetRequest;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request by notifying Admin.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        PasswordResetRequest::create([
            'user_id' => $user ? $user->id : null,
            'name' => $user ? $user->name : 'Pengguna',
            'email' => $email,
            'role' => $user ? $user->role : 'orang_tua',
            'status' => 'pending',
            'notes' => 'Permintaan reset password via form Lupa Password',
        ]);

        return back()->with('status', 'Notifikasi permintaan reset kata sandi telah dikirimkan ke Administrator Sekolah. Silakan hubungi Admin untuk mendapatkan kata sandi baru Anda.');
    }
}
