<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminPasswordController extends Controller
{
    /**
     * Display password reset requests for Admin.
     */
    public function index()
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        if (!$admin || !$admin->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $pendingRequests = PasswordResetRequest::where('status', 'pending')->latest()->get();
        $resolvedRequests = PasswordResetRequest::where('status', 'resolved')->latest()->take(20)->get();

        return view('admin.password_requests', compact('pendingRequests', 'resolvedRequests'));
    }

    /**
     * Reset password for any user directly by Admin.
     */
    public function reset(Request $request, User $user)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        if (!$admin || !$admin->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        $newPass = $request->input('new_password');

        $user->update([
            'password' => Hash::make($newPass),
            'plain_password' => $newPass,
        ]);

        // Resolve any pending reset requests for this user/email
        PasswordResetRequest::where('email', $user->email)
            ->where('status', 'pending')
            ->update(['status' => 'resolved', 'notes' => 'Di-reset langsung oleh Admin']);

        return redirect()->back()->with('success', "Kata sandi akun {$user->name} ({$user->email}) berhasil di-reset menjadi '{$newPass}' dan dapat langsung digunakan login.");
    }

    /**
     * Mark a password reset request as resolved.
     */
    public function resolveRequest(PasswordResetRequest $resetRequest)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        if (!$admin || !$admin->isAdmin()) {
            abort(403, 'Akses khusus Administrator.');
        }

        $resetRequest->update(['status' => 'resolved']);

        return redirect()->back()->with('success', 'Permintaan reset password telah ditandai Selesai.');
    }
}
