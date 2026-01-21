<main class="bg-[#f9fafc] min-h-screen" x-data="{
    addModal: false,
    editModal: false,
    showToast: {{ session()->has('toastMessage') ? 'true' : 'false' }},
    toastMessage: '{{ session('toastMessage') }}',
    toastType: '{{ session('toastType') }}'
}" x-init="if (showToast) {
    setTimeout(() => showToast = false, 5000);
}">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    @endpush
    <!-- Toast -->
    <div x-show="showToast" x-transition
        :class="toastType === 'success' ? 'text-color-success-500' : 'text-color-danger-500'"
        class="fixed top-24 right-5 z-50 flex items-center w-full max-w-xs p-4 rounded-lg shadow bg-white" role="alert">
        <div :class="toastType === 'success' ? 'text-color-success-500 bg-color-success-100' :
            'text-color-danger-500 bg-color-danger-100'"
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
            <span>
                <i :class="toastType === 'success' ? 'fas fa-check' : 'fas fa-exclamation'"></i>
            </span>
        </div>
        <div class="ml-3 text-sm font-normal" x-text="toastMessage"></div>
        <button type="button" @click="showToast = false"
            class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8"
            aria-label="Close">
            <span><i class="fas fa-times"></i></span>
        </button>
    </div>
    <section class="max-w-screen-xl w-full mx-auto px-4 pt-24">

        <div
            class="mt-4 p-6 bg-white flex flex-col lg:flex-row lg:items-center gap-y-2 justify-between rounded-lg border border-slate-100 shadow-sm">
            <div>
                <h1 class="font-bold text-lg">{{ $master }}</h1>
                <p class="text-slate-500 text-sm">List data {{ $master }} yang berhasil terinput dalam Database
                </p>
            </div>
            <div>
                <x-button class="" color="info" size="sm" @click="addModal = !addModal; editModal = false">
                    Tambah {{ $master }}
                </x-button>
            </div>
        </div>
        {{-- add modal --}}
        <div x-show="addModal" style="display: none" x-on:keydown.escape.window="addModal = false"
            class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-full max-h-full bg-black/20">
            <div class="relative p-4 w-full max-w-2xl max-h-full" @click.outside="addModal = false">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow ">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                        <h3 class="text-lg font-bold text-gray-900 ">
                            Tambah Data {{ $master }}
                        </h3>
                        <button type="button" @click="addModal = false"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center "
                            data-modal-hide="default-modal">
                            <span>
                                <i class="fas fa-times"></i>
                            </span>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        <form wire:submit.prevent="submit" class="grid grid-cols-12 p-2">
                            <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                <label for="name" class="text-sm">Nama RTM:</label>
                                <input type="text" id="name" name="name" wire:model="rtm.name"
                                    placeholder="Masukkan Nama RTM"
                                    class="p-4 text-sm rounded-md bg-neutral-100 text-slate-600 focus:outline-none focus:ring-color-info-500 border border-neutral-200">
                                @error('rtm.name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                <label for="tahun" class="text-sm">Tahun:</label>
                                <input type="number" id="tahun" name="tahun" wire:model="rtm.tahun"
                                    placeholder="Masukkan Tahun"
                                    class="p-4 text-sm rounded-md bg-neutral-100 text-slate-600 focus:outline-none focus:ring-color-info-500 border border-neutral-200">
                                @error('rtm.tahun')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- AMI Anchor -->
                            <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                <label for="ami_anchor" class="text-sm">AMI:</label>
                                <div wire:ignore>
                                    <select multiple id="ami_anchor"
                                        class="multi-select p-4 text-sm rounded-md bg-neutral-100 text-slate-600 border border-neutral-200">
                                        @foreach ($anchor_ami as $anchor)
                                            <option value="{{ $anchor['id'] }}">{{ $anchor['periode_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('rtm.ami_anchor')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                <label for="survei_anchor" class="text-sm">Survei:</label>
                                <div wire:ignore>
                                    <select multiple id="survei_anchor"
                                        class="multi-select p-4 text-sm rounded-md bg-neutral-100 text-slate-600 border border-neutral-200">
                                        @foreach ($anchor_survei as $anchor)
                                            <option value="{{ $anchor['id'] }}">{{ $anchor['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('rtm.survei_anchor')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (Auth::user()->role->name == 'Universitas')
                                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                    <label class="text-sm">Jenis Laporan:</label>
                                    <div class="inline-flex items-center gap-x-4">
                                        <label class="inline-flex items-center gap-x-2">
                                            <input type="radio" name="is_temuan" value="0" wire:model="rtm.is_temuan">
                                            <span>Normal</span>
                                        </label>
                                        <label class="inline-flex items-center gap-x-2">
                                            <input type="radio" name="is_temuan" value="1" wire:model="rtm.is_temuan">
                                            <span>Temuan</span>
                                        </label>
                                    </div>
                                    @error('rtm.is_temuan')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <x-button class="inline-flex items-center w-fit gap-x-2 col-span-12" color="info"
                                type="submit" id="submitBtn">
                                <span wire:loading.remove><i class="fas fa-plus"></i></span>
                                <span wire:loading class="animate-spin"><i class="fas fa-circle-notch"></i></span>
                                Tambah RTM
                            </x-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- edit modal --}}
        <div x-show="editModal" style="display: none" x-on:keydown.escape.window="editModal = false"
            class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-full max-h-full bg-black/20">
            <div class="relative p-4 w-full max-w-2xl max-h-full" @click.outside="editModal = false">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow ">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                        <h3 class="text-lg font-bold text-gray-900 ">
                            Edit Data {{ $master }}
                        </h3>
                        <button type="button" @click="editModal = false"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center "
                            data-modal-hide="default-modal">
                            <span>
                                <i class="fas fa-times"></i>
                            </span>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        <form wire:submit.prevent="submit" class="grid grid-cols-12 p-2">
                            <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                <label for="edit_name" class="text-sm">Nama RTM:</label>
                                <input type="text" id="edit_name" name="edit_name" wire:model="rtm.name"
                                    placeholder="Masukkan Nama RTM"
                                    class="p-4 text-sm rounded-md bg-neutral-100 text-slate-600 focus:outline-none focus:ring-color-info-500 border border-neutral-200">
                                @error('rtm.name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                <label for="edit_tahun" class="text-sm">Tahun:</label>
                                <input type="number" id="edit_tahun" name="edit_tahun" wire:model="rtm.tahun"
                                    placeholder="Masukkan Tahun"
                                    class="p-4 text-sm rounded-md bg-neutral-100 text-slate-600 focus:outline-none focus:ring-color-info-500 border border-neutral-200">
                                @error('rtm.tahun')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- AMI Anchor -->
                            <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                <label for="edit_ami_anchor" class="text-sm">AMI:</label>
                                <div wire:ignore>
                                    <select multiple id="edit_ami_anchor"
                                        class="multi-select p-4 text-sm rounded-md bg-neutral-100 text-slate-600 border border-neutral-200">
                                        @foreach ($anchor_ami as $anchor)
                                            <option value="{{ $anchor['id'] }}">{{ $anchor['periode_name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('rtm.ami_anchor')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                <label for="edit_survei_anchor" class="text-sm">Survei:</label>
                                <div wire:ignore>
                                    <select multiple id="edit_survei_anchor"
                                        class="multi-select p-4 text-sm rounded-md bg-neutral-100 text-slate-600 border border-neutral-200">
                                        @foreach ($anchor_survei as $anchor)
                                            <option value="{{ $anchor['id'] }}">{{ $anchor['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('rtm.survei_anchor')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (Auth::user()->role->name == 'Universitas')
                                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                                    <label class="text-sm">Jenis Laporan:</label>
                                    <div class="inline-flex items-center gap-x-4">
                                        <label class="inline-flex items-center gap-x-2">
                                            <input type="radio" name="edit_is_temuan" value="0" wire:model="rtm.is_temuan">
                                            <span>Normal</span>
                                        </label>
                                        <label class="inline-flex items-center gap-x-2">
                                            <input type="radio" name="edit_is_temuan" value="1" wire:model="rtm.is_temuan">
                                            <span>Temuan</span>
                                        </label>
                                    </div>
                                    @error('rtm.is_temuan')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <div class="flex gap-x-2 col-span-12">
                                <x-button class="inline-flex items-center w-fit gap-x-2" color="secondary"
                                    type="button" wire:click="cancelEdit" @click="editModal = false">
                                    <i class="fas fa-times"></i>
                                    Batal
                                </x-button>
                                <x-button class="inline-flex items-center w-fit gap-x-2" color="info"
                                    type="submit" id="editBtn">
                                    <span wire:loading.remove><i class="fas fa-save"></i></span>
                                    <span wire:loading class="animate-spin"><i class="fas fa-circle-notch"></i></span>
                                    Simpan Perubahan
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="max-w-screen-xl w-full mx-auto px-4 mt-4 pb-12">
        <div class="p-4 bg-white rounded-lg border-slate-100 shadow-sm ">
            <div class="p-4 overflow-x-auto text-sm">
                <table id="myTable" class="cell-border stripe w-full">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama RTM</th>
                            <th>Tahun</th>
                            <th>AMI</th>
                            <th>Survei</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $rtm)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rtm['name'] }}</td>
                                <td>{{ $rtm['tahun'] }}</td>
                                <td>
                                    @if (!empty($rtm['ami_anchor']))
                                        <ul class="list-disc pl-4">
                                            @foreach ($rtm['ami_anchor'] as $anchor)
                                                @php
                                                    $matchedAnchor = collect($anchor_ami)->firstWhere('id', $anchor);
                                                @endphp
                                                <li>{{ $matchedAnchor ? $matchedAnchor['periode_name'] : '-' }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif

                                </td>
                                <td>
                                    @if (!empty($rtm['survei_anchor']))
                                        <ul class="list-disc pl-4">
                                            @foreach ($rtm['survei_anchor'] as $anchor)
                                                @php
                                                    $matchedAnchor = collect($anchor_survei)->firstWhere(
                                                        'id',
                                                        (int) $anchor,
                                                    );
                                                @endphp
                                                <li>{{ $matchedAnchor['name'] ?? '-' }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>

                                    <div class="inline-flex gap-x-2">

                                        @if (Auth::user()->role->name == 'Universitas')
                                            <x-button color="info" size="sm"
                                                onclick="window.location.href='{{ route('dashboard.master.rtm.edit', $rtm['id']) }}'">
                                                Edit

                                                <x-button color="danger" size="sm"
                                                    onclick="confirmDelete({{ $rtm['id'] }})">
                                                    Hapus
                                                </x-button>
                                            </x-button>
                                        @endif


                                        <x-button color="success" size="sm"
                                            onclick="window.location.href='{{ route('dashboard.master.rtm.detail', $rtm['id']) }}'">
                                            Detail
                                        </x-button>


                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </section>
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Inisialisasi DataTables
                var table = $('#myTable').DataTable();
            });
        </script>
        <script>
            function confirmDelete(id) {
                if (confirm(`Hapus fakultas? ${id}`)) {
                    @this.call('deleteFakultas', id);
                }
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                // Initialize Choices.js for Add modal
                const amiChoices = new Choices('#ami_anchor', {
                    removeItemButton: true,
                    allowHTML: true
                });

                const surveiChoices = new Choices('#survei_anchor', {
                    removeItemButton: true,
                    allowHTML: true
                });

                // Initialize Choices.js for Edit modal
                const editAmiChoices = new Choices('#edit_ami_anchor', {
                    removeItemButton: true,
                    allowHTML: true
                });

                const editSurveiChoices = new Choices('#edit_survei_anchor', {
                    removeItemButton: true,
                    allowHTML: true
                });

                // Set Livewire data on Add form submission
                document.getElementById('submitBtn').addEventListener('click', function() {
                    const amiValues = amiChoices.getValue().map(item => item.value);
                    const surveiValues = surveiChoices.getValue().map(item => item.value);

                    @this.set('rtm.ami_anchor', amiValues);
                    @this.set('rtm.survei_anchor', surveiValues);
                });

                // Set Livewire data on Edit form submission
                document.getElementById('editBtn').addEventListener('click', function() {
                    const amiValues = editAmiChoices.getValue().map(item => item.value);
                    const surveiValues = editSurveiChoices.getValue().map(item => item.value);

                    @this.set('rtm.ami_anchor', amiValues);
                    @this.set('rtm.survei_anchor', surveiValues);
                });

                // Listen for the rtm-edit event from Livewire to update select fields
                window.addEventListener('rtm-edit', event => {
                    const {
                        ami_anchor,
                        survei_anchor
                    } = event.detail;

                    // First clear any existing selections
                    editAmiChoices.clearStore();
                    editSurveiChoices.clearStore();

                    // Then set the new selections
                    if (ami_anchor && ami_anchor.length > 0) {
                        editAmiChoices.setChoiceByValue(ami_anchor.map(String));
                    }

                    if (survei_anchor && survei_anchor.length > 0) {
                        editSurveiChoices.setChoiceByValue(survei_anchor.map(String));
                    }
                });
            });
        </script>
    @endpush
</main>
