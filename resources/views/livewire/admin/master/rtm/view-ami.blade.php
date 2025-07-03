<main class="bg-gradient-to-br from-blue-50 via-indigo-50 to-gray-100 min-h-screen" x-data="{
    showToast: {{ session()->has('toastMessage') ? 'true' : 'false' }},
    toastMessage: '{{ session('toastMessage') }}',
    toastType: '{{ session('toastType') }}',
    expandedSections: {},
    formIsOpen: false,
    currentIndicatorId: null,
    currentIndicatorDesc: null,
    isLoading: false
}" x-init="if (showToast) { setTimeout(() => showToast = false, 5000); }">
    <!-- Toast -->
    <div x-show="showToast" x-transition.opacity.duration.300ms
        :class="toastType === 'success' ? 'text-green-600' : 'text-red-600'"
        class="fixed top-20 right-5 z-50 flex items-center w-full max-w-xs p-4 rounded-lg shadow-xl bg-white/95 backdrop-blur-md border border-gray-100">
        <div :class="toastType === 'success' ? 'text-green-600 bg-green-100 border border-green-200' : 'text-red-600 bg-red-100 border border-red-200'"
            class="inline-flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-full shadow-sm">
            <span>
                <i :class="toastType === 'success' ? 'fas fa-check' : 'fas fa-exclamation'" class="text-xl"></i>
            </span>
        </div>
        <div class="ml-4 text-sm font-medium" x-text="toastMessage"></div>
        <button type="button" @click="showToast = false"
            class="ml-auto p-1.5 text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-full focus:outline-none transition-colors duration-200" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <section class="max-w-7xl w-full mx-auto px-8 pt-28 pb-16">
        <!-- Header with breadcrumb and navigation -->
        <div class="mb-12">
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 bg-white/80 backdrop-blur-md px-5 py-2.5 rounded-xl shadow-md">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors duration-200">
                            <i class="fas fa-home mr-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                            <a href="{{ route('dashboard.master.rtm.index') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors duration-200">RTM</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                            <a href="{{ route('dashboard.master.rtm.detail', $rtm->id) }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors duration-200">{{ $rtm->name }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                            <span class="text-sm font-medium text-indigo-600">Data AMI</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-stretch md:justify-between gap-6 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-lg flex-grow hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-gradient-to-br from-white to-indigo-50 border border-indigo-50">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-200 to-blue-200 rounded-lg flex items-center justify-center mr-5 shadow-md">
                            <i class="fas fa-chart-line text-xl text-black"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 mb-1.5">{{ $anchorName }}</h1>
                            <p class="text-gray-600 flex items-center text-base">
                                <i class="fas fa-clipboard-list mr-2 text-indigo-500"></i> 
                                RTM: {{ $rtm->name }} ({{ $rtm->tahun }})
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Back to RTM Details Button -->
                <a href="{{ route('dashboard.master.rtm.detail', $rtm->id) }}" class="inline-flex items-center justify-center px-6 py-4 bg-white hover:bg-gray-50 text-indigo-600 font-medium rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl hover:bg-gradient-to-r hover:from-indigo-50 hover:to-white text-base border border-indigo-100">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Detail RTM
                </a>
            </div>
        </div>

        @if ($user->role->name == 'Universitas')
        <!-- Filter Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg mb-10 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-gradient-to-br from-white to-indigo-50 border border-indigo-50">
            <div class="flex items-center mb-5">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-200 to-blue-200 rounded-lg flex items-center justify-center mr-4 shadow-md">
                    <i class="fas fa-filter text-lg text-black"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Filter Data</h2>
            </div>

            <div class="flex flex-col md:flex-row md:items-end gap-6">
                <div class="flex-grow">
                    <label for="fakultas" class="text-base font-medium text-gray-700 mb-2.5 block">Fakultas:</label>
                    <div class="relative">
                        <select id="fakultas" wire:model.live="selectedFakultas"
                            class="w-full p-3.5 text-base border border-gray-300 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 hover:bg-white shadow-md"
                            {{ $isLoading ? 'disabled' : '' }}>
                            <option value="">Semua Fakultas</option>
                            @foreach ($fakultas as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            @if($isLoading)
                                <i class="fas fa-spinner animate-spin text-gray-500"></i>
                            @else
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="md:w-1/4">
                    <button wire:click="resetFilter" class="w-full h-[50px] px-6 bg-gradient-to-r from-indigo-200 to-blue-200 hover:from-indigo-300 hover:to-blue-300 text-black font-medium rounded-lg transition-all duration-300 flex items-center justify-center text-base shadow-md hover:shadow-lg">
                        <span wire:loading.remove wire:target="resetFilter">
                            <i class="fas fa-redo-alt mr-2"></i> Reset Filter
                        </span>
                        <span wire:loading wire:target="resetFilter" class="flex items-center">
                            <i class="fas fa-spinner animate-spin mr-2"></i> Mereset...
                        </span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 border border-indigo-50">
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-200 to-blue-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white/90 backdrop-blur-md rounded-lg flex items-center justify-center mr-4 shadow-md">
                            <i class="fas fa-chart-line text-lg text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 mb-1">Data Audit Mutu Internal</h2>
                            <p class="text-base text-gray-700">{{ $selectedFakultas ? App\Models\Fakultas::find($selectedFakultas)->name : 'Semua Fakultas' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white px-4 py-3 rounded-lg shadow-md">
                            <div class="text-sm text-gray-500 mb-1">Rata-Rata Capaian Kinerja</div>
                            <div class="flex items-center justify-center">
                                <div class="text-2xl font-bold {{ $overallAverage >= 80 ? 'text-green-600' : ($overallAverage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $overallAverage }}%
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <button class="px-5 py-3 bg-white hover:bg-gray-100 text-indigo-700 rounded-lg transition-colors duration-200 text-base flex items-center shadow-md hover:shadow-lg text-center">
                        <i class="fas fa-file-export mr-2 text-center items-center"></i> Export Data
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($isLoading)
                    <!-- Loading State -->
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500/20 to-blue-500/20 rounded-full flex items-center justify-center mb-6">
                            <div class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                        <h3 class="text-2xl font-medium text-gray-700 mb-3">Memuat Data AMI</h3>
                        <p class="text-gray-500 max-w-md text-base">Sedang mengambil data Audit Mutu Internal, mohon tunggu sebentar...</p>
                    </div>
                @elseif (count($amiData) > 0)
                    @foreach ($amiData as $category => $items)
                        <div x-data="{ open: true }" class="border-b border-gray-200">
                            <!-- Category header (clickable) -->
                            <div @click="open = !open" class="bg-gradient-to-r from-gray-50 to-white px-7 py-5 cursor-pointer hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-200 to-blue-200 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                            <i class="fas fa-folder text-black"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-800">{{ $category }}</h3>
                                    </div>
                                    <div class="flex items-center space-x-5">
                                        <div class="text-sm font-medium text-gray-600 bg-gray-100 px-3 py-1.5 rounded-full shadow-sm flex items-center space-x-2">
                                            <span>{{ count($items) }} item(s)</span>
                                        </div>
                                        <div class="px-3 py-1.5 rounded-full shadow-sm {{ $categoryAverages[$category] >= 80 ? 'bg-green-100 text-green-700' : ($categoryAverages[$category] >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            <span class="text-sm font-medium">Rata-rata: {{ $categoryAverages[$category] }}%</span>
                                        </div>
                                        <button class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-indigo-100 transition-colors duration-200">
                                            <i class="fas text-indigo-600" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Category content (collapsible) -->
                            <div x-show="open" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border">No</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border w-1/3">Pernyataan Standar</th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 border">Sesuai</th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 border">Tidak Sesuai</th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 border">Capaian Kinerja</th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 border">Analisis Masalah dan Pemecahannya</th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 border">Target Penyelesaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $indicator)
                                            <tr class="hover:bg-indigo-50/30 transition-colors duration-150 border-b border-gray-200">
                                                <td class="px-6 py-4 text-base text-gray-600 border-r text-center">
                                                    <span class="font-medium">{{ $indicator['code'] }}</span>
                                                </td>
                                                <td class="px-6 py-4 text-base text-gray-600 border-r leading-relaxed">
                                                    {{ $indicator['desc'] }}
                                                </td>
                                                <td class="px-6 py-4 text-base text-gray-600 border-r text-center">
                                                    @if(is_array($indicator['sesuai']) && count($indicator['sesuai']) > 0)
                                                        <button 
                                                            wire:click="openProgramModal('sesuai', '{{ json_encode($indicator['sesuai']) }}', '{{ $indicator['code'] }}', '{{ $indicator['desc'] }}')"
                                                            class="px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 rounded-full transition-colors duration-200 font-medium shadow-sm hover:shadow-md">
                                                            {{ count($indicator['sesuai']) }} <i class="fas fa-eye ml-1 text-xs"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-gray-400">0</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-base text-gray-600 border-r text-center">
                                                    @if(is_array($indicator['tidak_sesuai']) && count($indicator['tidak_sesuai']) > 0)
                                                        <button 
                                                            wire:click="openProgramModal('tidak_sesuai', '{{ json_encode($indicator['tidak_sesuai']) }}', '{{ $indicator['code'] }}', '{{ $indicator['desc'] }}')"
                                                            class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-full transition-colors duration-200 font-medium shadow-sm hover:shadow-md">
                                                            {{ count($indicator['tidak_sesuai']) }} <i class="fas fa-eye ml-1 text-xs"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-gray-400">0</span>
                                                    @endif
                                                </td>
                                              
                                                <td class="px-6 py-4 text-base border-r text-center font-medium">
                                                    <span class="px-3 py-1 rounded-full {{ $indicator['score'] >= 80 ? 'bg-green-100 text-green-700' : ($indicator['score'] >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                        {{ $indicator['score'] }}%
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-base text-gray-600 border-r">
                                                    @if(isset($rencanaForms[$indicator['id']]) && !empty($rencanaForms[$indicator['id']]['rencana_tindak_lanjut']))
                                                        <div class="flex justify-between items-center gap-2">
                                                            <div class="text-gray-800 pr-2">{{ $rencanaForms[$indicator['id']]['rencana_tindak_lanjut'] }}</div>
                                                            <div class="flex gap-1">
                                                                <button 
                                                                    wire:click="openRencanaForm('{{ $indicator['id'] }}', '{{ $indicator['desc'] }}')"
                                                                    class="text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 py-1 px-2 rounded-full transition-colors duration-200 shadow-sm hover:shadow-md flex-shrink-0">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <button 
                                                                    onclick="if(confirm('Hapus rencana tindak lanjut ini?')) { @this.call('deleteRencanaTindakLanjut', '{{ $indicator['id'] }}'); }"
                                                                    class="text-xs bg-red-100 hover:bg-red-200 text-red-700 py-1 px-2 rounded-full transition-colors duration-200 shadow-sm hover:shadow-md flex-shrink-0">
                                                                    <i class="fas fa-trash text-xs"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <button 
                                                            wire:click="openRencanaForm('{{ $indicator['id'] }}', '{{ $indicator['desc'] }}')"
                                                            class="text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-700 py-1.5 px-3 rounded-full transition-colors duration-200 shadow-sm hover:shadow-md">
                                                            <i class="fas fa-plus text-xs mr-1"></i> Tambah
                                                        </button>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-base text-gray-600">
                                                    @if(isset($rencanaForms[$indicator['id']]) && !empty($rencanaForms[$indicator['id']]['target_penyelesaian']))
                                                        <div class="text-gray-800">{{ $rencanaForms[$indicator['id']]['target_penyelesaian'] }}</div>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-500/20 to-blue-500/20 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-search text-indigo-500 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-medium text-gray-700 mb-3">Tidak ada data tersedia</h3>
                        <p class="text-gray-500 max-w-md text-base">Tidak ada data AMI yang tersedia untuk periode ini. Silakan coba filter yang berbeda atau periksa kembali periode AMI Anda.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Rencana Tindak Lanjut Modal -->
        <div x-show="$wire.formIsOpen" style="display: none" x-on:keydown.escape.window="$wire.closeRencanaForm()"
            class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full bg-black/60 backdrop-blur-sm">
            <div class="relative p-4 w-full max-w-2xl max-h-full" @click.outside="$wire.closeRencanaForm()">
                <!-- Modal content -->
                <div class="relative bg-white rounded-xl shadow-2xl transform transition-all animate-fadeIn">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-5 border-b sticky top-0 bg-gradient-to-r from-indigo-100 to-white z-10">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-200 to-blue-200 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                <i class="fas fa-clipboard-list text-black"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">
                                Analisis Masalah dan Pemecahannya
                            </h3>
                        </div>
                        <button type="button" @click="$wire.closeRencanaForm()"
                            class="text-gray-400 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-2.5 inline-flex items-center shadow-sm">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    
                    <!-- Modal body -->
                    <div class="p-6">
                        @if($currentIndicatorId)
                            <div class="mb-5 p-4 bg-indigo-50 rounded-xl border border-indigo-100 shadow-sm">
                                <h4 class="text-sm font-medium text-indigo-600 mb-1.5">Indikator:</h4>
                                <p class="text-gray-800">{{ $currentIndicatorDesc }}</p>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label for="rencana_tindak_lanjut" class="block text-sm font-medium text-gray-700 mb-2">Rencana Tindak Lanjut <span class="text-red-500">*</span></label>
                                    <textarea 
                                        id="rencana_tindak_lanjut" 
                                        wire:model.defer="rencanaForms.{{ $currentIndicatorId }}.rencana_tindak_lanjut"
                                        class="w-full p-3.5 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 hover:bg-white shadow-sm"
                                        rows="4"
                                        placeholder="Masukkan rencana tindak lanjut..."
                                    ></textarea>
                                    @error('rencanaForms.'.$currentIndicatorId.'.rencana_tindak_lanjut')
                                        <span class="text-red-500 text-sm mt-1.5 block">{{ session('errors')->first('rencanaForms.'.$currentIndicatorId.'.rencana_tindak_lanjut') }}</span>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="target_penyelesaian" class="block text-sm font-medium text-gray-700 mb-2">Target Penyelesaian <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        id="target_penyelesaian" 
                                        wire:model.defer="rencanaForms.{{ $currentIndicatorId }}.target_penyelesaian"
                                        class="w-full p-4 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 hover:bg-white shadow-sm text-lg"
                                        placeholder="Masukkan target penyelesaian..."
                                    >
                                    @error('rencanaForms.'.$currentIndicatorId.'.target_penyelesaian')
                                        <span class="text-red-500 text-sm mt-1.5 block">{{ session('errors')->first('rencanaForms.'.$currentIndicatorId.'.target_penyelesaian') }}</span>
                                    @enderror
                                </div>
                                
                                <div class="pt-4 flex justify-end space-x-3">
                                    <button 
                                        type="button" 
                                        @click="$wire.closeRencanaForm()"
                                        class="px-5 py-2.5 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors duration-200 font-medium shadow-sm hover:shadow-md"
                                    >
                                        <i class="fas fa-times mr-1.5"></i> Batal
                                    </button>
                                    <button 
                                        type="button" 
                                        wire:click="saveRencanaTindakLanjut"
                                        class="px-5 py-2.5 text-black bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 rounded-lg transition-colors duration-200 font-medium shadow-md hover:shadow-lg"
                                    >
                                        <span wire:loading.remove wire:target="saveRencanaTindakLanjut">
                                            <i class="fas fa-save mr-1.5"></i> Simpan
                                        </span>
                                        <span wire:loading wire:target="saveRencanaTindakLanjut" class="flex items-center">
                                            <i class="fas fa-spinner animate-spin mr-1.5"></i> Menyimpan...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Program Modal -->
        <div x-show="$wire.programModalOpen" style="display: none" x-on:keydown.escape.window="$wire.closeProgramModal()"
            class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full bg-black/60 backdrop-blur-sm">
            <div class="relative p-4 w-full max-w-3xl max-h-full" @click.outside="$wire.closeProgramModal()">
                <!-- Modal content -->
                <div class="relative bg-white rounded-xl shadow-2xl transform transition-all animate-fadeIn">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-5 border-b sticky top-0 bg-gradient-to-r from-indigo-100 to-white z-10">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 shadow-md"
                                :class="{
                                    'bg-gradient-to-br from-green-200 to-green-300': $wire.programType === 'sesuai',
                                    'bg-gradient-to-br from-red-200 to-red-300': $wire.programType === 'tidak_sesuai'
                                }">
                                <i class="fas fa-list-check text-black"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">
                                <span x-show="$wire.programType === 'sesuai'">Item Sesuai</span>
                                <span x-show="$wire.programType === 'tidak_sesuai'">Item Tidak Sesuai</span>
                            </h3>
                        </div>
                        <button type="button" @click="$wire.closeProgramModal()"
                            class="text-gray-400 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-2.5 inline-flex items-center shadow-sm">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    
                    <!-- Modal body -->
                    <div class="p-6">
                        <!-- Indicator information -->
                        @if($indicatorCode && $indicatorDesc)
                            <div class="mb-5 p-4 bg-indigo-50 rounded-xl border border-indigo-100 shadow-sm">
                                <div class="flex items-center mb-2">
                                    <div class="bg-indigo-200 text-indigo-700 px-3 py-1 rounded-lg mr-3 shadow-sm">
                                        <span class="font-medium">{{ $indicatorCode }}</span>
                                    </div>
                                    <h4 class="text-sm font-medium text-indigo-600">Indikator</h4>
                                </div>
                                <p class="text-gray-800">{{ $indicatorDesc }}</p>
                            </div>
                         @endif

                         @if(empty($programItems))
                             <div class="flex flex-col items-center justify-center py-10 text-center">
                                 <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                     <i class="fas fa-info text-gray-400 text-2xl"></i>
                                 </div>
                                 <h4 class="text-lg font-medium text-gray-700 mb-2">Tidak ada data</h4>
                                 <p class="text-gray-500 max-w-md">Tidak ada data program yang tersedia untuk kategori ini.</p>
                             </div>
                         @else
                             <div class="space-y-4">
                                 @foreach($programItems as $index => $item)
                                     <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-200 hover:bg-indigo-50/30 transition-colors duration-200 shadow-sm">
                                         <div class="flex items-start">
                                             <div class="w-8 h-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center mr-3 font-medium text-sm">
                                                 {{ $index + 1 }}
                                             </div>
                                             <div class="flex-1">
                                                 <h4 class="text-base font-semibold text-gray-800 mb-2">{{ $item['program_name'] ?? 'Program #' . $item['id'] }}</h4>
                                                 @if(isset($item['description']) && !empty($item['description']))
                                                     <p class="text-gray-600">{{ $item['description'] }}</p>
                                                 @endif
                                                 
                                                 <div class="mt-3 flex flex-wrap gap-2">
                                                     @if(isset($item['status']))
                                                         <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium 
                                                             {{ $item['status'] == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                                             <i class="fas {{ $item['status'] == 'active' ? 'fa-check-circle' : 'fa-circle' }} mr-1"></i>
                                                             {{ $item['status'] == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                                         </span>
                                                     @endif
                                                     
                                                     @if(isset($item['category']) && !empty($item['category']))
                                                         <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                                             <i class="fas fa-tag mr-1"></i>
                                                             {{ $item['category'] }}
                                                         </span>
                                                     @endif
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         @endif
                    </div>
                    
                    <!-- Modal footer -->
                    <div class="flex items-center justify-end p-5 border-t">
                        <button 
                            type="button" 
                            @click="$wire.closeProgramModal()"
                            class="px-5 py-2.5 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors duration-200 font-medium shadow-sm hover:shadow-md"
                        >
                            <i class="fas fa-times mr-1.5"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main> 