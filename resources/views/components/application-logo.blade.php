<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 font-sans']) }}>
    <!-- Official NTO National Plus Logo Image -->
    <div class="relative flex items-center justify-center">
        <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="Logo NTO National Plus" class="h-10 w-10 rounded-full object-contain bg-white p-0.5 shadow-sm" onerror="this.src='{{ asset('logo.png') }}?v={{ time() }}'">
    </div>
    <!-- Brand Title -->
    <div class="flex flex-col text-left">
        <span class="font-extrabold text-sm tracking-tight text-white leading-tight">NTO NATIONAL PLUS</span>
        <span class="text-[10px] font-bold text-red-400 tracking-wider uppercase leading-none">PRIMARY SCHOOL</span>
    </div>
</div>
