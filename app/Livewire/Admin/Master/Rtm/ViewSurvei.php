<?php

namespace App\Livewire\Admin\Master\Rtm;

use App\Models\Fakultas;
use App\Models\RTM;
use App\Models\RtmRencanaTindakLanjut;
use App\Services\SurveiService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ViewSurvei extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $rtm = null;
    public $surveiId = null;
    public $selectedFakultas = null;
    public $fakultas = [];
    public $surveiData = [];
    public $anchorName = '';

    // New properties for rencana tindak lanjut form
    public $rencanaForms = [];
    public $formIsOpen = false;
    public $currentIndicatorId = null;
    public $currentIndicatorDesc = null;
    public $user = null;

    public function mount($rtm_id, $survei_id)
    {
        $this->rtm = RTM::findOrFail($rtm_id);
        $this->surveiId = $survei_id;
        $this->fakultas = Fakultas::all();
        $this->user = Auth::user();
        if ($this->user->role->name == 'Fakultas') {
            $this->selectedFakultas = $this->user->fakultas_id;
        }

        // Get Survei data
        $this->loadSurveiData();

        // Get anchor name from SurveiService
        $surveiService = app(SurveiService::class);
        $anchors = $surveiService->getAnchor()['data'];
        $matchedAnchor = collect($anchors)->firstWhere('id', (int) $this->surveiId);
        $this->anchorName = $matchedAnchor ? $matchedAnchor['name'] : "Survei #$this->surveiId";

        // Initialize the rencana forms
        $this->initializeRencanaForms();
    }

    public function loadSurveiData()
    {
        try {
            $surveiService = app(SurveiService::class);
            
            // Get the Survei ID from the fakultas, if one is selected
            $fakultasSurveiId = 'null';
            if ($this->selectedFakultas) {
                $fakultas = Fakultas::find($this->selectedFakultas);
                if ($fakultas) {
                    $fakultasSurveiId = $fakultas->survei;
                }
            }
            
            $result = $surveiService->getSurvei($this->surveiId, $fakultasSurveiId);

            // Initialize with empty structure to avoid foreach errors
            $this->surveiData = ['data' => ['tabel' => []]];

            if (isset($result['data']) && !empty($result['data'])) {
                $this->surveiData = $result;
            }
        } catch (\Exception $e) {
            // Handle error - keep the empty structure
            $this->surveiData = ['data' => ['tabel' => []]];
            session()->flash('toastMessage', 'Gagal memuat data: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
    }

    protected function initializeRencanaForms()
    {
        $this->rencanaForms = [];

        if (isset($this->surveiData['data']['tabel']) && is_array($this->surveiData['data']['tabel'])) {
            foreach ($this->surveiData['data']['tabel'] as $item) {
                $this->loadRencanaTindakLanjut($item['id']);
            }
        }
    }

    protected function loadRencanaTindakLanjut($indicatorId)
    {
        // Find existing rencana tindak lanjut for this indicator
        $rencana = RtmRencanaTindakLanjut::where('survei_id', $indicatorId)
            ->where('rtm_id', $this->rtm->id)
            ->where(function ($query) {
                if ($this->selectedFakultas) {
                    $query->where('fakultas_id', $this->selectedFakultas);
                } else {
                    $query->whereNull('fakultas_id');
                }
            })
            ->first();
        
        // Initialize the form data
        $this->rencanaForms[$indicatorId] = [
            'rencana_tindak_lanjut' => $rencana ? $rencana->rencana_tindak_lanjut : '',
            'target_penyelesaian' => $rencana ? $rencana->target_penyelesaian : '',
        ];
    }

    public function openRencanaForm($indicatorId, $indicatorDesc)
    {
        $this->currentIndicatorId = $indicatorId;
        $this->currentIndicatorDesc = $indicatorDesc;
        $this->formIsOpen = true;
    }

    public function closeRencanaForm()
    {
        $this->formIsOpen = false;
        $this->currentIndicatorId = null;
        $this->currentIndicatorDesc = null;
    }

    public function saveRencanaTindakLanjut()
    {
        $this->validate([
            'rencanaForms.' . $this->currentIndicatorId . '.rencana_tindak_lanjut' => 'required|string',
            'rencanaForms.' . $this->currentIndicatorId . '.target_penyelesaian' => 'required|string',
        ], [
            'rencanaForms.' . $this->currentIndicatorId . '.rencana_tindak_lanjut.required' => 'Rencana tindak lanjut tidak boleh kosong',
            'rencanaForms.' . $this->currentIndicatorId . '.target_penyelesaian.required' => 'Target penyelesaian tidak boleh kosong',
        ]);

        // Check if we already have a record
        $rencana = RtmRencanaTindakLanjut::updateOrCreate(
            [
                'survei_id' => $this->currentIndicatorId,
                'rtm_id' => $this->rtm->id,
                'fakultas_id' => $this->selectedFakultas,
            ],
            [
                'rencana_tindak_lanjut' => $this->rencanaForms[$this->currentIndicatorId]['rencana_tindak_lanjut'],
                'target_penyelesaian' => $this->rencanaForms[$this->currentIndicatorId]['target_penyelesaian'],
            ]
        );

        session()->flash('toastMessage', 'Rencana tindak lanjut berhasil disimpan!');
        session()->flash('toastType', 'success');

        $this->closeRencanaForm();
    }

    public function deleteRencanaTindakLanjut($indicatorId)
    {
        $query = RtmRencanaTindakLanjut::where('survei_id', $indicatorId)
            ->where('rtm_id', $this->rtm->id);
        if ($this->selectedFakultas) {
            $query->where('fakultas_id', $this->selectedFakultas);
        } else {
            $query->whereNull('fakultas_id');
        }
        $query->delete();
        
        // Reset the form data for this indicator
        $this->rencanaForms[$indicatorId] = [
            'rencana_tindak_lanjut' => '',
            'target_penyelesaian' => '',
        ];
        
        session()->flash('toastMessage', 'Rencana tindak lanjut berhasil dihapus!');
        session()->flash('toastType', 'success');
    }

    public function updatedSelectedFakultas()
    {
        $this->loadSurveiData();
        $this->initializeRencanaForms();
    }

    public function resetFilter()
    {
        $this->selectedFakultas = null;
        $this->loadSurveiData();
        $this->initializeRencanaForms();
    }

    public function render()
    {
        return view('livewire.admin.master.rtm.view-survei')
            ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
            ->title('UNG RTM - Survei Data');
    }
} 