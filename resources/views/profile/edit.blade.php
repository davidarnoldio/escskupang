<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Pengaturan Akun & Profil</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                    Kelola informasi data profil dan perbarui kata sandi akun Anda
                </p>
            </div>
            <div>
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-red-50 text-red-900 font-bold text-xs rounded-xl border border-red-200 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                    <span>{{ strtoupper(Auth::user()->role) }}: {{ Auth::user()->name }}</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Update Profile Information Card -->
        <div class="p-6 sm:p-8 bg-white shadow-sm border border-slate-100 rounded-3xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="p-6 sm:p-8 bg-white shadow-sm border border-slate-100 rounded-3xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        @if(!Auth::user()->isAdmin())
            <!-- Delete Account Card (Non-Admin only) -->
            <div class="p-6 sm:p-8 bg-white shadow-sm border border-slate-100 rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
