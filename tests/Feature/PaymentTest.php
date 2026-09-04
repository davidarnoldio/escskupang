<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $parentUser;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->student = Student::create([
            'nis' => '1001',
            'nama' => 'Budi Santoso',
            'kelas' => 'Primary A',
            'jenis_kelamin' => 'L',
        ]);

        $this->parentUser = User::factory()->create([
            'role' => 'orang_tua',
            'student_id' => $this->student->id,
        ]);
    }

    public function test_admin_can_create_payment_notification_for_student(): void
    {
        $response = $this->actingAs($this->admin)->post(route('payments.store'), [
            'target_type' => 'student',
            'student_id' => $this->student->id,
            'judul' => 'SPP Bulan September 2026',
            'jumlah' => 500000,
            'jatuh_tempo' => date('Y-m-d', strtotime('+7 days')),
            'keterangan' => 'Transfer ke rekening BCA Sekolah',
        ]);

        $response->assertRedirect(route('payments.index'));
        $this->assertDatabaseHas('payments', [
            'student_id' => $this->student->id,
            'judul' => 'SPP Bulan September 2026',
            'jumlah' => 500000.00,
            'status' => 'belum_lunas',
        ]);
    }

    public function test_admin_can_create_payment_notification_for_entire_class(): void
    {
        Student::create([
            'nis' => '1002',
            'nama' => 'Siti Aminah',
            'kelas' => 'Primary A',
            'jenis_kelamin' => 'P',
        ]);

        $response = $this->actingAs($this->admin)->post(route('payments.store'), [
            'target_type' => 'class',
            'kelas' => 'Primary A',
            'judul' => 'Uang Kegiatan Outbound',
            'jumlah' => 250000,
            'jatuh_tempo' => date('Y-m-d', strtotime('+14 days')),
        ]);

        $response->assertRedirect(route('payments.index'));
        $this->assertEquals(2, Payment::where('judul', 'Uang Kegiatan Outbound')->count());
    }

    public function test_parent_can_view_their_student_payments_and_upload_proof(): void
    {
        Storage::fake('public');

        $payment = Payment::create([
            'student_id' => $this->student->id,
            'judul' => 'SPP September',
            'jumlah' => 500000,
            'jatuh_tempo' => now()->addDays(7),
            'status' => 'belum_lunas',
        ]);

        $response = $this->actingAs($this->parentUser)->get(route('parent.payments'));
        $response->assertStatus(200);
        $response->assertSee('SPP September');

        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $uploadResponse = $this->actingAs($this->parentUser)->post(route('parent.upload-proof', $payment), [
            'bukti_pembayaran' => $file,
        ]);

        $uploadResponse->assertSessionHasNoErrors();
        $payment->refresh();
        $this->assertEquals('menunggu_konfirmasi', $payment->status);
        $this->assertNotNull($payment->bukti_pembayaran);
        Storage::disk('public')->assertExists($payment->bukti_pembayaran);
    }

    public function test_admin_can_approve_or_reject_payment_proof(): void
    {
        $payment = Payment::create([
            'student_id' => $this->student->id,
            'judul' => 'SPP September',
            'jumlah' => 500000,
            'jatuh_tempo' => now()->addDays(7),
            'status' => 'menunggu_konfirmasi',
            'bukti_pembayaran' => 'bukti_pembayaran/dummy.jpg',
        ]);

        $response = $this->actingAs($this->admin)->post(route('payments.verify', $payment), [
            'action' => 'setujui',
            'catatan_admin' => 'Pembayaran lunas terverifikasi.',
        ]);

        $payment->refresh();
        $this->assertEquals('lunas', $payment->status);
        $this->assertNotNull($payment->paid_at);
    }
}
