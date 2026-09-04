<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display payments for Admin (management & review).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Admin.');
        }

        $query = Payment::with('student')->latest();

        if ($request->filled('kelas')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15)->withQueryString();
        $classes = Student::OFFICIAL_CLASSES;
        $students = Student::orderBy('nama')->get();

        $stats = [
            'total_tagihan' => Payment::count(),
            'belum_lunas' => Payment::where('status', 'belum_lunas')->count(),
            'menunggu_konfirmasi' => Payment::where('status', 'menunggu_konfirmasi')->count(),
            'lunas' => Payment::where('status', 'lunas')->count(),
        ];

        return view('payments.admin-index', compact('payments', 'classes', 'students', 'stats'));
    }

    /**
     * Store new payment notification bill (per student or all students in a class).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Admin.');
        }

        $validated = $request->validate([
            'target_type' => 'required|in:student,class',
            'student_id' => 'nullable|required_if:target_type,student|exists:students,id',
            'kelas' => 'nullable|required_if:target_type,class|in:' . implode(',', Student::OFFICIAL_CLASSES),
            'judul' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        if ($validated['target_type'] === 'student') {
            Payment::create([
                'student_id' => $validated['student_id'],
                'judul' => $validated['judul'],
                'jumlah' => $validated['jumlah'],
                'jatuh_tempo' => $validated['jatuh_tempo'],
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => 'belum_lunas',
            ]);
            $count = 1;
        } else {
            $students = Student::where('kelas', $validated['kelas'])->get();
            if ($students->isEmpty()) {
                return redirect()->back()->with('error', "Tidak ada siswa ditemukan di kelas {$validated['kelas']}.");
            }
            foreach ($students as $student) {
                Payment::create([
                    'student_id' => $student->id,
                    'judul' => $validated['judul'],
                    'jumlah' => $validated['jumlah'],
                    'jatuh_tempo' => $validated['jatuh_tempo'],
                    'keterangan' => $validated['keterangan'] ?? null,
                    'status' => 'belum_lunas',
                ]);
            }
            $count = $students->count();
        }

        return redirect()->route('payments.index')->with('success', "Berhasil mengirimkan notifikasi tagihan ke {$count} siswa.");
    }

    /**
     * Admin verifies proof of payment (Approve/Reject).
     */
    public function verify(Request $request, Payment $payment)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Admin.');
        }

        $validated = $request->validate([
            'action' => 'required|in:setujui,tolak',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        if ($validated['action'] === 'setujui') {
            $payment->update([
                'status' => 'lunas',
                'catatan_admin' => $validated['catatan_admin'] ?? 'Pembayaran telah diverifikasi dan disetujui.',
                'paid_at' => now(),
            ]);
            $message = 'Pembayaran berhasil disetujui (Status: LUNAS).';
        } else {
            $payment->update([
                'status' => 'ditolak',
                'catatan_admin' => $validated['catatan_admin'] ?? 'Bukti pembayaran ditolak. Mohon unggah kembali bukti transfer yang valid.',
            ]);
            $message = 'Bukti pembayaran telah ditolak.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete payment notification.
     */
    public function destroy(Payment $payment)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Admin.');
        }

        if ($payment->bukti_pembayaran && Storage::disk('public')->exists($payment->bukti_pembayaran)) {
            Storage::disk('public')->delete($payment->bukti_pembayaran);
        }

        $payment->delete();

        return redirect()->back()->with('success', 'Notifikasi tagihan berhasil dihapus.');
    }

    /**
     * Display payments for Parent.
     */
    public function parentIndex()
    {
        $user = Auth::user();
        if (!$user->isParent() || !$user->student_id) {
            return redirect()->route('dashboard')->with('error', 'Akun ini belum terhubung dengan data siswa.');
        }

        $student = $user->student;
        $payments = Payment::where('student_id', $student->id)->latest()->get();

        $stats = [
            'total_tagihan' => $payments->count(),
            'belum_lunas' => $payments->whereIn('status', ['belum_lunas', 'ditolak'])->count(),
            'menunggu_konfirmasi' => $payments->where('status', 'menunggu_konfirmasi')->count(),
            'lunas' => $payments->where('status', 'lunas')->count(),
            'total_tunggakan' => $payments->whereIn('status', ['belum_lunas', 'ditolak'])->sum('jumlah'),
        ];

        return view('payments.parent-index', compact('student', 'payments', 'stats'));
    }

    /**
     * Parent uploads payment proof photo/document.
     */
    public function uploadProof(Request $request, Payment $payment)
    {
        $user = Auth::user();
        if (!$user->isParent() || $user->student_id != $payment->student_id) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        if ($payment->bukti_pembayaran && Storage::disk('public')->exists($payment->bukti_pembayaran)) {
            Storage::disk('public')->delete($payment->bukti_pembayaran);
        }

        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        $payment->update([
            'bukti_pembayaran' => $path,
            'status' => 'menunggu_konfirmasi',
            'catatan_admin' => null,
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi verifikasi dari Admin.');
    }
}
