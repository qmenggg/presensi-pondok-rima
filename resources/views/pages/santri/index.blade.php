@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Data Santri</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola data santri pondok pesantren</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Export Button -->
                <a href="{{ route('santri.export') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export
                </a>
                <!-- Tambah Button -->
                <a href="{{ route('santri.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Santri
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <!-- Header with Search -->
            <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Santri</h3>
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
                            <input type="text" id="search-input" placeholder="Cari santri..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]"/>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="santri-table" class="w-full min-w-full">
                        <thead>
                            <tr class="border-gray-200 border-y dark:border-gray-700">
                                <th scope="col" class="hidden sm:table-cell px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">No</th>
                                <th scope="col" class="hidden md:table-cell px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Foto</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Nama</th>
                                <th scope="col" class="hidden md:table-cell px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Jenis Kelamin</th>
                                <th scope="col" class="hidden lg:table-cell px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Kamar</th>
                                <th scope="col" class="hidden lg:table-cell px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">QR Code</th>
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

    <!-- QR Code Modal -->
    <div id="qr-modal-component" class="fixed inset-0 z-99999 hidden flex items-center justify-center bg-black/50 backdrop-blur-[32px] p-5" onclick="if(event.target === this) closeQRModal()">
        <!-- Modal Content -->
        <div class="relative w-full max-w-2xl rounded-3xl bg-white dark:bg-gray-900" onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button type="button" onclick="closeQRModal()"
                class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 sm:h-11 sm:w-11">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fillRule="evenodd" clipRule="evenodd"
                        d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z"
                        fill="currentColor" />
                </svg>
            </button>

            <!-- Modal Body -->
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-primary-50 dark:bg-primary-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white/90">QR Code Santri</h3>
                        <p id="qr-santri-name" class="text-sm text-gray-500 dark:text-gray-400"></p>
                    </div>
                </div>

                <!-- QR Code Container -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 mb-4">
                    <div id="qr-code-container" class="flex justify-center items-center">
                        <!-- QR Code image will be inserted here -->
                    </div>
                </div>

                <!-- QR Code Info & Download -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Kode QR:</span>
                        <span id="qr-code-text" class="text-sm font-mono font-semibold text-gray-900 dark:text-white/90"></span>
                    </div>

                    <button onclick="downloadQRCode()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download QR Code
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
        let currentQRFile = '';
        let currentQRCode = '';
        let table;

        $(document).ready(function() {
            table = $('#santri-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('santri.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'hidden sm:table-cell px-4 py-4 text-sm text-gray-900 dark:text-white/90' },
                    { data: 'foto', name: 'foto', orderable: false, searchable: false, className: 'hidden md:table-cell px-4 py-4' },
                    { data: 'nama', name: 'user.nama', className: 'px-4 py-4 text-sm' },
                    { data: 'jenis_kelamin', name: 'user.jenis_kelamin', className: 'hidden md:table-cell px-4 py-4' },
                    { data: 'kamar', name: 'kamar.nama_kamar', className: 'hidden lg:table-cell px-4 py-4' },
                    { data: 'qr_code', name: 'qr_code', className: 'hidden lg:table-cell px-4 py-4' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'px-4 py-4 text-right' },
                ],
                language: {
                    processing: '<div class="flex items-center gap-2"><svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Memuat data...</span></div>',
                    emptyTable: "Tidak ada data santri",
                    zeroRecords: "Tidak ada data yang cocok",
                },
                responsive: true,
                autoWidth: false,
                order: [[2, 'asc']],
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
            const table = $('#santri-table');

            // Show/hide columns based on screen width
            if (width < 640) {
                // Mobile: show only Nama and Aksi
                table.find('th:eq(0), td:nth-child(1)').hide(); // No
                table.find('th:eq(1), td:nth-child(2)').hide(); // Foto
                table.find('th:eq(3), td:nth-child(4)').hide(); // Jenis Kelamin
                table.find('th:eq(4), td:nth-child(5)').hide(); // Kamar
                table.find('th:eq(5), td:nth-child(6)').hide(); // QR Code
            } else if (width < 768) {
                // Tablet small: show No, Nama, Aksi
                table.find('th:eq(0), td:nth-child(1)').show();
                table.find('th:eq(1), td:nth-child(2)').hide(); // Foto
                table.find('th:eq(3), td:nth-child(4)').hide(); // Jenis Kelamin
                table.find('th:eq(4), td:nth-child(5)').hide(); // Kamar
                table.find('th:eq(5), td:nth-child(6)').hide(); // QR Code
            } else if (width < 1024) {
                // Tablet: show No, Foto, Nama, Jenis Kelamin, Aksi
                table.find('th:eq(0), td:nth-child(1)').show();
                table.find('th:eq(1), td:nth-child(2)').show(); // Foto
                table.find('th:eq(3), td:nth-child(4)').show(); // Jenis Kelamin
                table.find('th:eq(4), td:nth-child(5)').hide(); // Kamar
                table.find('th:eq(5), td:nth-child(6)').hide(); // QR Code
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

        function deleteSantri(id) {
            if (confirm('Apakah Anda yakin ingin menghapus santri ini?')) {
                $.ajax({
                    url: '/santri/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            table.ajax.reload();
                            alert(response.message || 'Santri berhasil dihapus');
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus santri');
                    }
                });
            }
        }

        function showQRModal(qrCode, qrFile, santriName) {
            console.log('Opening modal with:', qrCode, qrFile, santriName);
            currentQRCode = qrCode;
            currentQRFile = qrFile;

            const container = document.getElementById('qr-code-container');
            const text = document.getElementById('qr-code-text');
            const name = document.getElementById('qr-santri-name');

            if (qrFile) {
                container.innerHTML = '<img src="/storage/asset_santri/qrcode/' + qrFile + '" alt="QR Code" class="w-64 h-64 object-contain">';
            } else {
                container.innerHTML = '<div class="w-64 h-64 flex items-center justify-center text-gray-400 dark:text-gray-500"><p>QR Code tidak tersedia</p></div>';
            }

            text.textContent = qrCode;
            name.textContent = santriName;

            // Open modal
            const modal = document.getElementById('qr-modal-component');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeQRModal(event) {
            // Close modal
            const modal = document.getElementById('qr-modal-component');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeQRModal();
            }
        });

        function downloadQRCode() {
            if (currentQRFile) {
                const link = document.createElement('a');
                link.href = '/storage/asset_santri/qrcode/' + currentQRFile;
                link.download = 'QR-' + currentQRCode + '.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert('QR Code tidak tersedia untuk diunduh');
            }
        }
    </script>
