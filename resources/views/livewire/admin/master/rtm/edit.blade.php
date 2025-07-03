<main class="bg-[#f9fafc] min-h-screen" x-data="{
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

    <section class="max-w-screen-xl w-full mx-auto px-4 py-24">
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Edit {{ $master }}</h1>
                    <p class="text-slate-500">Edit informasi {{ $master }} di bawah ini</p>
                </div>
                <x-button color="secondary" size="sm"
                    onclick="window.location.href='{{ route('dashboard.master.rtm.index') }}'">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </x-button>
            </div>

            <form wire:submit.prevent="submit" class="grid grid-cols-12 gap-6 p-4">
                <div class="flex flex-col gap-y-2 col-span-12 md:col-span-6">
                    <label for="name" class="text-sm font-medium">Nama RTM:</label>
                    <input type="text" id="name" name="name" wire:model="rtm.name"
                        placeholder="Masukkan Nama RTM"
                        class="p-4 text-sm rounded-md bg-neutral-100 text-slate-600 focus:outline-none focus:ring-color-info-500 border border-neutral-200">
                    @error('rtm.name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-y-2 col-span-12 md:col-span-6">
                    <label for="tahun" class="text-sm font-medium">Tahun:</label>
                    <input type="number" id="tahun" name="tahun" wire:model="rtm.tahun"
                        placeholder="Masukkan Tahun"
                        class="p-4 text-sm rounded-md bg-neutral-100 text-slate-600 focus:outline-none focus:ring-color-info-500 border border-neutral-200">
                    @error('rtm.tahun')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- AMI Anchor -->
                <div class="flex flex-col gap-y-2 col-span-12">
                    <label for="ami_anchor" class="text-sm font-medium">AMI:</label>
                    <div wire:ignore>
                        <select multiple id="ami_anchor"
                            class="multi-select p-4 text-sm rounded-md bg-neutral-100 text-slate-600 border border-neutral-200">
                            @foreach ($anchor_ami as $anchor)
                                <option @if (in_array($anchor['id'], $rtm['ami_anchor'])) selected @endif value="{{ $anchor['id'] }}">
                                    {{ $anchor['periode_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('rtm.ami_anchor')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Survei Anchor -->
                <div class="flex flex-col gap-y-2 col-span-12">
                    <label for="survei_anchor" class="text-sm font-medium">Survei:</label>
                    <div wire:ignore>
                        <select multiple id="survei_anchor"
                            class="multi-select p-4 text-sm rounded-md bg-neutral-100 text-slate-600 border border-neutral-200">
                            @foreach ($anchor_survei as $anchor)
                                <option @if (in_array($anchor['id'], $rtm['survei_anchor'])) selected @endif value="{{ $anchor['id'] }}">
                                    {{ $anchor['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('rtm.survei_anchor')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex gap-x-3 col-span-12 mt-4">
                    <x-button class="inline-flex items-center gap-x-2 text-black" color="secondary" type="button"
                        wire:click="cancel">
                        <i class="fas fa-times"></i>
                        Batal
                    </x-button>
                    <x-button class="inline-flex items-center gap-x-2" color="info" type="submit" id="editBtn">
                        <span wire:loading.remove><i class="fas fa-save"></i></span>
                        <span wire:loading class="animate-spin"><i class="fas fa-circle-notch"></i></span>
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                // Initialize Choices.js for multi-select dropdowns
                const amiChoices = new Choices('#ami_anchor', {
                    removeItemButton: true,
                    allowHTML: true
                });

                const surveiChoices = new Choices('#survei_anchor', {
                    removeItemButton: true,
                    allowHTML: true
                });

                // Set Livewire data on form submission
                document.getElementById('editBtn').addEventListener('click', function() {
                    const amiValues = amiChoices.getValue().map(item => item.value);
                    const surveiValues = surveiChoices.getValue().map(item => item.value);

                    @this.set('rtm.ami_anchor', amiValues);
                    @this.set('rtm.survei_anchor', surveiValues);
                });

                // Listen for the rtm-edit-init event from Livewire to set initial values
                // For Livewire 3, use Livewire.on instead of window.addEventListener
                Livewire.on('rtm-edit-init', data => {
                    const {
                        ami_anchor,
                        survei_anchor
                    } = data;

                    // First clear any existing selections
                    amiChoices.clearStore();
                    surveiChoices.clearStore();

                    // Then set the new selections
                    if (ami_anchor && ami_anchor.length > 0) {
                        amiChoices.setChoiceByValue(ami_anchor.map(String));
                    }

                    if (survei_anchor && survei_anchor.length > 0) {
                        surveiChoices.setChoiceByValue(survei_anchor.map(String));
                    }
                });

                // Also trigger immediately if we have initial data
                if (@this.rtm.ami_anchor.length > 0 || @this.rtm.survei_anchor.length > 0) {
                    setTimeout(() => {
                        amiChoices.setChoiceByValue(@this.rtm.ami_anchor.map(String));
                        surveiChoices.setChoiceByValue(@this.rtm.survei_anchor.map(String));
                    }, 300);
                }
            });
        </script>
    @endpush
</main>
