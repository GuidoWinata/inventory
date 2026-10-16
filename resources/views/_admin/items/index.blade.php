@extends('_admin._layout.app')

@section('title', 'Data Stok Barang')

@section('content')
    <x-admin.page-header :title="'Data ' . $page['title']" subtitle="Informasi Master Barang dan Posisi Stok">
        @if (Auth::user()->isAdmin())
            <x-admin.button href="{{ route('admin.items.add') }}" class="font-bold">
                @include('_admin._layout.icons.add')
                Tambah Barang
            </x-admin.button>
        @endif
    </x-admin.page-header>

    <div class="mb-6">
        <form action="{{ route('admin.items.index') }}" method="GET" navigate-form
            class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:w-80">
                <x-admin.input name="keywords" :value="$keywords ?? ''" placeholder="Cari Kode atau Nama Barang..." size="sm" />
            </div>
            <div class="flex items-center gap-2">
                <x-admin.button type="submit" size="sm" color="primary">
                    @include('_admin._layout.icons.search')
                    Cari
                </x-admin.button>
                @if (!empty($keywords))
                    <x-admin.button href="{{ route('admin.items.index') }}" size="sm" color="outline-secondary">
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
                    <x-admin.table.th>Kode</x-admin.table.th>
                    <x-admin.table.th>Nama Barang</x-admin.table.th>
                    <x-admin.table.th>Kategori</x-admin.table.th>
                    <x-admin.table.th>Satuan</x-admin.table.th>
                    <x-admin.table.th>Stok Saat Ini</x-admin.table.th>
                    <x-admin.table.th>Status Stok</x-admin.table.th>
                    <x-admin.table.th align="end"></x-admin.table.th>
                </tr>
            </x-admin.table.thead>
            <x-admin.table.tbody>
                @forelse($data as $d)
                    <x-admin.table.tr>
                        <x-admin.table.td>
                            <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded bg-gray-100 dark:bg-neutral-800 text-gray-800 dark:text-neutral-200">
                                {{ $d->code }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                {{ $d->name }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-sm text-gray-600 dark:text-neutral-400">
                                {{ $d->category }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-sm text-gray-600 dark:text-neutral-400">
                                {{ $d->unit }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            <span class="text-sm font-semibold text-gray-800 dark:text-neutral-200">
                                {{ number_format($d->stock) }}
                            </span>
                        </x-admin.table.td>
                        <x-admin.table.td>
                            @if ($d->stock == 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    Habis
                                </span>
                            @elseif ($d->stock < 10)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    Menipis
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    Aman
                                </span>
                            @endif
                        </x-admin.table.td>
                        <x-admin.table.td innerClass="px-6 py-1.5 flex items-center justify-end gap-x-1">
                            <a navigate
                                class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:hover:bg-neutral-700"
                                href="{{ route('admin.items.detail', $d->id) }}" title="Detail">
                                @include('_admin._layout.icons.view_detail')
                            </a>

                            @if (Auth::user()->isAdmin())
                                <a navigate
                                    class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:bg-blue-100 disabled:opacity-50 disabled:pointer-events-none dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-500 dark:hover:bg-blue-800/30 dark:hover:border-blue-700"
                                    href="{{ route('admin.items.update', $d->id) }}" title="Edit">
                                    @include('_admin._layout.icons.pencil')
                                </a>

                                <button type="button"
                                    class="inline-flex items-center justify-center size-8 text-sm font-semibold rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:border-red-300 focus:outline-none focus:bg-red-100 disabled:opacity-50 disabled:pointer-events-none dark:border-red-800 dark:bg-red-900/20 dark:text-red-500 dark:hover:bg-red-800/30 dark:hover:border-red-700 cursor-pointer"
                                    title="Hapus" data-hs-overlay="#delete-modal"
                                    onclick="setDeleteData('{{ $d->id }}', '{{ addslashes($d->name) }}')">
                                    @include('_admin._layout.icons.trash')
                                </button>
                            @endif
                        </x-admin.table.td>
                    </x-admin.table.tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-neutral-500">
                            <x-admin.empty-state message="Data barang tidak ditemukan" />
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

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto"
        role="dialog" tabindex="-1" aria-labelledby="delete-modal-label">
        <div
            class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
            <div
                class="relative flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                <div class="absolute top-2 end-2">
                    <button type="button"
                        class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600 cursor-pointer"
                        aria-label="Close" data-hs-overlay="#delete-modal">
                        <span class="sr-only">Close</span>
                        @include('_admin._layout.icons.close_modal')
                    </button>
                </div>

                <div class="p-4 sm:p-10 text-center overflow-y-auto">
                    <!-- Icon -->
                    <span
                        class="mb-4 inline-flex justify-center items-center size-14 rounded-full border-4 border-red-50 bg-red-100 text-red-500 dark:bg-red-700 dark:border-red-600 dark:text-red-100">
                        @include('_admin._layout.icons.warning_modal')
                    </span>
                    <!-- End Icon -->

                    <h3 id="delete-modal-label" class="mb-2 text-xl font-bold text-gray-800 dark:text-neutral-200">
                        Hapus Barang
                    </h3>
                    <p class="text-gray-500 dark:text-neutral-500 text-sm">
                        Apakah Anda yakin ingin menghapus barang <span id="delete-item-name"
                            class="font-semibold text-gray-800 dark:text-neutral-200"></span>?
                        <br>Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="mt-6 flex justify-center gap-x-4">
                        <button type="button"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800 cursor-pointer"
                            data-hs-overlay="#delete-modal">
                            Batal
                        </button>
                        <form id="delete-form" method="POST" class="inline" navigate-form>
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDeleteData(id, name) {
            document.getElementById('delete-item-name').textContent = name;
            document.getElementById('delete-form').action = '{{ url('admin/items/delete') }}/' + id;
        }
    </script>
@endsection
