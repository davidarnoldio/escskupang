<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Switch context to a target teacher account (Admin Only).
     */
    public function switch(Request $request, User $teacher)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Strict Check: Only Admin or currently impersonating user can switch accounts
        if (!$currentUser || (!$currentUser->isAdmin() && !session()->has('impersonated_by'))) {
            abort(403, 'Akses khusus Administrator Utama.');
        }

        // Only allow switching to a teacher user
        if (!$teacher->isTeacher()) {
            return redirect()->back()->with('error', 'Hanya dapat switch ke akun Guru / Wali Kelas.');
        }

        // Store original Admin ID in session if not already in impersonation mode
        if (!session()->has('impersonated_by')) {
            session(['impersonated_by' => $currentUser->id]);
        }

        // Log in as target teacher
        Auth::login($teacher);

        $assignedClass = $teacher->getAssignedClass() ?? 'Semua Kelas';
        return redirect()->route('dashboard')->with('success', "Mode Switch Guru: {$teacher->name} (Wali Kelas {$assignedClass}).");
    }

    /**
     * Leave teacher mode and switch back to main Admin account.
     */
    public function leave(Request $request)
    {
        if (!session()->has('impersonated_by')) {
            abort(403, 'Anda tidak sedang dalam mode switch akun.');
        }

        $adminId = session('impersonated_by');
        session()->forget('impersonated_by');

        $adminUser = User::find($adminId);
        if ($adminUser) {
            Auth::login($adminUser);
            return redirect()->route('dashboard')->with('success', 'Berhasil kembali ke Akun Administrator Utama.');
        }

        Auth::logout();
        return redirect()->route('login');
    }
}
