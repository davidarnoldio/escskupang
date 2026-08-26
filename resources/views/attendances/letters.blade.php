<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Penerimaan Surat Izin / Sakit Orang Tua</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Daftar surat izin dan surat dokter yang dikirimkan Orang Tua murid</p>
            </div>
            @if($assignedClass)
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-sky-50 text-sky-900 font-bold text-xs rounded-xl border border-sky-200">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <span>Wali Kelas {{ $assignedClass }}</span>
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8" x-data="{ showModal: false, modalImg: '', modalTitle: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card List -->
            <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-900">Berkas Surat Diterima</h3>
                    <span class="text-xs font-semibold text-slate-500">Total: {{ $attendances->total() }} Surat</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-900 text-xs font-bold text-slate-200 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">NIS & Nama Siswa</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4">Jenis Permohonan</th>
                                <th class="px-6 py-4">Catatan Orang Tua</th>
                                <th class="px-6 py-4 text-center">Foto Surat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($attendances as $att)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        {{ \Carbon\Carbon::parse($att->tanggal)->isoFormat('D MMMM YYYY') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-slate-900">{{ $att->student->nama ?? '-' }}</div>
                                        <div class="text-xs font-mono text-slate-400">NIS: {{ $att->student->nis ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-900 border border-sky-200">
                                            {{ $att->student->kelas ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($att->status == 'sakit')
                                            <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-200 uppercase">
                                                🏥 Sakit
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-sky-50 text-sky-900 border border-sky-200 uppercase">
                                                ✉️ Izin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                        {{ $att->keterangan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($att->surat_izin)
                                            <button type="button" @click="showModal = true; modalImg = '{{ asset($att->surat_izin) }}'; modalTitle = 'Surat {{ ucfirst($att->status) }} - {{ $att->student->nama }}';" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 inline-flex items-center gap-1.5 transition cursor-pointer">
                                                📷 Pratinjau Surat
                                            </button>
                                        @else
                                            <span class="text-slate-300 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-xs">
                                        Belum ada foto surat izin / sakit yang dikirimkan Orang Tua.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $attendances->links() }}
                </div>
            </div>

        </div>

        <!-- Image Preview Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-base text-slate-900" x-text="modalTitle"></h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>
                <div class="max-h-[70vh] overflow-y-auto bg-slate-950 rounded-2xl p-2 text-center">
                    <img :src="modalImg" class="max-w-full mx-auto rounded-xl shadow-lg">
                </div>
                <div class="flex justify-between items-center pt-2">
                    <a :href="modalImg" target="_blank" class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl transition inline-flex items-center gap-1">
                        🔗 Buka File Asli
                    </a>
                    <button type="button" @click="showModal = false" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-sky-400 font-extrabold text-xs rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
