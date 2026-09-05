<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Pembayaran SPP & Biaya Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tagihan</p>
                <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($stats['total_tagihan']) }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm bg-red-50/30">
                <p class="text-xs font-bold text-red-500 uppercase tracking-wider">Belum Lunas</p>
                <p class="text-2xl font-black text-red-600 mt-1">{{ number_format($stats['belum_lunas']) }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-sm bg-amber-50/30">
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Konfirmasi</p>
                <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['menunggu_konfirmasi']) }}</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm bg-red-50/30">
                <p class="text-xs font-bold text-red-600 uppercase tracking-wider">Lunas</p>
                <p class="text-2xl font-black text-red-600 mt-1">{{ number_format($stats['lunas']) }}</p>
            </div>
        </div>

        <!-- Form Kirim Notifikasi Tagihan & Filter -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ targetType: 'student' }">
            
            <!-- Left: Create Payment Notification Form -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    <span>Kirim Tagihan / Notifikasi Pembayaran</span>
                </h3>

                <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Tagihan</label>
                        <select name="target_type" x-model="targetType" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-red-500 focus:border-red-500">
                            <option value="student">Satu Siswa (Spesifik)</option>
                            <option value="class">Seluruh Siswa dalam Satu Kelas</option>
                        </select>
                    </div>

                    <div x-show="targetType === 'student'">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Siswa (Cari Nama / NIS)</label>
                        
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '',
                            selectedName: '',
                            students: {{ json_encode($students->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama, 'kelas' => $s->kelas, 'nis' => $s->nis])) }},
                            get filteredStudents() {
                                if (!this.search) return this.students;
                                return this.students.filter(s => 
                                    s.nama.toLowerCase().includes(this.search.toLowerCase()) || 
                                    s.nis.toLowerCase().includes(this.search.toLowerCase()) ||
                                    s.kelas.toLowerCase().includes(this.search.toLowerCase())
                                );
                            },
                            selectStudent(s) {
                                this.selectedId = s.id;
                                this.selectedName = s.nama + ' (' + s.kelas + ' - NIS: ' + s.nis + ')';
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative">

                            <input type="hidden" name="student_id" :value="selectedId">

                            <button type="button" @click="open = !open" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-left text-xs font-medium flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-2xs">
                                <span x-text="selectedName || '-- Cari & Pilih Siswa --'" :class="{ 'text-slate-400': !selectedName, 'text-slate-900 font-bold': selectedName }"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-2.5 flex flex-col space-y-2 max-h-60" style="display: none;">
                                <input type="text" x-model="search" placeholder="🔍 Ketik nama atau NIS siswa untuk mencari..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-red-500 focus:border-red-500" autofocus>
                                
                                <div class="overflow-y-auto divide-y divide-slate-100 flex-1">
                                    <template x-for="s in filteredStudents" :key="s.id">
                                        <button type="button" @click="selectStudent(s)" class="w-full text-left px-3 py-2 hover:bg-red-50 text-xs font-semibold text-slate-700 hover:text-red-900 rounded-lg flex justify-between items-center transition">
                                            <span x-text="s.nama"></span>
                                            <span class="text-[10px] text-slate-400 font-normal" x-text="s.kelas + ' • NIS: ' + s.nis"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredStudents.length === 0" class="py-4 text-center text-xs text-slate-400 italic">
                                        Siswa tidak ditemukan...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="targetType === 'class'" style="display: none;">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Kelas</label>
                        <select name="kelas" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-red-500 focus:border-red-500">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls }}">{{ $cls }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Judul / Nama Pembayaran</label>
                        <input type="text" name="judul" required placeholder="Contoh: SPP Bulan September 2026" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" min="0" required placeholder="500000" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jatuh Tempo</label>
                            <input type="date" name="jatuh_tempo" required value="{{ date('Y-m-d', strtotime('+7 days')) }}" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan / Instruksi Rekening (Opsional)</label>
                        <textarea name="keterangan" rows="2" placeholder="Transfer ke BCA 123456789 a.n. NTO National Plus" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-red-600 to-red-800 text-white rounded-xl font-bold text-xs shadow-md shadow-red-500/20 hover:from-red-700 hover:to-red-900 transition">
                        Kirim Notifikasi Tagihan
                    </button>
                </form>
            </div>

            <!-- Right: Payments List & Search/Filter -->
            <div class="lg:col-span-2 space-y-4">
                
                <!-- Filter Bar -->
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                    <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <select name="kelas" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 text-xs font-medium">
                                <option value="">Semua Kelas</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls }}" {{ request('kelas') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-slate-200 text-xs font-medium">
                                <option value="">Semua Status</option>
                                <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / judul..." class="w-full rounded-xl border-slate-200 text-xs font-medium">
                        </div>
                    </form>
                </div>

                <!-- Payments Table -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ activeProofModal: null, activeVerifyModal: null }">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-extrabold">
                                    <th class="py-3 px-4">Siswa & Kelas</th>
                                    <th class="py-3 px-4">Judul Tagihan</th>
                                    <th class="py-3 px-4">Jumlah</th>
                                    <th class="py-3 px-4">Jatuh Tempo</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-center">Bukti / Aksis</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($payments as $p)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-4 font-bold text-slate-900">
                                            <div>{{ $p->student->nama }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium">{{ $p->student->kelas }} (NIS: {{ $p->student->nis }})</div>
                                        </td>
                                        <td class="py-3 px-4 font-semibold">
                                            {{ $p->judul }}
                                            @if($p->keterangan)
                                                <div class="text-[10px] text-slate-400 font-normal truncate max-w-xs">{{ $p->keterangan }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-black text-slate-900">
                                            Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-500">
                                            {{ $p->jatuh_tempo ? $p->jatuh_tempo->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-block px-2.5 py-1 text-[10px] font-black rounded-full border {{ $p->getStatusBadgeClass() }}">
                                                {{ $p->getStatusLabel() }}
                                            </span>
                                            @if($p->catatan_admin)
                                                <div class="text-[10px] text-rose-500 mt-1 font-medium">{{ $p->catatan_admin }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center space-x-1">
                                            @if($p->bukti_pembayaran)
                                                <button @click="activeProofModal = '{{ asset('storage/' . $p->bukti_pembayaran) }}'" class="px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-[10px] font-bold border border-red-200 transition">
                                                    🔍 Lihat Bukti
                                                </button>
                                            @endif

                                            @if($p->status === 'menunggu_konfirmasi')
                                                <button @click="activeVerifyModal = {{ $p->id }}" class="px-2.5 py-1 bg-red-600 text-white hover:bg-red-700 rounded-lg text-[10px] font-bold transition shadow-sm">
                                                    Verifikasi
                                                </button>
                                            @endif

                                            <form action="{{ route('payments.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 text-slate-400 hover:text-rose-600 transition">
                                                    🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Verify Modal -->
                                    <div x-show="activeVerifyModal === {{ $p->id }}" @click.away="activeVerifyModal = null" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                                        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                                            <h4 class="text-base font-bold text-slate-900">Verifikasi Pembayaran</h4>
                                            <p class="text-xs text-slate-500">Siswa: <strong>{{ $p->student->nama }}</strong> - {{ $p->judul }} (Rp {{ number_format($p->jumlah, 0, ',', '.') }})</p>
                                            
                                            @if($p->bukti_pembayaran)
                                                <div class="rounded-xl overflow-hidden border border-slate-200 max-h-48 flex justify-center bg-slate-100 p-2">
                                                    <img src="{{ asset('storage/' . $p->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="max-h-44 object-contain">
                                                </div>
                                            @endif

                                            <form action="{{ route('payments.verify', $p) }}" method="POST" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Admin (Opsional)</label>
                                                    <textarea name="catatan_admin" rows="2" placeholder="Tulis alasan jika menolak atau konfirmasi..." class="w-full rounded-xl border-slate-200 text-xs"></textarea>
                                                </div>

                                                <div class="flex gap-2 pt-2">
                                                    <button type="submit" name="action" value="setujui" class="flex-1 py-2 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 transition">
                                                        ✅ Setujui (Lunas)
                                                    </button>
                                                    <button type="submit" name="action" value="tolak" class="flex-1 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition">
                                                        ❌ Tolak Bukti
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                            Belum ada data tagihan pembayaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Proof Modal -->
                    <div x-show="activeProofModal" @click.away="activeProofModal = null" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                        <div class="bg-white rounded-2xl max-w-xl w-full p-4 shadow-2xl relative">
                            <button @click="activeProofModal = null" class="absolute top-3 right-3 text-slate-400 hover:text-slate-700 text-sm font-bold">✕ Close</button>
                            <h4 class="text-sm font-bold text-slate-900 mb-3">Bukti Transfer Pembayaran</h4>
                            <div class="rounded-xl overflow-hidden border border-slate-200 max-h-[70vh] flex items-center justify-center bg-slate-950 p-2">
                                <img :src="activeProofModal" class="max-h-[65vh] object-contain">
                            </div>
                        </div>
                    </div>

                    @if($payments->hasPages())
                        <div class="p-4 border-t border-slate-100">
                            {{ $payments->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
