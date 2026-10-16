@extends('_admin._layout.app')

@section('title', 'Laporan Stok & Transaksi')

@section('content')
    <div class="print:hidden">
        <x-admin.page-header :title="$page['title']" subtitle="Rekapitulasi Arus Barang Masuk, Keluar, dan Posisi Stok Fisik">
            <x-admin.button onclick="window.print()" color="primary" class="font-bold">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9" />
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <rect width="12" height="8" x="6" y="14" />
                </svg>
                Cetak / Print Laporan
            </x-admin.button>
        </x-admin.page-header>

        {{-- Filter Form --}}
        <x-admin.card :fit="true" class="mb-6">
            <form action="{{ route('admin.reports.index') }}" method="GET" navigate-form
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end p-2">
                <x-admin.input name="start_date" id="start_date" label="Tanggal Mulai" class="datepicker cursor-pointer" :value="$startDate ?? ''" placeholder="Pilih Tanggal Mulai" size="sm" />

                <x-admin.input name="end_date" id="end_date" label="Tanggal Akhir" class="datepicker cursor-pointer" :value="$endDate ?? ''" placeholder="Pilih Tanggal Akhir" size="sm" />

                <x-admin.select name="type" label="Jenis Transaksi" :options="[
                    'all' => 'Semua Jenis',
                    'in' => 'Barang Masuk (in)',
                    'out' => 'Barang Keluar (out)'
                ]" :value="$type ?? 'all'" size="sm" class="cursor-pointer" />

                <div class="flex items-center gap-2 pb-0.5">
                    <x-admin.button type="submit" size="sm" color="primary">
                        @include('_admin._layout.icons.search')
                        Terapkan Filter
                    </x-admin.button>
                    @if (!empty($startDate) || !empty($endDate) || ($type ?? 'all') !== 'all')
                        <x-admin.button href="{{ route('admin.reports.index') }}" size="sm" color="outline-secondary">
                            @include('_admin._layout.icons.reset')
                            Reset
                        </x-admin.button>
                    @endif
                </div>
            </form>
        </x-admin.card>
    </div>

    {{-- Printable Report Container --}}
    <div id="printable-report" class="space-y-6">

        {{-- Print Header (Only visible on print) --}}
        <div class="hidden print:block text-center pb-6 border-b-2 border-gray-800 mb-6">
            <h1 class="text-2xl font-bold uppercase tracking-wider text-black">Laporan Stok & Arus Barang</h1>
            <p class="text-sm text-gray-600 mt-1">Sistem Manajemen Stok Gudang</p>
            <div class="text-xs text-gray-500 mt-2 flex justify-between">
                <span>
                    Periode:
                    @if($startDate && $endDate)
                        {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    @elseif($startDate)
                        Mulai {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                    @elseif($endDate)
                        Hingga {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                    @else
                        Semua Periode
                    @endif
                    ({{ $type === 'in' ? 'Hanya Barang Masuk' : ($type === 'out' ? 'Hanya Barang Keluar' : 'Semua Transaksi') }})
                </span>
                <span>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</span>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 print:grid-cols-4 print:gap-2">
            <div class="p-4 rounded-xl bg-white dark:bg-neutral-800 border border-gray-100 dark:border-neutral-700 shadow-sm print:border-gray-400 print:shadow-none">
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($report['summary']['total_transactions']) }}</p>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-neutral-800 border border-gray-100 dark:border-neutral-700 shadow-sm print:border-gray-400 print:shadow-none">
                <p class="text-xs font-semibold uppercase text-emerald-600 dark:text-emerald-400">Total Kuantitas Masuk</p>
                <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400 mt-1">+{{ number_format($report['summary']['total_in_quantity']) }}</p>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-neutral-800 border border-gray-100 dark:border-neutral-700 shadow-sm print:border-gray-400 print:shadow-none">
                <p class="text-xs font-semibold uppercase text-orange-600 dark:text-orange-400">Total Kuantitas Keluar</p>
                <p class="text-2xl font-bold text-orange-700 dark:text-orange-400 mt-1">-{{ number_format($report['summary']['total_out_quantity']) }}</p>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-neutral-800 border border-gray-100 dark:border-neutral-700 shadow-sm print:border-gray-400 print:shadow-none">
                <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-400">Total Stok Fisik Gudang</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-400 mt-1">{{ number_format($report['summary']['total_current_stock']) }}</p>
            </div>
        </div>

        {{-- TABEL 1: Riwayat Transaksi Sesuai Filter --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-gray-100 dark:border-neutral-700 shadow-sm overflow-hidden print:border-gray-400 print:shadow-none print:overflow-visible">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-neutral-700 flex items-center justify-between print:py-2">
                <h2 class="text-base font-bold text-gray-800 dark:text-neutral-200">
                    1. Riwayat Transaksi Arus Barang
                </h2>
                <span class="text-xs text-gray-500 dark:text-neutral-400">
                    {{ count($report['transactions']) }} baris data
                </span>
            </div>

            <div class="overflow-x-auto print:overflow-visible">
                <table class="w-full min-w-full print:min-w-0 print:w-full divide-y divide-gray-200 dark:divide-neutral-700 print:border print:border-collapse">
                    <thead class="bg-gray-50 dark:bg-neutral-900/50 print:bg-gray-100">
                        <tr>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Tanggal</th>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Kode Barang</th>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Nama Barang</th>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Jenis</th>
                            <th class="px-4 py-2.5 text-end text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Jumlah</th>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Pencatat</th>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700 print:divide-gray-400">
                        @forelse($report['transactions'] as $tx)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-700/30">
                                <td class="px-4 py-3 whitespace-nowrap print:whitespace-normal text-xs text-gray-800 dark:text-neutral-200 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap print:whitespace-normal text-xs font-mono font-medium text-gray-700 dark:text-neutral-300 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ $tx->item_code }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap print:whitespace-normal text-xs font-semibold text-gray-900 dark:text-white print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ $tx->item_name }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    @if($tx->type === 'in')
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 print:border print:border-emerald-600">
                                            Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 print:border print:border-orange-600">
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs font-bold text-end text-gray-900 dark:text-white print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ number_format($tx->quantity) }} {{ $tx->item_unit }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap print:whitespace-normal text-xs text-gray-600 dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ $tx->user_name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px] print:whitespace-normal">
                                    {{ $tx->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-400 dark:text-neutral-500 print:border">
                                    Tidak ada transaksi barang pada periode / filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL 2: Ringkasan Posisi Stok Fisik Barang Saat Ini --}}
        <div class="bg-white dark:bg-neutral-800 rounded-2xl border border-gray-100 dark:border-neutral-700 shadow-sm overflow-hidden print:border-gray-400 print:shadow-none print:overflow-visible">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-neutral-700 flex items-center justify-between print:py-2">
                <h2 class="text-base font-bold text-gray-800 dark:text-neutral-200">
                    2. Ringkasan Posisi Stok Saat Ini
                </h2>
                <span class="text-xs text-gray-500 dark:text-neutral-400">
                    {{ count($report['current_stock']) }} jenis barang
                </span>
            </div>

            <div class="overflow-x-auto print:overflow-visible">
                <table class="w-full min-w-full print:min-w-0 print:w-full divide-y divide-gray-200 dark:divide-neutral-700 print:border print:border-collapse">
                    <thead class="bg-gray-50 dark:bg-neutral-900/50 print:bg-gray-100">
                        <tr>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Kode</th>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Nama Barang</th>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Kategori</th>
                            <th class="px-4 py-2.5 text-start text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Satuan</th>
                            <th class="px-4 py-2.5 text-end text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Stok Saat Ini</th>
                            <th class="px-4 py-2.5 text-center text-xs font-bold text-gray-600 uppercase dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700 print:divide-gray-400">
                        @foreach($report['current_stock'] as $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-700/30">
                                <td class="px-4 py-2.5 whitespace-nowrap print:whitespace-normal text-xs font-mono font-medium text-gray-800 dark:text-neutral-200 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ $item->code }}
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap print:whitespace-normal text-xs font-semibold text-gray-900 dark:text-white print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ $item->name }}
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap print:whitespace-normal text-xs text-gray-600 dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ $item->category }}
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs text-gray-600 dark:text-neutral-400 print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ $item->unit }}
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-xs font-bold text-end text-gray-900 dark:text-white print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    {{ number_format($item->stock) }}
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap text-center text-xs print:text-black print:border print:px-2 print:py-1.5 print:text-[11px]">
                                    @if ($item->stock == 0)
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 print:border print:border-red-600">
                                            Habis
                                        </span>
                                    @elseif ($item->stock < 10)
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 print:border print:border-amber-600">
                                            Menipis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 print:border print:border-emerald-600">
                                            Aman
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Print Signature Block (Only visible on print) --}}
        <div class="hidden print:grid grid-cols-2 pt-8 text-xs text-center print:break-inside-avoid">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold uppercase mt-1">Kepala Gudang</p>
                <div class="h-16"></div>
                <p class="font-bold underline">( ........................................ )</p>
            </div>
            <div>
                <p>Dibuat oleh,</p>
                <p class="font-bold uppercase mt-1">Manager Operasional</p>
                <div class="h-16"></div>
                <p class="font-bold underline">{{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>
@endsection
