<div x-data="toastManager()"
     @notify.window="add($event.detail)"
     class="fixed top-4 right-4 sm:top-5 sm:right-5 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none sm:max-w-md"
     style="display: none;"
     x-show="toasts.length > 0">
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2 sm:translate-y-0 sm:translate-x-4 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 sm:translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="pointer-events-auto relative overflow-hidden rounded-2xl border-2 shadow-2xl p-4 transition duration-200"
             :class="{
                 'border-emerald-500 shadow-emerald-500/20': toast.type === 'success',
                 'border-rose-500 shadow-rose-500/20': toast.type === 'error',
                 'border-amber-500 shadow-amber-500/20': toast.type === 'warning',
                 'border-blue-500 shadow-blue-500/20': toast.type === 'info'
             }"
             style="background-color: #ffffff !important;">
            
            <div class="flex items-start gap-3.5">
                <!-- Icon Box -->
                <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                     :class="{
                         'bg-emerald-500 text-white': toast.type === 'success',
                         'bg-rose-500 text-white': toast.type === 'error',
                         'bg-amber-500 text-white': toast.type === 'warning',
                         'bg-blue-500 text-white': toast.type === 'info'
                     }">
                    <!-- Success Icon -->
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </template>
                    <!-- Error Icon -->
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </template>
                    <!-- Warning Icon -->
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </template>
                    <!-- Info Icon -->
                    <template x-if="toast.type === 'info'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </template>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0 pr-6">
                    <h5 class="text-sm font-extrabold tracking-tight"
                        :class="{
                            'text-emerald-800': toast.type === 'success',
                            'text-rose-800': toast.type === 'error',
                            'text-amber-800': toast.type === 'warning',
                            'text-blue-800': toast.type === 'info'
                        }"
                        x-text="toast.title"></h5>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5 leading-snug break-words" x-text="toast.message"></p>
                    <span class="text-[10px] font-bold text-slate-400 mt-1 block">Baru saja</span>
                </div>

                <!-- Close Button -->
                <button type="button" @click="remove(toast.id)"
                        class="absolute top-3 right-3 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Auto-dismiss Progress Bar -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100 overflow-hidden">
                <div class="h-full transition-all linear"
                     :class="{
                         'bg-emerald-500': toast.type === 'success',
                         'bg-rose-500': toast.type === 'error',
                         'bg-amber-500': toast.type === 'warning',
                         'bg-blue-500': toast.type === 'info'
                     }"
                     :style="'width: ' + toast.progress + '%; transition-duration: 100ms;'"></div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toastManager', () => ({
            toasts: [],
            init() {
                // Flash session detection from Laravel
                @if(session('success'))
                    this.add({
                        type: 'success',
                        title: 'Berhasil Disimpan!',
                        message: '{{ addslashes(session('success')) }}',
                        timeout: 5000
                    });
                @endif

                @if(session('status') && session('status') === 'profile-updated')
                    this.add({
                        type: 'success',
                        title: 'Profil Berhasil Disimpan!',
                        message: 'Informasi profil Anda telah berhasil diperbarui.',
                        timeout: 5000
                    });
                @elseif(session('status'))
                    this.add({
                        type: 'info',
                        title: 'Pemberitahuan Sistem',
                        message: '{{ addslashes(session('status')) }}',
                        timeout: 5000
                    });
                @endif

                @if(session('error'))
                    this.add({
                        type: 'error',
                        title: 'Gagal Menyimpan!',
                        message: '{{ addslashes(session('error')) }}',
                        timeout: 6000
                    });
                @endif

                @if(session('warning'))
                    this.add({
                        type: 'warning',
                        title: 'Peringatan Sistem',
                        message: '{{ addslashes(session('warning')) }}',
                        timeout: 6000
                    });
                @endif

                @if($errors->any())
                    this.add({
                        type: 'error',
                        title: 'Gagal Menyimpan Perubahan!',
                        message: '{{ addslashes($errors->first()) }}',
                        timeout: 6000
                    });
                @endif
            },
            add({ type = 'success', title = 'Berhasil!', message = '', timeout = 5000 }) {
                const id = Date.now() + Math.random();
                const toast = {
                    id: id,
                    type: type,
                    title: title,
                    message: message,
                    visible: true,
                    progress: 100,
                    interval: null
                };

                this.toasts.push(toast);

                const stepTime = 100;
                const decrement = 100 / (timeout / stepTime);

                toast.interval = setInterval(() => {
                    toast.progress -= decrement;
                    if (toast.progress <= 0) {
                        clearInterval(toast.interval);
                        this.remove(id);
                    }
                }, stepTime);
            },
            remove(id) {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index !== -1) {
                    if (this.toasts[index].interval) {
                        clearInterval(this.toasts[index].interval);
                    }
                    this.toasts[index].visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 250);
                }
            }
        }));
    });
</script>
