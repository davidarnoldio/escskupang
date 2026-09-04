<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentProofUploadTddTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_upload_payment_proof_image()
    {
        Storage::fake('public');

        $student = Student::factory()->create(['nama' => 'Budi SPP']);
        $parent = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $student->id,
        ]);

        $payment = Payment::create([
            'student_id' => $student->id,
            'judul' => 'SPP September 2026',
            'jumlah' => 500000,
            'jatuh_tempo' => now()->addDays(10),
            'status' => 'belum_lunas',
        ]);

        $proofImage = UploadedFile::fake()->image('bukti_transfer.png', 800, 800);

        $response = $this->actingAs($parent)->post(route('parent.upload-proof', $payment), [
            'bukti_pembayaran' => $proofImage,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals('menunggu_konfirmasi', $payment->status);
        $this->assertNotNull($payment->bukti_pembayaran);
        Storage::disk('public')->assertExists($payment->bukti_pembayaran);
    }

    public function test_admin_can_verify_payment_proof()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();

        $payment = Payment::create([
            'student_id' => $student->id,
            'judul' => 'SPP September 2026',
            'jumlah' => 500000,
            'jatuh_tempo' => now()->addDays(10),
            'status' => 'menunggu_konfirmasi',
            'bukti_pembayaran' => 'bukti_pembayaran/bukti.png',
        ]);

        $response = $this->actingAs($admin)->post(route('payments.verify', $payment), [
            'action' => 'setujui',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals('lunas', $payment->status);
    }

    public function test_admin_can_reject_payment_proof_with_notes()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();

        $payment = Payment::create([
            'student_id' => $student->id,
            'judul' => 'SPP September 2026',
            'jumlah' => 500000,
            'jatuh_tempo' => now()->addDays(10),
            'status' => 'menunggu_konfirmasi',
            'bukti_pembayaran' => 'bukti_pembayaran/bukti_kabur.png',
        ]);

        $response = $this->actingAs($admin)->post(route('payments.verify', $payment), [
            'action' => 'tolak',
            'catatan_admin' => 'Bukti transfer buram, mohon upload ulang.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals('ditolak', $payment->status);
        $this->assertEquals('Bukti transfer buram, mohon upload ulang.', $payment->catatan_admin);
    }
}
