<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 font-sans']) }}>
    <!-- Official NTO National Plus Logo Image -->
    <div class="relative flex items-center justify-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus" class="h-10 w-auto object-contain drop-shadow-md">
    </div>
    <!-- Brand Title -->
    <div class="flex flex-col text-left">
        <span class="font-extrabold text-sm tracking-tight text-white leading-tight">NTO NATIONAL PLUS</span>
        <span class="text-[10px] font-bold text-emerald-400 tracking-wider uppercase leading-none">PRIMARY SCHOOL</span>
    </div>
</div>
