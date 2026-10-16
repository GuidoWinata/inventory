@extends('_admin._layout.app')

@section('title', 'Detail Transaksi')

@section('content')
    <x-admin.page-header :title="'Detail ' . $page['title'] . ' #' . $data->id" subtitle="Rincian informasi arus barang gudang" :backUrl="route('admin.transactions.index')">
        @if ($data->type === 'in')
            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                <span class="size-2 rounded-full bg-emerald-500"></span>
                Barang Masuk
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                <span class="size-2 rounded-full bg-orange-500"></span>
                Barang Keluar
            </span>
        @endif
    </x-admin.page-header>

    <div class="max-w-3xl">
        <x-admin.card :fit="true">
            <div class="p-4 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Tanggal Transaksi</p>
                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($data->date)->translatedFormat('l, d F Y') }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Jumlah Transaksi</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($data->quantity) }} {{ $data->item_unit }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Kode & Nama Barang</p>
                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ $data->item_name }}</p>
                        <p class="font-mono text-xs text-gray-400 dark:text-neutral-500">{{ $data->item_code }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Pencatat Transaksi</p>
                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ $data->user_name ?? '-' }}</p>
                        <p class="text-xs text-gray-400 dark:text-neutral-500">Staff Gudang</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700 md:col-span-2">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Keterangan / Catatan</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200 whitespace-pre-line">{{ $data->notes ?: 'Tidak ada keterangan tambahan.' }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-neutral-700 flex justify-start">
                    <x-admin.button href="{{ route('admin.transactions.index') }}" color="outline-secondary">
                        Kembali ke Riwayat Transaksi
                    </x-admin.button>
                </div>
            </div>
        </x-admin.card>
    </div>
@endsection