@endpush

@push('styles')
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css"> --}}
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

        /* Responsive table */
        @media (max-width: 640px) {
            #santri-table th:nth-child(1),
            #santri-table td:nth-child(1),
            #santri-table th:nth-child(2),
            #santri-table td:nth-child(2),
            #santri-table th:nth-child(4),
            #santri-table td:nth-child(4),
            #santri-table th:nth-child(5),
            #santri-table td:nth-child(5),
            #santri-table th:nth-child(6),
            #santri-table td:nth-child(6) {
                display: none;
            }
        }

        @media (min-width: 641px) and (max-width: 768px) {
            #santri-table th:nth-child(2),
            #santri-table td:nth-child(2),
            #santri-table th:nth-child(4),
            #santri-table td:nth-child(4),
            #santri-table th:nth-child(5),
            #santri-table td:nth-child(5),
            #santri-table th:nth-child(6),
            #santri-table td:nth-child(6) {
                display: none;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            #santri-table th:nth-child(5),
            #santri-table td:nth-child(5),
            #santri-table th:nth-child(6),
            #santri-table td:nth-child(6) {
                display: none;
            }
        }

        /* Ensure table doesn't overflow */
        #santri-table {
            table-layout: fixed;
        }

        #santri-table td,
        #santri-table th {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
    </style>
@endpush
