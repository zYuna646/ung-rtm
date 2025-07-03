<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Fakultas;
use App\Services\AmiService;
use App\Services\SurveiService;
use App\Services\AkreditasiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EditFakultas extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $master = 'Fakultas';

    public $fakultas = [];
    public $ami_faculties = [];
    public $survei_faculties = [];
    public $akreditasi_faculties = [];

    public function mount($id, AmiService $amiService, SurveiService $surveiService, AkreditasiService $akreditasiService)
    {
        // Fetch fakultas data by ID from the model
        $fakultas = Fakultas::findOrFail($id);
        // Assign all data to the fakultas property
        $this->fakultas = $fakultas->toArray();

        // Get faculty data from AMI service
        $amiFacultyData = $amiService->getAllFaculty();
        if ($amiFacultyData && isset($amiFacultyData['data'])) {
            $this->ami_faculties = $amiFacultyData['data'];
            
            // Log the first faculty structure to debug
            if (!empty($this->ami_faculties) && isset($this->ami_faculties[0])) {
                Log::info('AMI Faculty Structure (Edit):', $this->ami_faculties[0]);
            }
        }
        
        // Get faculty data from Survei service
        $surveiFacultyData = $surveiService->getAllFaculty();
        if ($surveiFacultyData && isset($surveiFacultyData['data'])) {
            $this->survei_faculties = $surveiFacultyData['data'];
            
            // Log the first faculty structure to debug
            if (!empty($this->survei_faculties) && isset($this->survei_faculties[0])) {
                Log::info('Survei Faculty Structure (Edit):', $this->survei_faculties[0]);
            }
        }
        
        // Get faculty data from Akreditasi service
        $akreditasiFacultyData = $akreditasiService->getAllFaculty();
        if ($akreditasiFacultyData && isset($akreditasiFacultyData['data'])) {
            $this->akreditasi_faculties = $akreditasiFacultyData['data'];
            
            // Log the first faculty structure to debug
            if (!empty($this->akreditasi_faculties) && isset($this->akreditasi_faculties[0])) {
                Log::info('Akreditasi Faculty Structure (Edit):', $this->akreditasi_faculties[0]);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.master.fakultas.edit-fakultas')
        ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
        ->title('UNG RTM - Master Fakultas');
    }

    public function updateFakultas(){
        
        $fakultas = Fakultas::findOrFail($this->fakultas['id']);

        $this->validate([
            'fakultas.name' => 'required|string|max:255',
            'fakultas.code' => 'required|string|max:10|unique:fakultas,code,' . $this->fakultas['id'],
            'fakultas.ami' => 'nullable|string',
            'fakultas.survei' => 'nullable|string',
            'fakultas.akreditasi' => 'nullable|string',
        ]);

        try
        {
            DB::beginTransaction();

            $fakultas->update($this->fakultas);

            DB::commit();

            session()->flash('toastMessage', 'Data berhasil diedit');
            session()->flash('toastType', 'success');
            
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('toastMessage', 'Terjadi kesalahan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
       
        return redirect()->route('dashboard.master.fakultas.index');
    }

    public function redirectToAdd()
    {
        return redirect()->route('dashboard.master.fakultas.index');
    }

}
