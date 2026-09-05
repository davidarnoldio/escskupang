<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tagihan & Pembayaran SPP Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3 text-red-800 font-semibold text-sm">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3 text-rose-800 font-semibold text-sm">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Student Info Header Card -->
        <div class="bg-gradient-to-r from-slate-900 to-red-950 p-6 rounded-3xl text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-red-600/30 border border-red-400/30 flex items-center justify-center font-black text-lg text-red-300 shadow-inner">
                    {{ strtoupper(substr($student->nama, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tight">{{ $student->nama }}</h3>
                    <p class="text-xs text-slate-300 font-medium">Kelas: <span class="font-bold text-red-300">{{ $student->kelas }}</span> | NIS: {{ $student->nis }}</p>
                </div>
            </div>

            <div class="text-right bg-white/10 px-5 py-3 rounded-2xl border border-white/10 backdrop-blur-sm w-full md:w-auto">
                <div class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Total Belum Lunas</div>
                <div class="text-xl font-black text-rose-400">Rp {{ number_format($stats['total_tunggakan'], 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Payments Card List -->
        <div class="space-y-4" x-data="{ uploadModal: null, proofModal: null }">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Daftar Tagihan Sekolah</span>
            </h3>

            @forelse($payments as $p)
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-slate-900">{{ $p->judul }}</span>
                            <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full border {{ $p->getStatusBadgeClass() }}">
                                {{ $p->getStatusLabel() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">
                            Nominal: <strong class="text-slate-900">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</strong>
                            | Jatuh Tempo: <span class="font-bold text-slate-700">{{ $p->jatuh_tempo ? $p->jatuh_tempo->format('d M Y') : '-' }}</span>
                        </p>
                        @if($p->keterangan)
                            <p class="text-xs text-slate-400 bg-slate-50 p-2 rounded-xl border border-slate-100 font-normal mt-2">{{ $p->keterangan }}</p>
                        @endif

                        @if($p->catatan_admin)
                            <div class="text-xs text-rose-600 bg-rose-50 p-2 rounded-xl border border-rose-200 font-medium mt-2">
                                <strong>Catatan Admin:</strong> {{ $p->catatan_admin }}
                            </div>
                        @endif
                    </div>

                    <div class="shrink-0 flex items-center gap-2 w-full md:w-auto">
                        @if($p->bukti_pembayaran)
                            <button @click="proofModal = '{{ asset('storage/' . $p->bukti_pembayaran) }}'" class="flex-1 md:flex-initial px-3 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-xs font-bold border border-red-200 transition">
                                🔍 Lihat Bukti Upload
                            </button>
                        @endif

                        @if(in_array($p->status, ['belum_lunas', 'ditolak']))
                            <button @click="uploadModal = {{ $p->id }}" class="flex-1 md:flex-initial px-4 py-2 bg-gradient-to-r from-red-600 to-red-800 text-white hover:from-red-700 hover:to-red-900 rounded-xl text-xs font-bold shadow-md shadow-red-600/20 transition">
                                📤 Upload Bukti Transfer
                            </button>
                        @endif
                    </div>

                    <!-- Upload Proof Modal -->
                    <div x-show="uploadModal === {{ $p->id }}" @click.away="uploadModal = null" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                            <h4 class="text-base font-bold text-slate-900">Upload Bukti Pembayaran</h4>
                            <p class="text-xs text-slate-500">{{ $p->judul }} - Rp {{ number_format($p->jumlah, 0, ',', '.') }}</p>

                            <form action="{{ route('parent.upload-proof', $p) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Foto / Gambar Bukti Transfer (JPG/PNG/PDF max 2MB)</label>
                                    <input type="file" name="bukti_pembayaran" required accept="image/jpeg,image/png,image/jpg,application/pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 transition">
                                        Kirim Bukti Pembayaran
                                    </button>
                                    <button type="button" @click="uploadModal = null" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl border border-slate-100 text-center text-slate-400 text-xs font-medium">
                    Belum ada notifikasi tagihan pembayaran untuk anak Anda.
                </div>
            @endforelse

            <!-- Proof View Modal -->
            <div x-show="proofModal" @click.away="proofModal = null" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-2xl max-w-xl w-full p-4 shadow-2xl relative">
                    <button @click="proofModal = null" class="absolute top-3 right-3 text-slate-400 hover:text-slate-700 text-sm font-bold">✕ Close</button>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Bukti Pembayaran yang Diunggah</h4>
                    <div class="rounded-xl overflow-hidden border border-slate-200 max-h-[70vh] flex items-center justify-center bg-slate-950 p-2">
                        <img :src="proofModal" class="max-h-[65vh] object-contain">
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
