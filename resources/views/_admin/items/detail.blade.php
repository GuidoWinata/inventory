@extends('_admin._layout.app')

@section('title', 'Detail Barang')

@section('content')
    <x-admin.page-header :title="'Detail ' . $page['title']" subtitle="Informasi lengkap spesifikasi dan stok barang" :backUrl="route('admin.items.index')">
        @if(Auth::user()->isAdmin())
            <x-admin.button href="{{ route('admin.items.update', $data->id) }}" color="primary">
                @include('_admin._layout.icons.pencil')
                Edit Barang
            </x-admin.button>
        @endif
    </x-admin.page-header>

    <div class="max-w-3xl">
        <x-admin.card :fit="true">
            <div class="p-4 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Kode Barang</p>
                        <p class="mt-1 text-lg font-mono font-bold text-gray-900 dark:text-white">{{ $data->code }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Nama Barang</p>
                        <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $data->name }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Kategori</p>
                        <p class="mt-1 text-base font-medium text-gray-800 dark:text-neutral-200">{{ $data->category }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Satuan</p>
                        <p class="mt-1 text-base font-medium text-gray-800 dark:text-neutral-200">{{ $data->unit }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Stok Saat Ini</p>
                        <div class="mt-1 flex items-center gap-3">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($data->stock) }} {{ $data->unit }}</span>
                            @if ($data->stock == 0)
                                <span class="py-1 px-3 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Habis</span>
                            @elseif ($data->stock < 10)
                                <span class="py-1 px-3 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">Menipis</span>
                            @else
                                <span class="py-1 px-3 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">Aman</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-900/50 border border-gray-100 dark:border-neutral-700">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-neutral-400 font-medium">Tanggal Didaftarkan</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-neutral-200">
                            {{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y, H:i') : '-' }}
                        </p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-neutral-700 flex justify-start">
                    <x-admin.button href="{{ route('admin.items.index') }}" color="outline-secondary">
                        Kembali ke Daftar Barang
                    </x-admin.button>
                </div>
            </div>
        </x-admin.card>
    </div>
@endsection
