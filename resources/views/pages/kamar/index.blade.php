@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Data Kamar</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola data kamar pondok pesantren</p>
            </div>
            <a href="{{ route('kamar.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Kamar
            </a>
        </div>

        <!-- Table Card -->
        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <!-- Header with Search -->
            <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Kamar</h3>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <!-- Entries per page -->
                    <div class="flex items-center gap-2">
                        <label for="entries-select" class="text-sm font-medium text-gray-700 dark:text-gray-400">Show</label>
                        <select id="entries-select" class="h-[42px] rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <form>
                        <div class="relative">
                            <button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
                                <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z" fill=""/>
                                </svg>
                            </button>
                            <input type="text" id="search-input" placeholder="Cari kamar..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]"/>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="kamar-table" class="w-full min-w-full">
                        <thead>
                            <tr class="border-gray-200 border-y dark:border-gray-700">
                                <th scope="col" class="hidden sm:table-cell px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">No</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Nama Kamar</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Jenis</th>
                                <th scope="col" class="relative px-4 py-3">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- DataTable will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
                <div class="flex items-center justify-between">
                    <!-- Previous Button -->
                    <button id="prev-btn" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 sm:px-3.5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z" fill="currentColor"/>
                        </svg>
                        <span class="hidden sm:inline">Previous</span>
                    </button>

                    <!-- Page Info (Mobile) -->
                    <span id="page-info-mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">
                        Page <span id="current-page-mobile">1</span> of <span id="total-pages-mobile">1</span>
                    </span>

                    <!-- Page Numbers (Desktop) -->
                    <ul id="page-numbers" class="hidden items-center gap-0.5 sm:flex">
                        <!-- Will be populated by JavaScript -->
                    </ul>

                    <!-- Next Button -->
                    <button id="next-btn" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 sm:px-3.5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="hidden sm:inline">Next</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        let table;

        $(document).ready(function() {
            table = $('#kamar-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kamar.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'hidden sm:table-cell px-4 py-4 text-sm text-gray-900 dark:text-white/90' },
                    { data: 'nama_kamar', name: 'nama_kamar', className: 'px-4 py-4 text-sm font-medium text-gray-900 dark:text-white/90' },
                    { data: 'jenis', name: 'jenis', className: 'px-4 py-4' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'px-4 py-4 text-right' },
                ],
                language: {
                    processing: '<div class="flex items-center gap-2"><svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Memuat data...</span></div>',
                    emptyTable: "Tidak ada data kamar",
                    zeroRecords: "Tidak ada data yang cocok",
                },
                responsive: true,
                autoWidth: false,
                order: [[1, 'asc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: 'rt',
                drawCallback: function(settings) {
                    updatePagination(settings);
                    handleTableResponsive();
                }
            });

            // Handle responsive on window resize
            $(window).on('resize', function() {
                handleTableResponsive();
            });

            // Custom search
            $('#search-input').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Custom length
            $('#entries-select').on('change', function() {
                table.page.len(this.value).draw();
            });
        });

        function handleTableResponsive() {
            const width = window.innerWidth;
            const table = $('#kamar-table');

            // Show/hide columns based on screen width
            if (width < 640) {
                // Mobile: show only Nama Kamar, Jenis, Aksi
                table.find('th:eq(0), td:nth-child(1)').hide(); // No
            } else {
                // Desktop: show all
                table.find('th, td').show();
            }
        }

        function updatePagination(settings) {
            const api = new $.fn.dataTable.Api(settings);
            const pageInfo = api.page.info();
            const currentPage = pageInfo.page + 1;
            const totalPages = pageInfo.pages;

            // Update mobile info
            $('#current-page-mobile').text(currentPage);
            $('#total-pages-mobile').text(totalPages);

            // Update prev/next buttons
            const prevBtn = $('#prev-btn');
            const nextBtn = $('#next-btn');

            prevBtn.prop('disabled', currentPage === 1);
            nextBtn.prop('disabled', currentPage === totalPages);

            // Generate page numbers
            const pageNumbers = $('#page-numbers');
            pageNumbers.empty();

            const displayedPages = getDisplayedPages(currentPage, totalPages);

            displayedPages.forEach(page => {
                const li = $('<li></li>');

                if (page === '...') {
                    li.html('<span class="flex h-10 w-10 items-center justify-center text-gray-500">...</span>');
                } else {
                    const button = $('<button></button>')
                        .text(page)
                        .addClass('flex h-10 w-10 items-center justify-center rounded-lg text-theme-sm font-medium transition-colors')
                        .on('click', function() {
                            table.page(page - 1).draw('page');
                        });

                    if (currentPage === page) {
                        button.addClass('bg-blue-500 text-white');
                    } else {
                        button.addClass('text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500');
                    }

                    li.append(button);
                }

                pageNumbers.append(li);
            });

            // Prev/Next handlers
            prevBtn.off('click').on('click', function() {
                if (currentPage > 1) {
                    table.page(currentPage - 2).draw('page');
                }
            });

            nextBtn.off('click').on('click', function() {
                if (currentPage < totalPages) {
                    table.page(currentPage).draw('page');
                }
            });
        }

        function getDisplayedPages(current, total) {
            const range = [];
            for (let i = 1; i <= total; i++) {
                if (i === 1 || i === total || (i >= current - 1 && i <= current + 1)) {
                    range.push(i);
                } else if (range[range.length - 1] !== '...') {
                    range.push('...');
                }
            }
            return range;
        }

        function deleteKamar(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data kamar yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/kamar/' + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                window.showGlobalAlert(true, response.message || 'Kamar berhasil dihapus');
                            } else {
                                window.showGlobalAlert(false, response.message || 'Gagal menghapus kamar');
                            }
                        },
                        error: function(xhr) {
                            window.showGlobalAlert(false, 'Terjadi kesalahan saat menghapus kamar');
                        }
                    });
                }
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Hide default DataTables elements */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none;
        }

        .dataTables_wrapper .dataTables_processing {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        @media (prefers-color-scheme: dark) {
            .dataTables_wrapper .dataTables_processing {
                background: rgba(31, 41, 55, 0.95);
                border-color: #374151;
                color: #f3f4f6;
            }
        }

        /* Ensure table doesn't overflow */
        #kamar-table {
            table-layout: fixed;
        }

        #kamar-table td,
        #kamar-table th {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
    </style>
@endpush
