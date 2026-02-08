<?php

namespace App\Livewire\Admin\Master\Rtm;

use App\Models\Fakultas;
use App\Models\RTM;
use App\Models\RtmRencanaTindakLanjut;
use App\Services\SurveiService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;

class ViewSurvei extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $rtm = null;
    public $surveiId = null;
    public $selectedFakultas = null;
    public $selectedProdi = null;
    public $fakultas = [];
    public $prodis = [];
    public $surveiData = [];
    public $anchorName = '';
    public $isTemuan = false;

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
        // Detect temuan context via route name or RTM flag
        $routeTemuan = Str::contains(RouteFacade::currentRouteName(), 'temuan');
        $this->isTemuan = $routeTemuan || (bool) ($this->rtm->is_temuan ?? false);

        // Restrict temuan access to Universitas role only
        if ($this->isTemuan && $this->user->role->name !== 'Universitas') {
            session()->flash('toastMessage', 'Akses Temuan hanya untuk role Universitas');
            session()->flash('toastType', 'error');
            return redirect()->route('dashboard.master.rtm.view-survei', ['rtm_id' => $rtm_id, 'survei_id' => $survei_id]);
        }

        if ($this->user->role->name == 'Fakultas') {
            $this->selectedFakultas = $this->user->fakultas_id;
            $this->loadProdis();
        } elseif ($this->user->role->name == 'Prodi') {
            $this->selectedFakultas = $this->user->fakultas_id;
            $this->selectedProdi = $this->user->prodi_id;
            $this->loadProdis();
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

    public function loadProdis()
    {
        if ($this->selectedFakultas) {
            $fakultas = Fakultas::find($this->selectedFakultas);
            if ($fakultas) {
                $this->prodis = $fakultas->prodis()->get();
            } else {
                $this->prodis = [];
            }
        } else {
            $this->prodis = [];
        }
    }

    public function loadSurveiData()
    {
        try {
            $surveiService = app(SurveiService::class);
            $totalItem = $this->isTemuan ? 0 : 5;

            if ($this->user->role->name == 'Prodi') {
                // For Prodi role, only show their own data
                $result = $surveiService->getSurveiProdi($this->surveiId, $this->user->prodi_id, $totalItem);
            } else {
                // Get the Survei ID from the fakultas, if one is selected
                $fakultasSurveiId = 'null';
                if ($this->selectedFakultas) {
                    $fakultas = Fakultas::find($this->selectedFakultas);
                    if ($fakultas) {
                        $fakultasSurveiId = $fakultas->survei;

                        // If prodi is selected, get prodi-specific data
                        if ($this->selectedProdi) {
                            $result = $surveiService->getSurveiProdi($this->surveiId, $this->selectedProdi, $totalItem);
                        } else {
                            $result = $surveiService->getSurvei($this->surveiId, $fakultasSurveiId, $totalItem);
                        }
                    }
                } else {
                    $result = $surveiService->getSurvei($this->surveiId, $fakultasSurveiId, $totalItem);
                }
            }

            // Initialize with empty structure to avoid foreach errors
            $this->surveiData = ['data' => ['tabel' => []]];
            if (isset($result['data']) && !empty($result['data'])) {
                $this->surveiData = $result;
            }
            // dd($this->surveiData);
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
        $query = RtmRencanaTindakLanjut::where('survei_id', $indicatorId)
            ->where('rtm_id', $this->rtm->id);

        if ($this->user->role->name == 'Prodi') {
            $query->where('prodi_id', $this->user->prodi_id);
        } else {
            if ($this->selectedProdi) {
                $query->where('prodi_id', $this->selectedProdi);
            } elseif ($this->selectedFakultas) {
                $query->where('fakultas_id', $this->selectedFakultas)->where('prodi_id', null);
            } else {
                $query->whereNull('fakultas_id');
            }
        }

        $rencana = $query->first();

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
        // Conditional validation: in temuan context, only target_penyelesaian is required
        if ($this->isTemuan) {
            $this->validate([
                'rencanaForms.' . $this->currentIndicatorId . '.target_penyelesaian' => 'required|string',
            ], [
                'rencanaForms.' . $this->currentIndicatorId . '.target_penyelesaian.required' => 'Target penyelesaian tidak boleh kosong',
            ]);
        } else {
            $this->validate([
                'rencanaForms.' . $this->currentIndicatorId . '.rencana_tindak_lanjut' => 'required|string',
                'rencanaForms.' . $this->currentIndicatorId . '.target_penyelesaian' => 'required|string',
            ], [
                'rencanaForms.' . $this->currentIndicatorId . '.rencana_tindak_lanjut.required' => 'Rencana tindak lanjut tidak boleh kosong',
                'rencanaForms.' . $this->currentIndicatorId . '.target_penyelesaian.required' => 'Target penyelesaian tidak boleh kosong',
            ]);
        }

        $isProdiSpecific = $this->user->role->name == 'Prodi' || !empty($this->selectedProdi);
        $data = [
            'survei_id' => $this->currentIndicatorId,
            'rtm_id' => $this->rtm->id,
            'rencana_tindak_lanjut' => $this->rencanaForms[$this->currentIndicatorId]['rencana_tindak_lanjut'] ?? '',
            'target_penyelesaian' => $this->rencanaForms[$this->currentIndicatorId]['target_penyelesaian'],
            'fakultas_id' => $isProdiSpecific ? null : ($this->user->role->name == 'Prodi' ? null : $this->selectedFakultas),
            'prodi_id' => $this->user->role->name == 'Prodi' ? $this->user->prodi_id : ($this->selectedProdi ?: null),
        ];

        // Check if we already have a record
        RtmRencanaTindakLanjut::updateOrCreate(
            [
                'survei_id' => $this->currentIndicatorId,
                'rtm_id' => $this->rtm->id,
                'fakultas_id' => $data['fakultas_id'],
                'prodi_id' => $data['prodi_id'],
            ],
            [
                'rencana_tindak_lanjut' => $data['rencana_tindak_lanjut'],
                'target_penyelesaian' => $data['target_penyelesaian'],
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

        if ($this->user->role->name == 'Prodi') {
            $query->where('prodi_id', $this->user->prodi_id);
        } else {
            if ($this->selectedProdi) {
                $query->where('prodi_id', $this->selectedProdi);
            } elseif ($this->selectedFakultas) {
                $query->where('fakultas_id', $this->selectedFakultas)->where('prodi_id', null);
            } else {
                $query->whereNull('fakultas_id');
            }
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
        $this->selectedProdi = null; // Reset prodi selection when fakultas changes
        $this->loadProdis(); // Load prodis for selected fakultas
        $this->loadSurveiData();
        $this->initializeRencanaForms();
    }

    public function updatedSelectedProdi()
    {
        $this->loadSurveiData();
        $this->initializeRencanaForms();
    }

    public function resetFilter()
    {
        if ($this->user->role->name != 'Prodi') {
            $this->selectedFakultas = null;
            $this->selectedProdi = null;
            $this->prodis = [];
            $this->loadSurveiData();
            $this->initializeRencanaForms();
        }
    }

    public function render()
    {
        return view('livewire.admin.master.rtm.view-survei')
            ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
            ->title('UNG RTM - Survei Data');
    }
}
