@extends('_admin._layout.app')

@section('title', 'Barang Masuk & Keluar')

@section('content')
    <x-admin.page-header :title="'Riwayat ' . $page['title']" subtitle="Pencatatan Alur Barang Masuk dan Keluar Gudang">
        <x-admin.button href="{{ route('admin.transactions.add') }}" class="font-bold">
            @include('_admin._layout.icons.add')
            Catat Transaksi
        </x-admin.button>
    </x-admin.page-header>

    <div class="mb-6">
        <form action="{{ route('admin.transactions.index') }}" method="GET" navigate-form
            class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-72">
                <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari Barang atau Keterangan..." size="sm" />
            </div>
            <div class="w-full sm:w-48">
                <x-admin.select :label="null" name="type" :options="[
                    'all' => 'Semua Jenis',
                    'in' => 'Barang Masuk (in)',
                    'out' => 'Barang Keluar (out)'
                ]" :value="$type ?? 'all'" size="sm" class="cursor-pointer" />
            </div>
            <div class="flex items-center gap-2">
                <x-admin.button type="submit" size="sm" color="primary">
                    @include('_admin._layout.icons.search')
                    Cari
                </x-admin.button>
                @if (!empty($keywords) || ($type ?? 'all') !== 'all')
                    <x-admin.button href="{{ route('admin.transactions.index') }}" size="sm" color="outline-secondary">
                        @include('_admin._layout.icons.reset')
                        Reset
                    </x-admin.button>
                @endif
            </div>
        </form>
    </div>

    <x-admin.table.wrapper>
        <x-admin.table>
            <x-admin.table.thead>
                <tr>
                    <x-admin.table.th>Tanggal</x-admin.table.th>
                    <x-admin.table.th>Barang</x-admin.table.th>
                    <x-admin.table.th>Jenis Transaksi</x-admin.table.th>
                    <x-admin.table.th>Jumlah</x-admin.table.th>
                    <x-admin.table.th>Pencatat</x-admin.table.th>
                    <x-admin.table.th>Keterangan</x-admin.table.th>
                    <x-admin.table.th align="end"></x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $d)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <span class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                {{ \Carbon\Carbon::parse($d->date)->translatedFormat('d M Y') }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white block">
                                    {{ $d->item_name }}
                                </span>
                                <span class="font-mono text-xs text-gray-400 dark:text-neutral-500">
                                    {{ $d->item_code }}
                                </span>
                            </div>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            @if ($d->type === 'in')
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-2.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    Barang Masuk
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 py-0.5 px-2.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                    <span class="size-1.5 rounded-full bg-orange-500"></span>
                                    Barang Keluar
                                </span>
                            @endif
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="font-semibold text-sm text-gray-900 dark:text-white">
                                {{ number_format($d->quantity) }} {{ $d->item_unit }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-sm text-gray-600 dark:text-neutral-400">
                                {{ $d->user_name ?? '-' }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-sm text-gray-500 dark:text-neutral-400 truncate max-w-xs block">
                                {{ $d->notes ?? '-' }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-6 py-1.5 flex items-center justify-end gap-x-1">
                            <a navigate
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700"
                                href="{{ route('admin.transactions.detail', $d->id) }}" title="Detail">
                                @include('_admin._layout.icons.view_detail')
                            </a>
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-neutral-500">
                            <x-admin.empty-state message="Belum ada data transaksi barang." />
                        </td>
                    </tr>
                @endforelse
            </x-admin.table.tbody>
        </x-admin.table>

        @if (count($data) > 0 && $data->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                <div class="flex justify-end">
                    {{ $data->links() }}
                </div>
            </div>
        @endif
    </x-admin.table.wrapper>
@endsection
