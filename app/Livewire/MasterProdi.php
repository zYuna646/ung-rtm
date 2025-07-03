<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Prodi;
use App\Models\Jurusan;
use App\Models\Fakultas;
use App\Services\AmiService;
use App\Services\SurveiService;
use App\Services\AkreditasiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MasterProdi extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $master = 'Prodi';

    public $ami_prodis = [];
    public $survei_prodis = [];
    public $akreditasi_prodis = [];

    public $prodi = [
        'nama' => '',
        'kode' => '',
        'fakultas_id' => '',
        'ami' => '',
        'survei' => '',
        'akreditasi' => '',
    ];

    public $dataProdi;
    public $dataFakultas;

    public function mount(AmiService $amiService, SurveiService $surveiService, AkreditasiService $akreditasiService)
    {
        $this->dataProdi = Prodi::all();
        $this->dataFakultas = Fakultas::all();
        
        // Get prodi data from AMI service
        $amiProdiData = $amiService->getAllProdi();
        if ($amiProdiData && isset($amiProdiData['data'])) {
            $this->ami_prodis = $amiProdiData['data'];
            
            // Log the first prodi structure to debug
            if (!empty($this->ami_prodis) && isset($this->ami_prodis[0])) {
                Log::info('AMI Prodi Structure:', $this->ami_prodis[0]);
            }
        }
        
        // Get prodi data from Survei service
        $surveiProdiData = $surveiService->getAllProdi();
        if ($surveiProdiData && isset($surveiProdiData['data'])) {
            $this->survei_prodis = $surveiProdiData['data'];
            
            // Log the first prodi structure to debug
            if (!empty($this->survei_prodis) && isset($this->survei_prodis[0])) {
                Log::info('Survei Prodi Structure:', $this->survei_prodis[0]);
            }
        }
        
        // Get prodi data from Akreditasi service
        $akreditasiProdiData = $akreditasiService->getAllProdi();
        if ($akreditasiProdiData && isset($akreditasiProdiData['data'])) {
            $this->akreditasi_prodis = $akreditasiProdiData['data'];
            
            // Log the first prodi structure to debug
            if (!empty($this->akreditasi_prodis) && isset($this->akreditasi_prodis[0])) {
                Log::info('Akreditasi Prodi Structure:', $this->akreditasi_prodis[0]);
            }
        }
    }


    public function render()
    {
        return view('livewire.admin.master.prodi.master-prodi')
        ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
        ->title('UNG RTM - Master Prodi');
    }

    public function addProdi()
    {
        $this->validate([
            'prodi.nama' => 'required|string|max:255',
            'prodi.kode' => 'required|string|max:10|unique:prodis,code',
            'prodi.fakultas_id' => 'required|exists:fakultas,id',
            'prodi.ami' => 'nullable|string',
            'prodi.survei' => 'nullable|string',
            'prodi.akreditasi' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            Prodi::create([
                'name' => $this->prodi['nama'],
                'code' => $this->prodi['kode'],
                'fakultas_id' => $this->prodi['fakultas_id'],
                'ami' => $this->prodi['ami'],
                'survei' => $this->prodi['survei'],
                'akreditasi' => $this->prodi['akreditasi'],
            ]);

            DB::commit();

            session()->flash('toastMessage', 'Data berhasil ditambahkan');
            session()->flash('toastType', 'success');

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('toastMessage', 'Terjadi kesalahan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
       

        return redirect()->to('master_prodi');
    }
    
    public function deleteProdi($id)    
    {
        try {
            DB::beginTransaction();

            Prodi::findOrFail($id)->delete();

            DB::commit();

            session()->flash('toastMessage', 'Data berhasil dihapus');
            session()->flash('toastType', 'success');
            
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('toastMessage', 'Terjadi kesalahan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
        return redirect()->to('master_prodi');
    }
}
