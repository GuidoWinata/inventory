@extends('_admin._layout.app')

@section('title', 'Edit Data Barang')

@section('content')
    <x-admin.page-header :title="'Edit ' . $page['title']" :subtitle="'Perbarui informasi barang: ' . $data->name" :backUrl="route('admin.items.index')" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-admin.card :fit="true">
            <form id="update-form" class="p-2" navigate-form action="{{ route('admin.items.doUpdate', $data->id) }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <x-admin.input label="Kode Barang" name="code" :value="old('code', $data->code)" placeholder="Contoh: BRG-001" :required="true" :error="$errors->first('code')" />

                    <x-admin.input label="Nama Barang" name="name" :value="old('name', $data->name)" placeholder="Contoh: Laptop ASUS VivoBook" :required="true" :error="$errors->first('name')" />

                    <x-admin.input label="Kategori" name="category" :value="old('category', $data->category)" placeholder="Contoh: Elektronik / ATK" :required="true" :error="$errors->first('category')" />

                    <x-admin.input label="Satuan" name="unit" :value="old('unit', $data->unit)" placeholder="Contoh: Pcs, Unit, Box, Rim" :required="true" :error="$errors->first('unit')" />

                    <div>
                        <x-admin.input label="Stok Saat Ini" name="stock_display" :value="$data->stock . ' ' . $data->unit" :readonly="true" :disabled="true" />
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Perubahan stok dilakukan melalui modul Transaksi Barang Masuk & Keluar.</p>
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="mt-6 flex justify-start gap-x-2">
                    <x-admin.button href="{{ route('admin.items.index') }}" color="outline-secondary">
                        Batal
                    </x-admin.button>
                    <x-admin.button type="submit" color="primary">
                        @include('_admin._layout.icons.pencil')
                        Simpan Perubahan
                    </x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
@endsection
