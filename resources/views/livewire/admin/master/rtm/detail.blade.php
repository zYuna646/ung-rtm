<main class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen" x-data="{
    showToast: {{ session()->has('toastMessage') ? 'true' : 'false' }},
    toastMessage: '{{ session('toastMessage') }}',
    toastType: '{{ session('toastType') }}',
    rtmReport: false,
    lampiranModal: false
}" x-init="if (showToast) { setTimeout(() => showToast = false, 5000); }">

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    @endpush

    <!-- Toast -->
    <div x-show="showToast" x-transition.opacity.duration.300ms
        :class="toastType === 'success' ? 'text-green-600' : 'text-red-600'"
        class="fixed top-20 right-5 z-50 flex items-center w-full max-w-xs p-4 rounded-lg shadow-lg bg-white">
        <div :class="toastType === 'success' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100'"
            class="inline-flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-full">
            <span>
                <i :class="toastType === 'success' ? 'fas fa-check' : 'fas fa-exclamation'" class="text-xl"></i>
            </span>
        </div>
        <div class="ml-4 text-sm font-medium" x-text="toastMessage"></div>
        <button type="button" @click="showToast = false"
            class="ml-auto p-1 text-gray-400 hover:text-gray-700 rounded focus:outline-none" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <section class="max-w-7xl w-full mx-auto px-6 pt-24" x-data="{ addModal: false }">
        <!-- Kontainer Utama -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Informasi RTM dan Filter -->
            <div class="space-y-6">
                <!-- RTM Report Button -->
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center border-b border-gray-200 pb-4 mb-4">
                        <div class="bg-red-100 p-2 rounded-full mr-3">
                            <i class="fas fa-file-alt text-red-600 text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Laporan RTM</h2>
                    </div>
                    
                    <button @click="rtmReport = !rtmReport" class="w-full bg-white hover:bg-gray-100 text-black font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center border border-gray-300 shadow-md mb-3">
                        <i class="fas fa-file-download mr-2 text-red-600"></i> Buat Laporan RTM
                    </button>
                    
                    <button @click="lampiranModal = !lampiranModal" class="w-full bg-white hover:bg-gray-100 text-black font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center border border-gray-300 shadow-md">
                        <i class="fas fa-paperclip mr-2 text-green-600"></i> Kelola Lampiran ({{ count($lampiran) }})
                    </button>
                </div>
                
                @if ($user->role->name == 'Universitas')
                    <!-- Kartu Filter -->
                    <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center border-b border-gray-200 pb-4 mb-4">
                            <div class="bg-indigo-100 p-2 rounded-full mr-3">
                                <i class="fas fa-filter text-indigo-600 text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Filter Data</h2>
                        </div>
                        
                        <div class="mb-6">
                            <label for="fakultas" class="block text-sm font-medium text-gray-700 mb-2">Fakultas</label>
                            <div class="relative">
                            
                                <select id="fakultas" name="fakultas" wire:model="selectedFakultas" wire:change="loadLampiran"
                                    class="w-full p-3 border rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 hover:bg-white pl-4 pr-10">
                                    <option value="">Semua Fakultas</option>
                                    @foreach ($fakultas as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-500"></i>
                                </div>
                            </div>
                        </div>
                        
                        <button wire:click="resetFilter" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-redo-alt mr-2"></i> Reset Filter
                        </button>
                    </div>
                @endif
                <!-- Kartu Informasi RTM -->
                <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center border-b border-gray-200 pb-4 mb-6">
                        <div class="bg-indigo-100 p-2 rounded-full mr-3">
                            <i class="fas fa-info-circle text-indigo-600 text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Informasi RTM</h2>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-500 mb-1">Nama RTM</p>
                            <p class="font-medium text-gray-800">{{ $rtm->name }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-500 mb-1">Tahun</p>
                            <p class="font-medium text-gray-800">{{ $rtm->tahun }}</p>
                        </div>
                    </div>

                    @if (!empty($rtm->ami_anchor))
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-chart-line text-blue-600"></i>
                                </div>
                                <h3 class="font-semibold text-lg text-gray-800">AMI Anchor</h3>
                            </div>
                            <ul class="bg-blue-50 rounded-lg p-3 space-y-2">
                                @foreach ($rtm->ami_anchor as $anchor)
                                    @php
                                        $matchedAnchor = collect($anchor_ami)->firstWhere('id', $anchor);
                                    @endphp
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                                        <span class="text-gray-700">{{ $matchedAnchor ? $matchedAnchor['periode_name'] : '-' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-chart-line text-blue-600"></i>
                                </div>
                                <h3 class="font-semibold text-lg text-gray-800">AMI Anchor</h3>
                            </div>
                            <p class="bg-blue-50 rounded-lg p-3 text-gray-500">Tidak ada data</p>
                        </div>
                    @endif

                    @if (!empty($rtm->survei_anchor))
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-poll text-green-600"></i>
                                </div>
                                <h3 class="font-semibold text-lg text-gray-800">Survei Anchor</h3>
                            </div>
                            <ul class="bg-green-50 rounded-lg p-3 space-y-2">
                                @foreach ($rtm->survei_anchor as $anchor)
                                    @php
                                        $matchedAnchor = collect($anchor_survei)->firstWhere('id', (int) $anchor);
                                    @endphp
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span class="text-gray-700">{{ $matchedAnchor['name'] ?? '-' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="mb-6">
                            <div class="flex items-center mb-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-poll text-green-600"></i>
                                </div>
                                <h3 class="font-semibold text-lg text-gray-800">Survei Anchor</h3>
                            </div>
                            <p class="bg-green-50 rounded-lg p-3 text-gray-500">Tidak ada data</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Tiga Kartu Data (disusun vertikal) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Kartu Survei -->
                <div x-data="{ expanded: true }" class="bg-white shadow-lg rounded-xl p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                <i class="fas fa-poll text-green-600 text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Survei</h2>
                        </div>
                        <button type="button" @click="expanded = !expanded"
                            class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full focus:outline-none transition-colors duration-200">
                            <template x-if="expanded">
                                <i class="fas fa-chevron-up text-gray-600"></i>
                            </template>
                            <template x-if="!expanded">
                                <i class="fas fa-chevron-down text-gray-600"></i>
                            </template>
                        </button>
                    </div>
                    <div x-show="expanded" x-transition class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Code</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Nama</th>
                                    <th class="px-6 py-4 text-center text-sm font-medium text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($rtm->survei_anchor as $index => $item)
                                    @php
                                        $matchedAnchor = collect($anchor_survei)->firstWhere('id', (int) $item);
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $matchedAnchor['code'] ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $matchedAnchor['name'] ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <x-button color="info" size="sm" class="hover:bg-indigo-600 transition-colors duration-200 shadow-sm"
                                                onclick="window.location.href='{{ route('dashboard.master.rtm.view-survei', ['rtm_id' => $rtm->id, 'survei_id' => $item]) }}'">
                                                <i class="fas fa-eye mr-1"></i> Lihat Data
                                            </x-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kartu AMI -->
                <div x-data="{ expanded: true }" class="bg-white shadow-lg rounded-xl p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-2 rounded-full mr-3">
                                <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">AMI</h2>
                        </div>
                        <button type="button" @click="expanded = !expanded"
                            class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full focus:outline-none transition-colors duration-200">
                            <template x-if="expanded">
                                <i class="fas fa-chevron-up text-gray-600"></i>
                            </template>
                            <template x-if="!expanded">
                                <i class="fas fa-chevron-down text-gray-600"></i>
                            </template>
                        </button>
                    </div>
                    <div x-show="expanded" x-transition class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">No.</th>
                                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-700">Nama</th>
                                    <th class="px-6 py-4 text-center text-sm font-medium text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($rtm->ami_anchor as $index => $item)
                                    @php
                                        $matchedAnchor = collect($anchor_ami)->firstWhere('id', (int) $item);
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $matchedAnchor['periode_name'] ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <x-button color="info" size="sm" class="hover:bg-indigo-600 transition-colors duration-200 shadow-sm"
                                                onclick="window.location.href='{{ route('dashboard.master.rtm.view-ami', ['rtm_id' => $rtm->id, 'anchor_id' => $item]) }}'">
                                                <i class="fas fa-eye mr-1"></i> Lihat Data
                                            </x-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div x-data="{ expanded: true }" class="bg-white shadow-lg rounded-xl p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-2 rounded-full mr-3">
                                <i class="fas fa-medal text-purple-600 text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Akreditasi</h2>
                        </div>
                        <button type="button" @click="expanded = !expanded"
                            class="bg-gray-100 hover:bg-gray-200 p-2 rounded-full focus:outline-none transition-colors duration-200">
                            <template x-if="expanded">
                                <i class="fas fa-chevron-up text-gray-600"></i>
                            </template>
                            <template x-if="!expanded">
                                <i class="fas fa-chevron-down text-gray-600"></i>
                            </template>
                        </button>
                    </div>
                    <div x-show="expanded" x-transition class="overflow-hidden rounded-xl border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">No</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">Fakultas</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">Jenjang</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">Prodi</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">Terakreditasi</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">No. Sertifikat</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">Tanggal Akreditasi</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">Tanggal Kadaluarsa</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">Batas Berlaku</th>
                                        <th class="px-2 py-3 text-left text-xs font-medium text-gray-700">Peringatan</th>
                                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($paginatedAkreditasi as $index => $akreditasi)
                                        <tr class="hover:bg-gray-50 text-xs">
                                            <td class="px-2 py-2 text-gray-600">{{ $loop->iteration }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ $akreditasi['prodi']['fakultas']['fakultas_alias'] }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ $akreditasi['jenjang']['jenjang_alias'] }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ $akreditasi['prodi']['prodi_nama'] }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ $akreditasi['status']['status_nama'] }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ $akreditasi['akre_sk'] }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ $akreditasi['akre_tglmulai'] }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ $akreditasi['akre_tglakhir'] }}</td>
                                            <td class="px-2 py-2 text-gray-600">{{ $akreditasi['batas_berlaku'] }}</td>
                                            <td class="px-2 py-2 text-gray-600">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                                    @if($akreditasi['peringatan_level'] == 'warning') bg-yellow-100 text-yellow-700
                                                    @elseif($akreditasi['peringatan_level'] == 'danger') bg-red-100 text-red-700
                                                    @else bg-blue-100 text-blue-700 @endif">
                                                    {{ $akreditasi['peringatan'] }}
                                                </span>
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                @if(isset($akreditasiRencanaForms[$akreditasi['akre_id']]) && 
                                                    (!empty($akreditasiRencanaForms[$akreditasi['akre_id']]['rencana_tindak_lanjut']) || 
                                                     !empty($akreditasiRencanaForms[$akreditasi['akre_id']]['target_penyelesaian'])))
                                                    <div class="flex justify-center space-x-1">
                                                        <button wire:click="openAkreditasiRencanaForm({{ $akreditasi['akre_id'] }}, '{{ $akreditasi['prodi']['prodi_nama'] }}')" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1 px-2 rounded">
                                                            <i class="fas fa-edit mr-1"></i>
                                                        </button>
                                                        <button 
                                                            onclick="if(confirm('Hapus rencana tindak lanjut ini?')) { @this.call('deleteAkreditasiRencanaTindakLanjut', {{ $akreditasi['akre_id'] }}); }"
                                                            class="bg-red-500 hover:bg-red-600 text-white text-xs py-1 px-2 rounded">
                                                            <i class="fas fa-trash mr-1"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <button wire:click="openAkreditasiRencanaForm({{ $akreditasi['akre_id'] }}, '{{ $akreditasi['prodi']['prodi_nama'] }}')" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1 px-2 rounded">
                                                        <i class="fas fa-clipboard-list mr-1"></i> RTL
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @if($akreditasiExpiringSoon->count() == 0)
                                        <tr>
                                            <td colspan="11" class="px-2 py-3 text-center text-gray-500">
                                                Tidak ada data akreditasi yang tersedia
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="px-6 py-4 bg-white border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Menampilkan
                                            <span class="font-medium">{{ $paginatedAkreditasi->firstItem() ?? 0 }}</span>
                                            sampai
                                            <span class="font-medium">{{ $paginatedAkreditasi->lastItem() ?? 0 }}</span>
                                            dari
                                            <span class="font-medium">{{ $paginatedAkreditasi->total() }}</span>
                                            data
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            <button wire:click="previousPage" @if($paginatedAkreditasi->onFirstPage()) disabled @endif
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 @if($paginatedAkreditasi->onFirstPage()) opacity-50 cursor-not-allowed @endif">
                                                <span class="sr-only">Previous</span>
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            
                                            @for ($i = 1; $i <= $paginatedAkreditasi->lastPage(); $i++)
                                                <button wire:click="gotoPage({{ $i }})" 
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium {{ $i == $paginatedAkreditasi->currentPage() ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                                    {{ $i }}
                                                </button>
                                            @endfor
                                            
                                            <button wire:click="nextPage" @if(!$paginatedAkreditasi->hasMorePages()) disabled @endif
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 @if(!$paginatedAkreditasi->hasMorePages()) opacity-50 cursor-not-allowed @endif">
                                                <span class="sr-only">Next</span>
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RTM Report Modal -->
    <div x-show="rtmReport" style="display: none" x-on:keydown.escape.window="rtmReport = false"
        class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-start w-full h-full bg-black/20 p-4">
        <div class="relative w-full max-w-xl my-8">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-5 border-b sticky top-0 bg-white z-10">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-2 rounded-full mr-3">
                            <i class="fas fa-file-alt text-red-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">
                            Laporan {{ $master }}
                        </h3>
                    </div>
                    <button type="button" @click="rtmReport = false"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-2 inline-flex items-center"
                        data-modal-hide="default-modal">
                        <i class="fas fa-times text-lg"></i>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 overflow-y-auto" style="max-height: calc(100vh - 150px);">
                    <form wire:submit.prevent="generateReport" class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 bg-blue-50 p-4 rounded-lg border border-blue-100 text-sm text-blue-700 mb-2">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="font-medium">Informasi RTM</p>
                                    <p>Nama RTM: <span class="font-medium">{{ $rtm->name }}</span></p>
                                    <p>Tahun: <span class="font-medium">{{ $rtm->tahun }}</span></p>
                                </div>
                            </div>
                        </div>
                        @if ($user->role->name == 'Universitas')

                        <!-- Fakultas filter for RTM report -->
                        <div class="col-span-12 mb-4">
                            <label for="report_fakultas" class="block text-sm font-medium text-gray-700 mb-1">Fakultas:</label>
                            <div class="relative">
                                <select id="report_fakultas" wire:model="selectedFakultas" 
                                    class="w-full p-3 border rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 bg-gray-50 hover:bg-white pl-4 pr-10">
                                    <option value="">Tingkat Universitas</option>
                                    @foreach($fakultas as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-500"></i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Pilih fakultas jika laporan ini spesifik untuk fakultas tertentu.</p>
                        </div>
                        @endif
                        
                        <!-- Name fields in one row -->
                        <div class="flex flex-col col-span-12">
                            <div class="bg-gray-100 p-3 rounded-lg mb-3">
                                <h3 class="font-medium text-gray-700 mb-2">Mengetahui 1</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="flex flex-col">
                                        <label for="mengetahui1_nama" class="text-sm font-medium text-gray-700 mb-1">Nama:</label>
                                        <input type="text" id="mengetahui1_nama" name="mengetahui1_nama" wire:model="rtmReport.mengetahui1_nama"
                                            placeholder="Nama mengetahui 1"
                                            class="p-3 text-sm rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                                        @error('rtmReport.mengetahui1_nama')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="mengetahui1_jabatan" class="text-sm font-medium text-gray-700 mb-1">Jabatan:</label>
                                        <input type="text" id="mengetahui1_jabatan" name="mengetahui1_jabatan" wire:model="rtmReport.mengetahui1_jabatan"
                                            placeholder="Jabatan mengetahui 1"
                                            class="p-3 text-sm rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                                        @error('rtmReport.mengetahui1_jabatan')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="mengetahui1_nip" class="text-sm font-medium text-gray-700 mb-1">NIP:</label>
                                        <input type="text" id="mengetahui1_nip" name="mengetahui1_nip" wire:model="rtmReport.mengetahui1_nip"
                                            placeholder="NIP mengetahui 1"
                                            class="p-3 text-sm rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                                        @error('rtmReport.mengetahui1_nip')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <div class="bg-gray-100 p-3 rounded-lg mb-3">
                                <h3 class="font-medium text-gray-700 mb-2">Mengetahui 2</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="flex flex-col">
                                        <label for="mengetahui2_nama" class="text-sm font-medium text-gray-700 mb-1">Nama:</label>
                                        <input type="text" id="mengetahui2_nama" name="mengetahui2_nama" wire:model="rtmReport.mengetahui2_nama"
                                            placeholder="Nama mengetahui 2"
                                            class="p-3 text-sm rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                                        @error('rtmReport.mengetahui2_nama')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="mengetahui2_jabatan" class="text-sm font-medium text-gray-700 mb-1">Jabatan:</label>
                                        <input type="text" id="mengetahui2_jabatan" name="mengetahui2_jabatan" wire:model="rtmReport.mengetahui2_jabatan"
                                            placeholder="Jabatan mengetahui 2"
                                            class="p-3 text-sm rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                                        @error('rtmReport.mengetahui2_jabatan')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="mengetahui2_nip" class="text-sm font-medium text-gray-700 mb-1">NIP:</label>
                                        <input type="text" id="mengetahui2_nip" name="mengetahui2_nip" wire:model="rtmReport.mengetahui2_nip"
                                            placeholder="NIP mengetahui 2"
                                            class="p-3 text-sm rounded-lg bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                                        @error('rtmReport.mengetahui2_nip')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="tahun_akademik" class="text-sm font-medium text-gray-700 mb-1">Tahun Akademik:</label>
                            <input type="text" id="tahun_akademik" name="tahun_akademik" wire:model="rtmReport.tahun_akademik"
                                placeholder="Contoh: 2023/2024"
                                class="p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                            @error('rtmReport.tahun_akademik')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Lampiran:</label>
                                <span class="text-xs text-gray-500">({{ count($lampiran) }} file)</span>
                            </div>
                            <button type="button" @click="lampiranModal = true; rtmReport = false" 
                                class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-paperclip mr-2 text-indigo-600"></i> Kelola File Lampiran
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Klik untuk menambah, melihat, atau menghapus lampiran</p>
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="pemimpin_rapat" class="text-sm font-medium text-gray-700 mb-1">Pemimpin Rapat:</label>
                            <input type="text" id="pemimpin_rapat" name="pemimpin_rapat" wire:model="rtmReport.pemimpin_rapat"
                                placeholder="Masukkan nama Pemimpin Rapat"
                                class="p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                            @error('rtmReport.pemimpin_rapat')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="notulis" class="text-sm font-medium text-gray-700 mb-1">Notulis:</label>
                            <input type="text" id="notulis" name="notulis" wire:model="rtmReport.notulis"
                                placeholder="Masukkan nama Notulis"
                                class="p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                            @error('rtmReport.notulis')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Date and time in separate rows -->
                        <div class="flex flex-col col-span-12">
                            <label for="tanggal_pelaksanaan" class="text-sm font-medium text-gray-700 mb-1">Tanggal Pelaksanaan:</label>
                            <input type="date" id="tanggal_pelaksanaan" name="tanggal_pelaksanaan" wire:model="rtmReport.tanggal_pelaksanaan"
                                class="p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                            @error('rtmReport.tanggal_pelaksanaan')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="waktu_pelaksanaan" class="text-sm font-medium text-gray-700 mb-1">Waktu Pelaksanaan:</label>
                            <input type="time" id="waktu_pelaksanaan" name="waktu_pelaksanaan" wire:model="rtmReport.waktu_pelaksanaan"
                                class="p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                            @error('rtmReport.waktu_pelaksanaan')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="tempat_pelaksanaan" class="text-sm font-medium text-gray-700 mb-1">Tempat Pelaksanaan:</label>
                            <input type="text" id="tempat_pelaksanaan" name="tempat_pelaksanaan" wire:model="rtmReport.tempat_pelaksanaan"
                                placeholder="Masukkan tempat pelaksanaan"
                                class="p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200">
                            @error('rtmReport.tempat_pelaksanaan')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="agenda" class="text-sm font-medium text-gray-700 mb-1">Agenda:</label>
                            <textarea id="agenda" name="agenda" wire:model="rtmReport.agenda"
                                placeholder="Masukkan tema agenda rapat"
                                class="p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200 min-h-[120px]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Masukkan tema rapat (misalnya: Hasil AMI 2024)</p>
                            @error('rtmReport.agenda')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="agenda_kegiatan" class="text-sm font-medium text-gray-700 mb-1">Agenda Kegiatan:</label>
                            <textarea id="agenda_kegiatan" name="agenda_kegiatan" wire:model.defer="rtmReport.agenda_kegiatan"
                                placeholder="Masukkan agenda kegiatan rapat. Gunakan format daftar atau paragraf sesuai kebutuhan."
                                class="rich-editor p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200 min-h-[150px]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Gunakan toolbar untuk memformat teks, membuat daftar berurut atau tidak berurut, dll.</p>
                            @error('rtmReport.agenda_kegiatan')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="peserta" class="text-sm font-medium text-gray-700 mb-1">Peserta:</label>
                            <textarea id="peserta" name="peserta" wire:model.defer="rtmReport.peserta"
                                placeholder="Masukkan daftar peserta rapat. Gunakan format daftar atau paragraf sesuai kebutuhan."
                                class="rich-editor p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200 min-h-[150px]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Gunakan toolbar untuk memformat teks, membuat daftar berurut atau tidak berurut, dll.</p>
                            @error('rtmReport.peserta')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-12">
                            <h3 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">Konten Tambahan untuk Laporan</h3>
                            <p class="text-sm text-gray-600 mb-3">Konten berikut akan digunakan dalam Bab 1 laporan RTM. Jika dibiarkan kosong, akan menggunakan konten default.</p>
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="tujuan" class="text-sm font-medium text-gray-700 mb-1">Tujuan:</label>
                            <textarea id="tujuan" name="tujuan" wire:model.defer="rtmReport.tujuan"
                                placeholder="Masukkan tujuan RTM. Format sesuai kebutuhan."
                                class="rich-editor p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200 min-h-[150px]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Gunakan toolbar untuk memformat teks, membuat daftar berurut atau tidak berurut, dll.</p>
                            @error('rtmReport.tujuan')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="hasil" class="text-sm font-medium text-gray-700 mb-1">Hasil:</label>
                            <textarea id="hasil" name="hasil" wire:model.defer="rtmReport.hasil"
                                placeholder="Masukkan hasil RTM. Format sesuai kebutuhan."
                                class="rich-editor p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200 min-h-[150px]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Gunakan toolbar untuk memformat teks, membuat daftar berurut atau tidak berurut, dll.</p>
                            @error('rtmReport.hasil')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="kesimpulan" class="text-sm font-medium text-gray-700 mb-1">Kesimpulan:</label>
                            <textarea id="kesimpulan" name="kesimpulan" wire:model.defer="rtmReport.kesimpulan"
                                placeholder="Masukkan kesimpulan RTM. Format sesuai kebutuhan."
                                class="rich-editor p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200 min-h-[150px]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Gunakan toolbar untuk memformat teks, membuat daftar berurut atau tidak berurut, dll.</p>
                            @error('rtmReport.kesimpulan')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="flex flex-col col-span-12">
                            <label for="penutup" class="text-sm font-medium text-gray-700 mb-1">Penutup:</label>
                            <textarea id="penutup" name="penutup" wire:model.defer="rtmReport.penutup"
                                placeholder="Masukkan penutup RTM. Format sesuai kebutuhan."
                                class="rich-editor p-3.5 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500 border border-gray-200 min-h-[150px]"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Gunakan toolbar untuk memformat teks, membuat daftar berurut atau tidak berurut, dll.</p>
                            @error('rtmReport.penutup')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-12 flex flex-col">
                            <!-- Generation Progress Info (visible when generating report) -->
                            <div wire:loading wire:target="generateReport" class="flex items-center mb-4 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                                <i class="fas fa-spinner fa-spin text-indigo-600 mr-3 text-xl"></i>
                                <div>
                                    <p class="font-medium text-indigo-700">Sedang Membuat Laporan RTM</p>
                                    <p class="text-sm text-indigo-600">Mohon tunggu, proses ini membutuhkan waktu beberapa saat...</p>
                                </div>
                            </div>
                            
                            <!-- Buttons -->
                            <div class="flex justify-end mt-3">
                                <x-button class="bg-gray-200 hover:bg-gray-300 text-gray-700 mr-2" type="button" @click="rtmReport = false">
                                    Batal
                                </x-button>
                                <x-button class="bg-blue-600 hover:bg-blue-700 text-white shadow-md mr-2" type="button" wire:click="saveReport">
                                    <span wire:loading.remove wire:target="saveReport"><i class="fas fa-save mr-1"></i> Simpan Laporan</span>
                                    <span wire:loading wire:target="saveReport" class="flex items-center">
                                        <i class="fas fa-circle-notch animate-spin mr-1"></i> Menyimpan...
                                    </span>
                                </x-button>
                                <x-button class="bg-red-600 hover:bg-red-700 text-white shadow-md" type="submit" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="generateReport"><i class="fas fa-file-download mr-1"></i> Generate Laporan</span>
                                    <span wire:loading wire:target="generateReport" class="flex items-center">
                                        <i class="fas fa-circle-notch animate-spin mr-1"></i> Memproses...
                                    </span>
                                </x-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- RTM Lampiran Modal -->
    <div x-show="lampiranModal" style="display: none" x-on:keydown.escape.window="lampiranModal = false"
        class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-start w-full h-full bg-black/20 p-4">
        <div class="relative w-full max-w-xl my-8">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-5 border-b sticky top-0 bg-white z-10">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-full mr-3">
                            <i class="fas fa-file-alt text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">
                            Kelola Lampiran RTM
                        </h3>
                    </div>
                    <button type="button" @click="lampiranModal = false"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-2 inline-flex items-center">
                        <i class="fas fa-times text-lg"></i>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 overflow-y-auto" style="max-height: calc(100vh - 150px);">
                    <form wire:submit.prevent="uploadLampiran" class="mb-6 border-b pb-6">
                        <h4 class="text-lg font-medium text-gray-800 mb-4">Tambah Lampiran Baru</h4>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div class="flex flex-col">
                                <label for="judul" class="text-sm font-medium text-gray-700 mb-1">Judul Lampiran:</label>
                                <input type="text" id="judul" name="judul" wire:model="newLampiran.judul"
                                    placeholder="Masukkan judul lampiran"
                                    class="p-3 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 border border-gray-200">
                                @error('newLampiran.judul')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <!-- Fakultas filter for lampiran -->
                            @if ($user->role->name == 'Universitas')
                            <div class="flex flex-col">
                                <label for="lampiran_fakultas" class="text-sm font-medium text-gray-700 mb-1">Fakultas:</label>
                                <div class="relative">
                                    <select id="lampiran_fakultas" wire:model="selectedFakultas" wire:change="loadLampiran"
                                        class="w-full p-3 border rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 bg-gray-50 hover:bg-white pl-4 pr-10">
                                        <option value="">Tingkat Universitas</option>
                                        @foreach($fakultas as $f)
                                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-500"></i>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-sm text-gray-500">Pilih fakultas untuk filter lampiran atau buat lampiran khusus fakultas.</p>
                                    <button type="button" wire:click="resetFilter" class="text-xs text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times-circle mr-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                            @endif
                            
                            <div class="flex flex-col">
                                <label for="file" class="text-sm font-medium text-gray-700 mb-1">File Lampiran:</label>
                                <input type="file" id="file" name="file" wire:model="newLampiran.file"
                                    class="p-3 text-sm rounded-lg bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 border border-gray-200">
                                <div wire:loading wire:target="newLampiran.file" class="text-xs text-blue-600 mt-1">
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Mengupload file...
                                </div>
                                @error('newLampiran.file')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Format file: PDF, docx, xlsx, pptx (Maks: 10MB)</p>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" wire:click="uploadLampiran" class="bg-green-600 hover:bg-green-700 text-black font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center">
                                    <span wire:loading.remove wire:target="uploadLampiran"><i class="fas fa-plus mr-1"></i> Tambah Lampiran</span>
                                    <span wire:loading wire:target="uploadLampiran" class="flex items-center">
                                        <i class="fas fa-circle-notch animate-spin mr-1"></i> Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-medium text-gray-800">Daftar Lampiran</h4>
                            @if($selectedFakultas)
                                <div class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full flex items-center">
                                    <span>Filter: {{ collect($fakultas)->firstWhere('id', $selectedFakultas)->name }}</span>
                                    <button wire:click="resetFilter" class="ml-1 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        @if(count($lampiran) > 0)
                            <div class="space-y-3">
                                @foreach($lampiran as $item)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-start">
                                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                                @if(in_array($item->file_type, ['pdf']))
                                                    <i class="fas fa-file-pdf text-red-500"></i>
                                                @elseif(in_array($item->file_type, ['doc', 'docx']))
                                                    <i class="fas fa-file-word text-blue-500"></i>
                                                @elseif(in_array($item->file_type, ['xls', 'xlsx']))
                                                    <i class="fas fa-file-excel text-green-500"></i>
                                                @elseif(in_array($item->file_type, ['ppt', 'pptx']))
                                                    <i class="fas fa-file-powerpoint text-orange-500"></i>
                                                @else
                                                    <i class="fas fa-file text-gray-500"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="font-medium text-gray-800">{{ $item->judul }}</h5>
                                                <p class="text-xs text-gray-500">{{ $item->file_name }} ({{ round($item->file_size / 1024) }} KB)</p>
                                                <p class="text-xs text-gray-500">Fakultas: {{ $item->fakultas_id ? $item->fakultas->name : 'Universitas' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" wire:click="deleteLampiran({{ $item->id }})" class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500">
                                Belum ada lampiran tersimpan
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="button" @click="lampiranModal = false" 
                            class="bg-blue-600 hover:bg-blue-700 text-black font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Akreditasi Rencana Tindak Lanjut Modal -->
    <div x-show="akreditasiFormIsOpen" style="display: none" x-data="{ akreditasiFormIsOpen: @entangle('akreditasiFormIsOpen') }" x-on:keydown.escape.window="akreditasiFormIsOpen = false"
        class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full bg-black/30 backdrop-blur-sm">
        <div class="relative p-4 w-full max-w-[70%]">
            <!-- Modal content -->
            <div class="relative bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white z-10">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-2 rounded-full mr-3">
                            <i class="fas fa-clipboard-list text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                Analisis Masalah dan Pemecahannya
                            </h3>
                            <p class="text-sm text-gray-600">{{ $currentAkreditasiProdi }}</p>
                        </div>
                    </div>
                    <button type="button" @click="akreditasiFormIsOpen = false"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-2 inline-flex items-center"
                        data-modal-hide="default-modal">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-5 space-y-4">
                    @if($currentAkreditasiId)
                    <form wire:submit.prevent="saveAkreditasiRencanaTindakLanjut">
                        <div class="mb-4">
                            <label for="rencana_tindak_lanjut" class="block text-sm font-medium text-gray-700 mb-2">Analisis Masalah dan Pemecahannya:</label>
                            <textarea id="rencana_tindak_lanjut" wire:model="akreditasiRencanaForms.{{ $currentAkreditasiId }}.rencana_tindak_lanjut" rows="4"
                                class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500"
                                placeholder="Masukkan rencana tindak lanjut"></textarea>
                            @error('akreditasiRencanaForms.' . $currentAkreditasiId . '.rencana_tindak_lanjut')
                                <p class="text-red-500 text-xs mt-1">{{ session('errors')->first('akreditasiRencanaForms.'.$currentAkreditasiId.'.rencana_tindak_lanjut') }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="target_penyelesaian" class="block text-sm font-medium text-gray-700 mb-2">Target Penyelesaian:</label>
                            <input type="text" id="target_penyelesaian" wire:model="akreditasiRencanaForms.{{ $currentAkreditasiId }}.target_penyelesaian"
                                class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500"
                                placeholder="Contoh: Desember 2024">
                            @error('akreditasiRencanaForms.' . $currentAkreditasiId . '.target_penyelesaian')
                                <p class="text-red-500 text-xs mt-1">{{ session('errors')->first('akreditasiRencanaForms.'.$currentAkreditasiId.'.target_penyelesaian') }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" @click="akreditasiFormIsOpen = false"
                                class="px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-black font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span wire:loading.remove wire:target="saveAkreditasiRencanaTindakLanjut">Simpan</span>
                                <span wire:loading wire:target="saveAkreditasiRencanaTindakLanjut">
                                    <i class="fas fa-spinner fa-spin"></i> Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
       
        <script src="https://cdn.tiny.cloud/1/{{ env('TINY_API', 'no-api-key') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const elements = document.querySelectorAll('.multi-select');
                elements.forEach(el => new Choices(el, {
                    removeItemButton: true,
                    allowHTML: true
                }));

                // Initialize TinyMCE on textareas with the rich-editor class
                function initRichTextEditor() {
                    if (document.querySelectorAll('.rich-editor').length > 0) {
                        tinymce.remove();
                        tinymce.init({
                            selector: '.rich-editor',
                            height: 300,
                            menubar: false,
                            plugins: [
                                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                'insertdatetime', 'media', 'table', 'help', 'wordcount'
                            ],
                            toolbar: 'undo redo | blocks | ' +
                                'bold italic backcolor | alignleft aligncenter ' +
                                'alignright alignjustify | bullist numlist outdent indent | ' +
                                'removeformat | help',
                            setup: function (editor) {
                                editor.on('change', function () {
                                    editor.save(); // This triggers the change event on the textarea
                                    const textareaId = editor.getElement().id;
                                    const textarea = document.getElementById(textareaId);
                                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                                });
                            }
                        });
                    }
                }

                // Watch for modal open events to initialize the editor
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.attributeName === 'style' && 
                            mutation.target.style.display !== 'none' && 
                            mutation.target.querySelector('.rich-editor')) {
                            setTimeout(initRichTextEditor, 300);
                        }
                    });
                });

                // Monitor the modal container for display changes
                const rtmReportModal = document.querySelector('[x-show="rtmReport"]');
                if (rtmReportModal) {
                    observer.observe(rtmReportModal, { attributes: true });
                }

                // Setup for alpinejs modals
                window.addEventListener('alpine:initialized', () => {
                    window.Alpine.effect(() => {
                        if (window.Alpine.store('rtmReport')?.visible) {
                            setTimeout(initRichTextEditor, 300);
                        }
                    });
                });

                // Livewire hook for reinitializing after component updates
                document.addEventListener('livewire:load', function () {
                    Livewire.hook('message.processed', (message, component) => {
                        if (rtmReportModal && rtmReportModal.style.display !== 'none') {
                            setTimeout(initRichTextEditor, 300);
                        }
                    });
                });
            });
        </script>
    @endpush
</main>
