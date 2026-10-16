@extends('_admin._layout.app')

@section('title', 'Catat Transaksi Barang')

@section('content')
    <x-admin.page-header :title="'Catat ' . $page['title']" subtitle="Pencatatan alur barang masuk atau keluar gudang" :backUrl="route('admin.transactions.index')" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-admin.card :fit="true">
            <form id="add-transaction-form" class="p-2" navigate-form action="{{ route('admin.transactions.create') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    {{-- Pilih Barang --}}
                    @php
                        $itemOptions = $items->mapWithKeys(function($item) {
                            return [$item->id => "[{$item->code}] {$item->name} (Stok: {$item->stock} {$item->unit})"];
                        })->toArray();
                    @endphp
                    <x-admin.select label="Pilih Barang" name="item_id" :options="$itemOptions" placeholder="-- Pilih Barang --" :value="old('item_id')" :required="true" :error="$errors->first('item_id')" class="cursor-pointer" />

                    {{-- Jenis Transaksi --}}
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-neutral-200 pb-2">Jenis Transaksi <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center p-3 w-full bg-white border border-gray-200 rounded-lg text-sm cursor-pointer dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300 has-checked:border-emerald-500 has-checked:bg-emerald-50/30 dark:has-checked:bg-emerald-950/20">
                                <input type="radio" name="type" value="in" class="shrink-0 text-emerald-600 focus:ring-emerald-500" {{ old('type', 'in') === 'in' ? 'checked' : '' }} required>
                                <span class="ms-3 font-medium text-emerald-700 dark:text-emerald-400">
                                    Barang Masuk (in)
                                </span>
                            </label>
                            <label class="flex items-center p-3 w-full bg-white border border-gray-200 rounded-lg text-sm cursor-pointer dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300 has-checked:border-orange-500 has-checked:bg-orange-50/30 dark:has-checked:bg-orange-950/20">
                                <input type="radio" name="type" value="out" class="shrink-0 text-orange-600 focus:ring-orange-500" {{ old('type') === 'out' ? 'checked' : '' }} required>
                                <span class="ms-3 font-medium text-orange-700 dark:text-orange-400">
                                    Barang Keluar (out)
                                </span>
                            </label>
                        </div>
                        @error('type')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <x-admin.input label="Jumlah (Quantity)" name="quantity" type="number" min="1" :value="old('quantity', 1)" placeholder="Contoh: 10" :required="true" :error="$errors->first('quantity')" />

                    {{-- Tanggal Transaksi --}}
                    <x-admin.input label="Tanggal Transaksi" name="date" class="datepicker cursor-pointer" :value="old('date', date('Y-m-d'))" placeholder="Pilih Tanggal Transaksi" :required="true" :error="$errors->first('date')" />

                    {{-- Keterangan --}}
                    <div class="space-y-2">
                        <label for="notes" class="text-sm text-gray-600 dark:text-neutral-200 pb-3">Keterangan (Opsional)</label>
                        <textarea id="notes" name="notes" rows="3"
                            class="py-2.5 sm:py-3 px-4 block w-full border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 placeholder-gray-400 sm:text-sm @error('notes') border-red-500 @enderror"
                            placeholder="Contoh: Pengadaan dari Supplier X, atau Distribusi ke Divisi Marketing">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="mt-6 flex justify-start gap-x-2">
                    <x-admin.button href="{{ route('admin.transactions.index') }}" color="outline-secondary">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" color="primary">
                        @include('_admin._layout.icons.add')
                        Simpan Transaksi
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
