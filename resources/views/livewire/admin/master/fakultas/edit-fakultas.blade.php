<!-- resources/views/livewire/admin/master/fakultas/edit-fakultas.blade.php -->
<main class="bg-[#f9fafc] min-h-screen">
    <section class="max-w-screen-xl w-full mx-auto px-4 pt-24">
        <div
            class="p-6 bg-white flex flex-col lg:flex-row lg:items-center gap-y-2 justify-between rounded-lg border border-slate-100 shadow-sm">
            <div>
                <h1 class="font-bold text-lg">Edit {{ $master }}</h1>
                <p class="text-slate-500 text-sm">Edit data {{ $master }} yang berhasil terinput dalam Database</p>
            </div>
            <div>
                <x-button class="" color="danger" size="sm" wire:click="redirectToAdd">
                    Kembali
                </x-button>
            </div>
        </div>
    </section>
    <section class="max-w-screen-xl w-full mx-auto px-4 mt-4 pb-12">
        <div class="p-6 bg-white rounded-lg border-slate-100 shadow-sm">
            <form wire:submit.prevent="updateFakultas" class="grid grid-cols-12">
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="fakultas" class="text-sm ">Nama {{ $master }} :</label>
                    <input type="text" id="fakultas" name="fakultas" wire:model="fakultas.name"
                        placeholder="Masukan Nama {{ $master }}"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                    @error('fakultas.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="kode" class="text-sm ">Kode {{ $master }} :</label>
                    <input type="text" id="kode" name="Kode" wire:model="fakultas.code" placeholder="Masukan Kode {{ $master }}"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                        @error('fakultas.code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <!-- AMI dropdown -->
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="ami" class="text-sm">ID AMI:</label>
                    <select id="ami" name="ami" wire:model="fakultas.ami"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                        <option value="">Pilih ID AMI</option>
                        @foreach ($ami_faculties as $faculty)
                            <option value="{{ $faculty['id'] }}">{{ $faculty['name'] ?? $faculty['department_name'] ?? $faculty['nama'] ?? $faculty['faculty_name'] ?? 'Fakultas ID: '.$faculty['id'] }}</option>
                        @endforeach
                    </select>
                    @error('fakultas.ami') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Survei dropdown -->
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="survei" class="text-sm">ID Survei:</label>
                    <select id="survei" name="survei" wire:model="fakultas.survei"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                        <option value="">Pilih ID Survei</option>
                        @foreach ($survei_faculties as $faculty)
                            <option value="{{ $faculty['id'] }}">{{ $faculty['name'] ?? $faculty['nama'] ?? $faculty['faculty_name'] ?? 'Fakultas ID: '.$faculty['id'] }}</option>
                        @endforeach
                    </select>
                    @error('fakultas.survei') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Akreditasi dropdown -->
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="akreditasi" class="text-sm">Akreditasi:</label>
                    <select id="akreditasi" name="akreditasi" wire:model="fakultas.akreditasi"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                        <option value="">Pilih Akreditasi</option>
                        @foreach ($akreditasi_faculties as $faculty)
                            <option value="{{ $faculty['fakultas_id'] }}">{{ $faculty['fakultas_nama'] ?? $faculty['nama'] ?? $faculty['faculty_name'] ?? 'Fakultas ID: '.$faculty['fakultas_id'] }}</option>
                        @endforeach
                    </select>
                    @error('fakultas.akreditasi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <x-button class="inline-flex items-center w-fit gap-x-2 col-span-12" color="info" type="submit">
                    <span wire:loading.remove>
                        <i class="fas fa-edit"></i>
                    </span>
                    <span wire:loading class="animate-spin">
                        <i class="fas fa-circle-notch"></i>
                    </span>
                    Edit {{ $master }}
                </x-button>
            </form>
        </div>
    </section>
</main>
