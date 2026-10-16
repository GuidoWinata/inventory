@extends('_admin._layout.app')

@section('title', 'Tambah Data Barang')

@section('content')
    <x-admin.page-header :title="'Tambah ' . $page['title']" subtitle="Input data master barang baru ke gudang" :backUrl="route('admin.items.index')" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-admin.card :fit="true">
            <form id="add-form" class="p-2" navigate-form action="{{ route('admin.items.create') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <x-admin.input label="Kode Barang" name="code" :value="old('code')" placeholder="Contoh: BRG-001" :required="true" :error="$errors->first('code')" />

                    <x-admin.input label="Nama Barang" name="name" :value="old('name')" placeholder="Contoh: Laptop ASUS VivoBook" :required="true" :error="$errors->first('name')" />

                    <x-admin.input label="Kategori" name="category" :value="old('category')" placeholder="Contoh: Elektronik / ATK / Perkakas" :required="true" :error="$errors->first('category')" />

                    <x-admin.input label="Satuan" name="unit" :value="old('unit')" placeholder="Contoh: Pcs, Unit, Box, Rim" :required="true" :error="$errors->first('unit')" />

                    <div>
                        <x-admin.input label="Stok Awal" name="stock" type="number" min="0" :value="old('stock', 0)" placeholder="0" :error="$errors->first('stock')" />
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Default 0 jika belum ada fisik barang.</p>
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="mt-6 flex justify-start gap-x-2">
                    <x-admin.button href="{{ route('admin.items.index') }}" color="outline-secondary">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" color="primary">
                        @include('_admin._layout.icons.add')
                        Simpan Barang
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
