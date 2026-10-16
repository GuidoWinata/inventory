@extends('_admin._layout.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $today = now();

        $greeting = match (true) {
            $today->hour < 11 => 'Selamat Pagi',
            $today->hour < 15 => 'Selamat Siang',
            $today->hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };

        $accentPalette = ['blue', 'emerald', 'purple', 'orange', 'indigo', 'violet'];

        $accent = [
            'blue' => [
                'bg' => 'from-blue-500/10 via-blue-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-blue-500 to-blue-600 text-white',
                'icon_shadow' => 'shadow-blue-500/25',
                'arrow_hover' => 'group-hover:text-blue-400',
                'glow' => 'group-hover:shadow-blue-500/10',
            ],
            'emerald' => [
                'bg' => 'from-emerald-500/10 via-emerald-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white',
                'icon_shadow' => 'shadow-emerald-500/25',
                'arrow_hover' => 'group-hover:text-emerald-400',
                'glow' => 'group-hover:shadow-emerald-500/10',
            ],
            'purple' => [
                'bg' => 'from-purple-500/10 via-purple-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-purple-500 to-purple-600 text-white',
                'icon_shadow' => 'shadow-purple-500/25',
                'arrow_hover' => 'group-hover:text-purple-400',
                'glow' => 'group-hover:shadow-purple-500/10',
            ],
            'orange' => [
                'bg' => 'from-orange-500/10 via-orange-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-orange-500 to-orange-600 text-white',
                'icon_shadow' => 'shadow-orange-500/25',
                'arrow_hover' => 'group-hover:text-orange-400',
                'glow' => 'group-hover:shadow-orange-500/10',
            ],
            'indigo' => [
                'bg' => 'from-indigo-500/10 via-indigo-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white',
                'icon_shadow' => 'shadow-indigo-500/25',
                'arrow_hover' => 'group-hover:text-indigo-400',
                'glow' => 'group-hover:shadow-indigo-500/10',
            ],
            'violet' => [
                'bg' => 'from-violet-500/10 via-violet-500/5 to-transparent',
                'icon' => 'bg-gradient-to-br from-violet-500 to-violet-600 text-white',
                'icon_shadow' => 'shadow-violet-500/25',
                'arrow_hover' => 'group-hover:text-violet-400',
                'glow' => 'group-hover:shadow-violet-500/10',
            ],
        ];
    @endphp

    <div class="space-y-8">

        {{-- HERO --}}
        <section class="relative overflow-hidden rounded-[32px] bg-white dark:bg-neutral-900 border border-white/20 dark:border-white/5 shadow-[0_10px_60px_rgba(0,0,0,0.06)] mb-10">

            {{-- Background Decoration --}}
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-0 right-0 w-[420px] h-[420px] bg-blue-500/10 blur-3xl rounded-full"
                     style="animation: blob1 8s ease-in-out infinite;"></div>
                <div class="absolute bottom-0 left-0 w-[320px] h-[320px] bg-indigo-500/10 blur-3xl rounded-full"
                     style="animation: blob2 10s ease-in-out infinite;"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-violet-500/5 blur-3xl rounded-full"
                     style="animation: blob3 14s ease-in-out infinite;"></div>
            </div>



            <div class="relative px-8 lg:px-10 py-10 lg:py-12">

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">

                    {{-- LEFT --}}
                    <div class="max-w-2xl">

                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-neutral-100 dark:bg-white/5 mb-5">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>

                            <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                                {{ $today->translatedFormat('l, d F Y') }}
                            </span>
                        </div>

                        <h1 class="text-4xl lg:text-5xl font-semibold tracking-tight text-neutral-900 dark:text-white leading-tight">
                            {{ $greeting }},
                            <span class="bg-gradient-to-r from-blue-600 to-indigo-500 bg-clip-text text-transparent">
                                {{ Auth::user()->name }}
                            </span>
                        </h1>

                        <p class="mt-4 text-[15px] leading-7 text-neutral-500 dark:text-neutral-400 max-w-xl">
                            Selamat datang kembali di Aplikasi {{ config('app.name', 'SmartInventory') }}
                        </p>

                    </div>

                    {{-- RIGHT --}}
                    <div class="flex flex-col items-start lg:items-end">

                        <div class="text-[72px] leading-none font-light tracking-[-4px] text-neutral-900 dark:text-white tabular-nums"
                             id="dashboard-clock">
                            {{ $today->format('H:i') }}
                        </div>


                    </div>

                </div>
            </div>
        </section>

        {{-- SUMMARY CARDS (PRD Fitur 2) --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Total Jenis Barang --}}
            <x-admin.card :fit="true">
                <div class="flex items-center justify-between p-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-neutral-400">Total Jenis Barang</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_item_types'] }}</h3>
                        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">Item aktif terdaftar</p>
                    </div>
                    <div class="size-12 rounded-xl bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </x-admin.card>

            {{-- Total Stok Fisik --}}
            <x-admin.card :fit="true">
                <div class="flex items-center justify-between p-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-neutral-400">Total Stok Fisik</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($stats['total_physical_stock']) }}</h3>
                        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">Akumulasi kuantitas barang</p>
                    </div>
                    <div class="size-12 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-admin.card>

            {{-- Total Transaksi Masuk --}}
            <x-admin.card :fit="true">
                <div class="flex items-center justify-between p-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-neutral-400">Total Barang Masuk</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_transactions_in'] }}</h3>
                        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">Transaksi penerimaan</p>
                    </div>
                    <div class="size-12 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                        </svg>
                    </div>
                </div>
            </x-admin.card>

            {{-- Total Transaksi Keluar --}}
            <x-admin.card :fit="true">
                <div class="flex items-center justify-between p-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-neutral-400">Total Barang Keluar</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_transactions_out'] }}</h3>
                        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">Transaksi pengeluaran</p>
                    </div>
                    <div class="size-12 rounded-xl bg-orange-500/10 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>
            </x-admin.card>
        </section>

        {{-- LATEST TRANSACTIONS TABLE (PRD Fitur 2) --}}
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">5 Transaksi Terakhir</h2>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Aktivitas arus barang masuk dan keluar terbaru</p>
                </div>
                @if(Auth::user()->isStaff())
                    <x-admin.button href="{{ route('admin.transactions.index') }}" size="sm" color="outline-primary">
                        Lihat Semua Riwayat &rarr;
                    </x-admin.button>
                @endif
            </div>

            <x-admin.table.wrapper>
                <x-admin.table>
                    <x-admin.table.thead>
                        <tr>
                            <x-admin.table.th>Tanggal</x-admin.table.th>
                            <x-admin.table.th>Barang</x-admin.table.th>
                            <x-admin.table.th>Tipe</x-admin.table.th>
                            <x-admin.table.th>Jumlah</x-admin.table.th>
                            <x-admin.table.th>Pencatat</x-admin.table.th>
                        </tr>
                    </x-admin.table.thead>
                    <x-admin.table.tbody>
                        @forelse($stats['latest_transactions'] as $tx)
                            <x-admin.table.tr>
                                <x-admin.table.td>
                                    <span class="text-sm text-gray-800 dark:text-neutral-200">
                                        {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d M Y') }}
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td>
                                    <span class="font-medium text-gray-900 dark:text-white block">{{ $tx->item_name }}</span>
                                    <span class="block text-xs font-mono text-gray-400 dark:text-neutral-500">{{ $tx->item_code }}</span>
                                </x-admin.table.td>
                                <x-admin.table.td>
                                    @if($tx->type === 'in')
                                        <span class="inline-flex items-center gap-1.5 py-0.5 px-2.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-0.5 px-2.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                            Keluar
                                        </span>
                                    @endif
                                </x-admin.table.td>
                                <x-admin.table.td>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                        {{ $tx->quantity }} {{ $tx->item_unit }}
                                    </span>
                                </x-admin.table.td>
                                <x-admin.table.td>
                                    <span class="text-sm text-gray-600 dark:text-neutral-400">
                                        {{ $tx->user_name ?? '-' }}
                                    </span>
                                </x-admin.table.td>
                            </x-admin.table.tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-admin.empty-state message="Belum ada transaksi barang yang dicatat." />
                                </td>
                            </tr>
                        @endforelse
                    </x-admin.table.tbody>
                </x-admin.table>
            </x-admin.table.wrapper>
        </section>

        {{-- MENU --}}
        <section>

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                        Modul Aplikasi
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                @forelse ($modules as $index => $menu)
                    @php
                        $accentKey = $accentPalette[$index % count($accentPalette)];
                        $c = $accent[$accentKey];

                        try {
                            $moduleUrl = route($menu->route_name);
                        } catch (\Exception $e) {
                            $moduleUrl = '#';
                        }

                        $moduleDesc = ! empty($menu->children)
                            ? collect($menu->children)->pluck('label')->join(', ')
                            : 'Buka modul ' . $menu->label . '.';
                    @endphp

                    <a navigate
                       href="{{ $moduleUrl }}"
                       class="group relative overflow-hidden rounded-[28px] bg-white dark:bg-neutral-900 border border-neutral-200/70 dark:border-white/5 p-7 transition-all duration-500 hover:-translate-y-1 hover:shadow-lg shadow-black/5 {{ $c['glow'] }}">

                        {{-- Gradient Layer --}}
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500 bg-gradient-to-br {{ $c['bg'] }}"></div>

                        <div class="relative flex flex-col h-full">

                            {{-- TOP --}}
                            <div class="flex items-start justify-between">

                                <div class="size-14 rounded-2xl {{ $c['icon'] }} flex items-center justify-center shadow-lg {{ $c['icon_shadow'] }} ring-1 ring-white/20 ring-inset transition-all duration-300 group-hover:shadow-xl group-hover:scale-[1.03] [&_svg]:size-6">
                                    @if ($menu->icon)
                                        @include($menu->icon)
                                    @endif
                                </div>

                                <div class="translate-x-0 group-hover:translate-x-1 transition duration-300">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="size-5 text-neutral-300 dark:text-neutral-600 transition-colors {{ $c['arrow_hover'] }}"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        />

                                    </svg>
                                </div>

                            </div>

                            {{-- CONTENT --}}
                            <div class="mt-10">

                                <h3 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                                    Aplikasi {{ $menu->label }}
                                </h3>

                                <p class="mt-1 text-md text-neutral-400 dark:text-neutral-400">
                                    {{ $moduleDesc }}
                                </p>

                            </div>

                        </div>

                    </a>

                @empty
                    <div class="col-span-full rounded-[28px] border border-dashed border-neutral-200 dark:border-neutral-700 p-10 text-center">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            Tidak ada modul tersedia untuk akun Anda.
                        </p>
                    </div>
                @endforelse

            </div>

        </section>

    </div>

    @push('scripts')
        <style>
            @keyframes blob1 {
                0%   { transform: translateX(0px); }
                20%  { transform: translateX(60px); }
                45%  { transform: translateX(-30px); }
                65%  { transform: translateX(80px); }
                80%  { transform: translateX(-50px); }
                100% { transform: translateX(0px); }
            }
            @keyframes blob2 {
                0%   { transform: translateX(0px); }
                25%  { transform: translateX(-70px); }
                50%  { transform: translateX(40px); }
                70%  { transform: translateX(-55px); }
                90%  { transform: translateX(30px); }
                100% { transform: translateX(0px); }
            }
            @keyframes blob3 {
                0%   { transform: translate(-50%, -50%) translateX(0px); }
                30%  { transform: translate(-50%, -50%) translateX(50px); }
                55%  { transform: translate(-50%, -50%) translateX(-60px); }
                75%  { transform: translate(-50%, -50%) translateX(35px); }
                100% { transform: translate(-50%, -50%) translateX(0px); }
            }
        </style>
        <script>
            (() => {
                const clock = document.getElementById('dashboard-clock');

                if (!clock) return;

                setInterval(() => {
                    const now = new Date();

                    clock.textContent = now.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                }, 1000);
            })();
        </script>
    @endpush

@endsection