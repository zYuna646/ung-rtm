<?php

namespace App\Livewire\Admin\Master\Fakultas;

use Livewire\Component;
use App\Models\Fakultas;
use App\Services\AmiService;
use App\Services\SurveiService;
use App\Services\AkreditasiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $master = 'Fakultas';

    public $ami_faculties = [];
    public $survei_faculties = [];
    public $akreditasi_faculties = [];
    public $anchor_akreditas = [];

    public $fakultas = [
        'nama' => '',
        'kode' => '',
        'ami' => '',
        'survei' => '',
        'akreditasi' => '',
    ];

    public $dataFakultas;
    public $toastMessage = '';
    public $toastType = '';

    public function mount(AmiService $amiService, SurveiService $surveiService, AkreditasiService $akreditasiService)
    {
        // Get faculty data from AMI and Survei services
        $amiFacultyData = $amiService->getAllFaculty();
        if ($amiFacultyData && isset($amiFacultyData['data'])) {
            $this->ami_faculties = $amiFacultyData['data'];
            
            // Log the first faculty structure to debug
            if (!empty($this->ami_faculties) && isset($this->ami_faculties[0])) {
                Log::info('AMI Faculty Structure:', $this->ami_faculties[0]);
            }
        }
        
        $surveiFacultyData = $surveiService->getAllFaculty();
        if ($surveiFacultyData && isset($surveiFacultyData['data'])) {
            $this->survei_faculties = $surveiFacultyData['data'];
            
            // Log the first faculty structure to debug
            if (!empty($this->survei_faculties) && isset($this->survei_faculties[0])) {
                Log::info('Survei Faculty Structure:', $this->survei_faculties[0]);
            }
        }

        // Get Akreditasi faculty data
        $akreditasiFacultyData = $akreditasiService->getAllFaculty();
        if ($akreditasiFacultyData && isset($akreditasiFacultyData['data'])) {
            $this->akreditasi_faculties = $akreditasiFacultyData['data'];
            
            // Log the first faculty structure to debug
            if (!empty($this->akreditasi_faculties) && isset($this->akreditasi_faculties[0])) {
                Log::info('Akreditasi Faculty Structure:', $this->akreditasi_faculties[0]);
            }
        }

        // Get local faculty data
        $this->dataFakultas = Fakultas::all();
    }

    public function render()
    {
        return view('livewire.admin.master.fakultas.index')
            ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
            ->title('UNG RTM - Master Fakultas');
    }

    public function addFakultas()
    {
        $this->validate([
            'fakultas.nama' => 'required|string|max:255',
            'fakultas.kode' => 'required|string|max:10|unique:fakultas,code',
            'fakultas.ami' => 'nullable|string',
            'fakultas.survei' => 'nullable|string',
            'fakultas.akreditasi' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            Fakultas::create([
                'name' => $this->fakultas['nama'],
                'code' => $this->fakultas['kode'],
                'ami' => $this->fakultas['ami'],
                'survei' => $this->fakultas['survei'],
                'akreditasi' => $this->fakultas['akreditasi'],
            ]);

            DB::commit();

            session()->flash('toastMessage', 'Data berhasil ditambahkan');
            session()->flash('toastType', 'success');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('toastMessage', 'Terjadi kesalahan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }

        return redirect()->route('dashboard.master.fakultas.index');
    }

    public function deleteFakultas($id)
    {
        try {
            DB::beginTransaction();

            Fakultas::findOrFail($id)->delete();

            DB::commit();

            session()->flash('toastMessage', 'Data berhasil dihapus');
            session()->flash('toastType', 'success');
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('toastMessage', 'Terjadi kesalahan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }

        return redirect()->route('dashboard.master.fakultas.index');
    }
}
