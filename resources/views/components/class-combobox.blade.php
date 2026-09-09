@props([
    'name' => 'kelas',
    'id' => null,
    'value' => '',
    'placeholder' => 'Pilih atau ketik kelas (misal: TK, Kelas 1, 6A, 5C)',
    'officialClasses' => [],
    'required' => false
])

@php
    $id = $id ?? $name;
    
    // Default list of options ordered logically
    $defaultOptions = [
        'TK', 'TK A', 'TK B',
        'Kelas 1',
        'Kelas 2', 
        'Kelas 3', 
        'Kelas 4', 
        'Kelas 5',
        'Kelas 6', 
    ];
    
    // Merge provided officialClasses / DB classes with default options
    $merged = array_unique(array_merge($defaultOptions, (array)$officialClasses));
    
    // Custom sort helper to group TK first, then Kelas 1, 2, 3, 4, 5, 6 properly
    usort($merged, function($a, $b) {
        $getWeight = function($str) {
            if (preg_match('/^TK\s*([A-Z])?/i', trim($str), $m)) {
                return 0 + (isset($m[1]) ? (ord(strtoupper($m[1])) - 64) * 0.01 : 0);
            }
            if (preg_match('/^Kelas\s*(\d+)\s*([A-Z])?/i', trim($str), $m)) {
                $num = (int)$m[1];
                $sub = isset($m[2]) ? (ord(strtoupper($m[2])) - 64) * 0.01 : 0;
                return $num + $sub;
            }
            return 999;
        };
        return $getWeight($a) <=> $getWeight($b);
    });
    
    $optionsJson = json_encode(array_values($merged));
@endphp

<div x-data="{
    open: false,
    query: '{{ old($name, $value) }}',
    selected: '{{ old($name, $value) }}',
    options: {{ $optionsJson }},
    get filteredOptions() {
        if (!this.query) return this.options;
        return this.options.filter(opt => opt.toLowerCase().includes(this.query.toLowerCase().trim()));
    },
    selectOption(opt) {
        this.selected = opt;
        this.query = opt;
        this.open = false;
    }
}" @click.outside="open = false" class="relative">

    <!-- Input Box & Toggle Button -->
    <div class="relative flex items-center">
        <input 
            type="text" 
            id="{{ $id }}"
            name="{{ $name }}" 
            x-model="query" 
            @focus="open = true" 
            @input="open = true; selected = query" 
            @keydown.escape="open = false"
            placeholder="{{ $placeholder }}" 
            {{ $required ? 'required' : '' }}
            autocomplete="off"
            class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 font-semibold shadow-xs transition placeholder:text-slate-400"
        />
        <button 
            type="button" 
            @click="open = !open" 
            class="absolute right-0 top-0 bottom-0 px-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
            tabindex="-1">
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- Dropdown Menu -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1.5 w-full bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto p-1.5 focus:outline-none"
        style="display: none;">
        
        <template x-for="opt in filteredOptions" :key="opt">
            <button 
                type="button" 
                @click="selectOption(opt)" 
                class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold flex items-center justify-between transition cursor-pointer"
                :class="selected === opt ? 'bg-red-50 text-red-700 font-bold' : 'text-slate-700 hover:bg-slate-100'">
                <span x-text="opt"></span>
                <template x-if="selected === opt">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
            </button>
        </template>

        <!-- Dynamic manual entry option when typing custom input -->
        <div x-show="query && !options.some(opt => opt.toLowerCase() === query.toLowerCase().trim())" class="border-t border-slate-100 mt-1 pt-1">
            <button 
                type="button" 
                @click="selectOption(query)" 
                class="w-full text-left px-3 py-2 rounded-lg text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 flex items-center gap-2 transition cursor-pointer">
                <span>➕ Gunakan <u x-text="query"></u> (Input Manual)</span>
            </button>
        </div>

        <!-- Empty state when query doesn't match and query is empty -->
        <div x-show="filteredOptions.length === 0 && !query" class="px-3 py-3 text-xs text-slate-400 text-center font-medium">
            Tidak ada opsi kelas.
        </div>
    </div>
</div>
