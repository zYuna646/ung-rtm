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
            <form wire:submit.prevent="updateProdi" class="grid grid-cols-12">
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="nama" class="text-sm">Nama {{ $master }} :</label>
                    <input type="text" id="nama" name="nama" wire:model="prodi.name"
                        placeholder="Masukan Nama {{ $master }}"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                    @error('prodi.name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="kode" class="text-sm">Kode {{ $master }} :</label>
                    <input type="text" id="kode" name="kode" wire:model="prodi.code"
                        placeholder="Masukan Kode {{ $master }}"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                    @error('prodi.code')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="fakultas_id" class="text-sm">Fakultas :</label>
                    <select name="fakultas_id" id="fakultas_id" wire:model="prodi.fakultas_id"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                        <option value="">Pilih Fakultas</option>
                        @foreach ($dataFakultas as $fakultas)
                            <option value="{{ $fakultas->id }}">{{ $fakultas->name }}</option>
                        @endforeach
                    </select>
                    @error('prodi.fakultas_id')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- AMI dropdown -->
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="ami" class="text-sm">ID AMI:</label>
                    <select id="ami" name="ami" wire:model="prodi.ami"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                        <option value="">Pilih ID AMI</option>
                        @foreach ($ami_prodis as $ami_prodi)
                            <option value="{{ $ami_prodi['id'] }}">{{ $ami_prodi['program_name'] ?? 'Prodi ID: '.$ami_prodi['id'] }}</option>
                        @endforeach
                    </select>
                    @error('prodi.ami') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <!-- Survei dropdown -->
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="survei" class="text-sm">ID Survei:</label>
                    <select id="survei" name="survei" wire:model="prodi.survei"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                        <option value="">Pilih ID Survei</option>
                        @foreach ($survei_prodis as $survei_prodi)
                            <option value="{{ $survei_prodi['id'] }}">{{ $survei_prodi['name'] ?? $survei_prodi['nama'] ?? $survei_prodi['prodi_name'] ?? 'Prodi ID: '.$survei_prodi['id'] }}</option>
                        @endforeach
                    </select>
                    @error('prodi.survei') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <!-- Akreditasi dropdown -->
                <div class="flex flex-col gap-y-2 col-span-12 mb-4">
                    <label for="akreditasi" class="text-sm">Akreditasi:</label>
                    <select id="akreditasi" name="akreditasi" wire:model="prodi.akreditasi"
                        class="p-4 text-sm rounded-md bg-neutral-50 text-slate-800 focus:outline-none focus:outline-color-info-500 border border-neutral-200">
                        <option value="">Pilih Akreditasi</option>
                        @foreach ($akreditasi_prodis as $akreditasi_prodi)
                            <option value="{{ $akreditasi_prodi['prodi_id'] }}">{{ $akreditasi_prodi['prodi_nama'] ?? 'Prodi ID: '.$akreditasi_prodi['prodi_id'] }}</option>
                        @endforeach
                    </select>
                    @error('prodi.akreditasi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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
