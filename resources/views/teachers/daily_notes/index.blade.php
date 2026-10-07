<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Catatan Harian Siswa</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Buku penghubung digital dari Guru ke Orang Tua murid @if($assignedClass) &bull; Wali Kelas: <strong>{{ $assignedClass }}</strong> @endif</p>
            </div>
            <div>
                <button @click="$dispatch('open-create-modal')" type="button"
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold text-xs rounded-2xl shadow-md shadow-red-600/30 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tulis Catatan Baru</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        createOpen: false,
        editOpen: false,
        editAction: '',
        editData: {
            id: null,
            student_name: '',
            tanggal: '',
            kategori: '',
            judul: '',
            catatan: '',
            pesan_untuk_orangtua: ''
        },
        openEdit(note) {
            this.editData = {
                id: note.id,
                student_name: note.student ? note.student.nama : '',
                tanggal: note.tanggal,
                kategori: note.kategori,
                judul: note.judul,
                catatan: note.catatan,
                pesan_untuk_orangtua: note.pesan_untuk_orangtua || ''
            };
            this.editAction = '/daily-notes/' + note.id;
            this.editOpen = true;
        }
    }" @open-create-modal.window="createOpen = true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm font-semibold flex items-center gap-2 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs sm:text-sm space-y-1 shadow-xs">
                    @foreach($errors->all() as $error)
                        <p class="flex items-center gap-2 font-medium">
                            <span>⚠️</span> <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            <!-- Summary KPI Statistics Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Today Notes -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Catatan Hari Ini</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $todayNotesCount }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ now()->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-xl">
                        📝
                    </div>
                </div>

                <!-- Today Students Count -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Siswa Tercatat Hari Ini</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $todayStudentsCount }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Siswa berbeda</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl">
                        👥
                    </div>
                </div>

                <!-- Total Month Notes -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Bulan Ini</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $monthNotesCount }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ now()->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl">
                        📅
                    </div>
                </div>

                <!-- Read by Parent -->
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Dibaca Orang Tua</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $readCount }}</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Sudah dilihat di portal</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">
                        👁️
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
                <form method="GET" action="{{ route('daily-notes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    @if(!Auth::user()->isTeacher() || !Auth::user()->getAssignedClass())
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Kelas</label>
                            <select name="kelas" onchange="this.form.submit()"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                                <option value="">Semua Kelas</option>
                                @foreach((array)$classes as $c)
                                    <option value="{{ $c }}" {{ request('kelas') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Siswa</label>
                        <select name="student_id" onchange="this.form.submit()"
                                class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                            <option value="">Semua Siswa</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}" {{ request('student_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->nama }} ({{ $st->kelas }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()"
                               class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                        <select name="kategori" onchange="this.form.submit()"
                                class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Cari Kata Kunci</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari catatan..."
                                   class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                            <button type="submit" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                                🔍
                            </button>
                        </div>
                    </div>
                </form>

                @if(request()->anyFilled(['kelas', 'student_id', 'tanggal', 'kategori', 'search']))
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Menampilkan hasil filter pencarian</span>
                        <a href="{{ route('daily-notes.index') }}" class="text-red-600 hover:underline font-bold">Reset Filter</a>
                    </div>
                @endif
            </div>

            <!-- Daily Notes Feed List -->
            @if($notes->isEmpty())
                <div class="bg-white p-12 rounded-3xl border border-slate-100 shadow-xs text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-3xl mx-auto">
                        📝
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Catatan Harian</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Mulai tulis catatan harian untuk memantau perkembangan belajar, perilaku, atau apresiasi siswa. Catatan ini akan otomatis terbaca oleh akun orang tua di rumah.
                    </p>
                    <button @click="$dispatch('open-create-modal')" type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl transition shadow-md">
                        + Tulis Catatan Pertama
                    </button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($notes as $note)
                        <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-xs hover:shadow-md transition duration-200">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 pb-3 border-b border-slate-100">
                                <!-- Student & Date Info -->
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-700 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-sm overflow-hidden">
                                        @if($note->student?->foto_url)
                                            <img src="{{ $note->student->foto_url }}" alt="{{ $note->student->nama }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($note->student?->nama ?? 'S', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-sm font-extrabold text-slate-900">{{ $note->student?->nama ?? 'Siswa' }}</h4>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">
                                                {{ $note->student?->kelas }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5 flex-wrap">
                                            <span>📅 {{ \Carbon\Carbon::parse($note->tanggal)->translatedFormat('l, d F Y') }}</span>
                                            <span>&bull;</span>
                                            <span>Oleh: <strong class="text-slate-600">{{ $note->teacher?->name ?? 'Guru' }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category Badge & Read Status -->
                                <div class="flex items-center gap-2 flex-wrap sm:justify-end">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold border {{ $note->getKategoriBadgeClass() }}">
                                        <span>{{ $note->getKategoriIcon() }}</span>
                                        <span>{{ $note->kategori }}</span>
                                    </span>

                                    @if($note->is_read)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200" title="Dibaca {{ $note->read_at?->diffForHumans() }}">
                                            <span>✓ Dibaca Ortu</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                            <span>⏳ Belum Dibaca</span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Note Content Body -->
                            <div class="py-3.5 space-y-2">
                                <h5 class="text-xs font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                                    <span>📌</span>
                                    <span>{{ $note->judul }}</span>
                                </h5>
                                <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/70 p-3.5 rounded-2xl border border-slate-100">
                                    {{ $note->catatan }}
                                </div>

                                @if($note->pesan_untuk_orangtua)
                                    <div class="mt-2 p-3 bg-red-50/60 rounded-2xl border border-red-100/80 text-xs text-red-900 space-y-1">
                                        <div class="font-extrabold flex items-center gap-1.5 text-red-700 text-[11px] uppercase tracking-wider">
                                            <span>🏡 Arahan / Pendampingan di Rumah:</span>
                                        </div>
                                        <div class="text-slate-700 text-xs italic pl-4 border-l-2 border-red-400">
                                            "{{ $note->pesan_untuk_orangtua }}"
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons Footer -->
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-[10px] text-slate-400">
                                    Dibuat {{ $note->created_at->diffForHumans() }}
                                </span>

                                @if(Auth::user()->isAdmin() || $note->teacher_id === Auth::id())
                                    <div class="flex items-center gap-2">
                                        <button @click="openEdit({{ $note->toJson() }})" type="button"
                                                class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                                            ✏️ Edit
                                        </button>
                                        <form method="POST" action="{{ route('daily-notes.destroy', $note) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan harian ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs rounded-xl transition cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $notes->links() }}
                    </div>
                </div>
            @endif

        </div>

        <!-- ========================================== -->
        <!-- MODAL: CREATE DAILY NOTE                   -->
        <!-- ========================================== -->
        <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div @click="createOpen = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100">
                    <div class="bg-gradient-to-r from-red-600 to-red-800 px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📝</span>
                            <h3 class="text-base font-black tracking-tight">Tulis Catatan Harian Siswa</h3>
                        </div>
                        <button @click="createOpen = false" type="button" class="text-white/80 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('daily-notes.store') }}" class="p-6 space-y-4">
                        @csrf
                        
                        <!-- Pilihan Siswa -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Pilih Siswa <span class="text-rose-500">*</span></label>
                            <select name="student_id" required
                                    class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                                <option value="" disabled selected>-- Pilih Siswa yang Dituju --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}" {{ old('student_id', request('student_id')) == $st->id ? 'selected' : '' }}>
                                        {{ $st->nama }} (Kelas {{ $st->kelas }} &bull; NISN: {{ $st->nisn ?? $st->nis }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal & Kategori -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Tanggal <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                                       class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Kategori Catatan <span class="text-rose-500">*</span></label>
                                <select name="kategori" required
                                        class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Judul / Topik -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Judul / Topik Ringkas <span class="text-rose-500">*</span></label>
                            <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Sangat fokus dan aktif saat Matematika"
                                   class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                        </div>

                        <!-- Isi Catatan -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Isi Catatan Guru <span class="text-rose-500">*</span></label>
                            <textarea name="catatan" rows="4" required placeholder="Tuliskan perkembangan, apresiasi, perilaku, atau hal penting yang terjadi hari ini..."
                                      class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">{{ old('catatan') }}</textarea>
                        </div>

                        <!-- Arahan untuk Orang Tua di Rumah -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                Pesan / Pendampingan di Rumah <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                            </label>
                            <textarea name="pesan_untuk_orangtua" rows="2" placeholder="Contoh: Mohon Ananda dibimbing mengulang hafalan perkalian 5 di rumah ya Ayah/Bunda..."
                                      class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">{{ old('pesan_untuk_orangtua') }}</textarea>
                            <p class="text-[10px] text-slate-400 mt-1">Pesan ini akan ditampilkan khusus sebagai arahan tindak lanjut untuk Orang Tua di portal mereka.</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button @click="createOpen = false" type="button"
                                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold text-xs rounded-xl shadow-md shadow-red-600/30 transition">
                                🚀 Simpan & Kirim ke Orang Tua
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: EDIT DAILY NOTE                     -->
        <!-- ========================================== -->
        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div @click="editOpen = false" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100">
                    <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">✏️</span>
                            <h3 class="text-base font-black tracking-tight">Edit Catatan Harian: <span x-text="editData.student_name"></span></h3>
                        </div>
                        <button @click="editOpen = false" type="button" class="text-white/80 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <form method="POST" :action="editAction" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Tanggal & Kategori -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Tanggal</label>
                                <input type="date" name="tanggal" x-model="editData.tanggal" required
                                       class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Kategori Catatan</label>
                                <select name="kategori" x-model="editData.kategori" required
                                        class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Judul / Topik -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Judul / Topik</label>
                            <input type="text" name="judul" x-model="editData.judul" required
                                   class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                        </div>

                        <!-- Isi Catatan -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Isi Catatan Guru</label>
                            <textarea name="catatan" rows="4" x-model="editData.catatan" required
                                      class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500"></textarea>
                        </div>

                        <!-- Arahan untuk Orang Tua di Rumah -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                Pesan / Pendampingan di Rumah
                            </label>
                            <textarea name="pesan_untuk_orangtua" rows="2" x-model="editData.pesan_untuk_orangtua"
                                      class="w-full text-xs font-semibold rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500"></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button @click="editOpen = false" type="button"
                                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition">
                                💾 Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
