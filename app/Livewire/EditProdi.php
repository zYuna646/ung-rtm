<?php

namespace App\Livewire;

use App\Models\Fakultas;
use Livewire\Component;
use App\Models\Prodi;
use App\Models\Jurusan;
use App\Services\AmiService;
use App\Services\SurveiService;
use App\Services\AkreditasiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditProdi extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $master = 'Prodi';

    public $prodi = [];
    public $dataFakultas;
    public $ami_prodis = [];
    public $survei_prodis = [];
    public $akreditasi_prodis = [];

    public function mount($id, AmiService $amiService, SurveiService $surveiService, AkreditasiService $akreditasiService)
    {
        $this->dataFakultas = Fakultas::all();
        $prodi = Prodi::findOrFail($id);

        // Assign all data to the prodi property
        $this->prodi = $prodi->toArray();
        
        // Get prodi data from AMI service
        $amiProdiData = $amiService->getAllProdi();
        if ($amiProdiData && isset($amiProdiData['data'])) {
            $this->ami_prodis = $amiProdiData['data'];
            
            // Log the first prodi structure to debug
            if (!empty($this->ami_prodis) && isset($this->ami_prodis[0])) {
                Log::info('AMI Prodi Structure (Edit):', $this->ami_prodis[0]);
            }
        }
        
        // Get prodi data from Survei service
        $surveiProdiData = $surveiService->getAllProdi();
        if ($surveiProdiData && isset($surveiProdiData['data'])) {
            $this->survei_prodis = $surveiProdiData['data'];
            
            // Log the first prodi structure to debug
            if (!empty($this->survei_prodis) && isset($this->survei_prodis[0])) {
                Log::info('Survei Prodi Structure (Edit):', $this->survei_prodis[0]);
            }
        }
        
        // Get prodi data from Akreditasi service
        $akreditasiProdiData = $akreditasiService->getAllProdi();
        if ($akreditasiProdiData && isset($akreditasiProdiData['data'])) {
            $this->akreditasi_prodis = $akreditasiProdiData['data'];
            
            // Log the first prodi structure to debug
            if (!empty($this->akreditasi_prodis) && isset($this->akreditasi_prodis[0])) {
                Log::info('Akreditasi Prodi Structure (Edit):', $this->akreditasi_prodis[0]);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.master.prodi.edit-prodi')
        ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
        ->title('UNG RTM - Master Prodi');
    }

    public function updateProdi()
    {
        $prodi = Prodi::findOrFail($this->prodi['id']);

        $this->validate([
            'prodi.name' => 'required|string|max:255',
            'prodi.code' => 'required|string|max:10|unique:prodis,code,' . $this->prodi['id'],
            'prodi.fakultas_id' => 'required|exists:fakultas,id',
            'prodi.ami' => 'nullable|string',
            'prodi.survei' => 'nullable|string',
            'prodi.akreditasi' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
    
            $prodi->update($this->prodi);

            DB::commit();

            session()->flash('toastMessage', 'Data berhasil diedit');
            session()->flash('toastType', 'success');

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('toastMessage', 'Terjadi kesalahan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }

        return redirect()->to('master_prodi');
    }

    public function redirectToAdd()
    {
        return redirect()->to('master_prodi');
    }
}
