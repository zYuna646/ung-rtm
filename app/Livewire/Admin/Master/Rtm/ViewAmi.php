<?php

namespace App\Livewire\Admin\Master\Rtm;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\RTM;
use App\Models\RtmRencanaTindakLanjut;
use App\Services\AmiService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;

class ViewAmi extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $rtm = null;
    public $anchorId = null;
    public $selectedFakultas = null;
    public $selectedProdi = null;
    public $fakultas = [];
    public $prodis = [];
    public $amiData = [];
    public $anchorName = '';
    public $categoryAverages = [];
    public $overallAverage = 0;
    public $isLoading = false;
    public $isTemuan = false;

    // New properties for rencana tindak lanjut form
    public $rencanaForms = [];
    public $formIsOpen = false;
    public $currentIndicatorId = null;
    public $currentIndicatorDesc = null;

    // New properties for program modal
    public $programModalOpen = false;
    public $programType = null; // 'sesuai' or 'tidak_sesuai'
    public $programIds = [];
    public $programItems = [];
    public $indicatorCode = null;
    public $indicatorDesc = null;
    
    public $program = [];
    public $user = null;

    public function mount($rtm_id, $anchor_id)
    {
        $this->rtm = RTM::findOrFail($rtm_id);
        $this->anchorId = $anchor_id;
        $this->fakultas = Fakultas::all();
        $this->user = Auth::user();
        // Detect temuan context via route name or RTM flag
        $routeTemuan = Str::contains(RouteFacade::currentRouteName(), 'temuan');
        $this->isTemuan = $routeTemuan || (bool) ($this->rtm->is_temuan ?? false);

        // Restrict temuan access to Universitas role only
        if ($this->isTemuan && $this->user->role->name !== 'Universitas') {
            session()->flash('toastMessage', 'Akses Temuan hanya untuk role Universitas');
            session()->flash('toastType', 'error');
            return redirect()->route('dashboard.master.rtm.view-ami', ['rtm_id' => $rtm_id, 'anchor_id' => $anchor_id]);
        }
        $amiService = app(AmiService::class);
        $program = $amiService->getProgram();
        $this->program = $program['data'];
        
        if ($this->user->role->name == 'Fakultas') {
            $this->selectedFakultas = $this->user->fakultas_id;
            $this->loadProdis();
        }
        elseif ($this->user->role->name == 'Prodi') {
            $this->selectedFakultas = $this->user->fakultas_id;
            $this->selectedProdi = $this->user->prodi_id;
            $this->loadProdis();
        }

        // Get AMI data
        $this->loadAmiData();

        // Get anchor name from AmiService
        $anchors = $amiService->getAnchor()['data'];
        $matchedAnchor = collect($anchors)->firstWhere('id', (int) $this->anchorId);
        $this->anchorName = $matchedAnchor ? $matchedAnchor['periode_name'] : "AMI #$this->anchorId";

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

    public function loadAmiData()
    {
        $this->isLoading = true;
        
        try {
            $amiService = app(AmiService::class);
            $prodiAmiId = 'null';
            if ($this->user->role->name == 'Prodi') {
                // For Prodi role, only show their own data
                $prodiAmiId = Prodi::find($this->user->prodi_id)->ami;
                $result = $amiService->getAmiProdi($this->anchorId, $prodiAmiId);
            } else {
                // Get the AMI ID from the fakultas, if one is selected
                $fakultasAmiId = 'null';
                if ($this->selectedFakultas) {
                    $fakultas = Fakultas::find($this->selectedFakultas);
                    if ($fakultas) {
                        $fakultasAmiId = $fakultas->ami;
                        
                        // If prodi is selected, get prodi-specific data
                        if ($this->selectedProdi) {
                            $prodiAmiId = Prodi::find($this->selectedProdi)->ami;
                            $result = $amiService->getAmiProdi($this->anchorId, $prodiAmiId);
                        } else {
                            $result = $amiService->getAmi($this->anchorId, $fakultasAmiId);
                        }
                    } else {
                        $fakultasAmiId = "null";
                        $result = $amiService->getAmi($this->anchorId, $fakultasAmiId);
                    }
                } else {
                    $result = $amiService->getAmi($this->anchorId, $fakultasAmiId);
                }
            }
            
            if (isset($result['data']) && !empty($result['data'])) {
                if ($this->user->role->name == 'Prodi' || $this->selectedProdi) {
                    $this->amiData = $result['data']['rtm'];
                } else {
                    $this->amiData = $result['data'];
                }
                $this->calculateAverages();
            } else {
                $this->amiData = [];
                $this->categoryAverages = [];
                $this->overallAverage = 0;
            }
        } catch (\Exception $e) {
            $this->amiData = [];
            $this->categoryAverages = [];
            $this->overallAverage = 0;
            session()->flash('toastMessage', 'Gagal memuat data: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
        
        $this->isLoading = false;
    }
    
    /**
     * Calculate average performance scores per category and overall
     */
    protected function calculateAverages()
    {
        $this->categoryAverages = [];
        $allScores = [];
        
        foreach ($this->amiData as $category => $items) {
            $categoryScores = [];
            
            foreach ($items as $indicator) {
                if (isset($indicator['score']) && is_numeric($indicator['score'])) {
                    $categoryScores[] = $indicator['score'];
                    $allScores[] = $indicator['score'];
                }
            }
            
            // Calculate category average
            if (count($categoryScores) > 0) {
                $this->categoryAverages[$category] = round(array_sum($categoryScores) / count($categoryScores), 2);
            } else {
                $this->categoryAverages[$category] = 0;
            }
        }
        
        // Calculate overall average
        if (count($allScores) > 0) {
            $this->overallAverage = round(array_sum($allScores) / count($allScores), 2);
        } else {
            $this->overallAverage = 0;
        }
    }

    protected function initializeRencanaForms()
    {
        $this->rencanaForms = [];

        if (count($this->amiData) > 0) {
            foreach ($this->amiData as $category => $items) {
                foreach ($items as $indicator) {
                    $this->loadRencanaTindakLanjut($indicator['id']);
                }
            }
        }
    }

    protected function loadRencanaTindakLanjut($indicatorId)
    {
        // Find existing rencana tindak lanjut for this indicator
        $query = RtmRencanaTindakLanjut::where('ami_id', $indicatorId)
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

        $data = [
            'ami_id' => $this->currentIndicatorId,
            'rtm_id' => $this->rtm->id,
            'rencana_tindak_lanjut' => $this->rencanaForms[$this->currentIndicatorId]['rencana_tindak_lanjut'] ?? '',
            'target_penyelesaian' => $this->rencanaForms[$this->currentIndicatorId]['target_penyelesaian'],
        ];

        if ($this->user->role->name == 'Prodi') {
            $data['fakultas_id'] = $this->user->fakultas_id;
            $data['prodi_id'] = $this->user->prodi_id;
        } else {
            $data['fakultas_id'] = $this->selectedFakultas;
            $data['prodi_id'] = $this->selectedProdi;
        }

        // Check if we already have a record
        RtmRencanaTindakLanjut::updateOrCreate(
            [
                'ami_id' => $this->currentIndicatorId,
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

    // New methods for program modal
    public function openProgramModal($type, $ids, $code = null, $desc = null)
    {
        $this->programType = $type;
        $this->programIds = json_decode($ids);
        $this->indicatorCode = $code;
        $this->indicatorDesc = $desc;
        $this->programItems = [];
        
        // Get program items based on IDs
        if (!empty($this->programIds) && !empty($this->program)) {
            foreach ($this->programIds as $id) {
                foreach ($this->program as $programItem) {
                    if ($programItem['id'] == $id) {
                        $this->programItems[] = $programItem;
                        break;
                    }
                }
            }
        }
        
        $this->programModalOpen = true;
    }
    
    public function closeProgramModal()
    {
        $this->programModalOpen = false;
        $this->programType = null;
        $this->programIds = [];
        $this->programItems = [];
        $this->indicatorCode = null;
        $this->indicatorDesc = null;
    }

    public function updatedSelectedFakultas()
    {
        $this->selectedProdi = null; // Reset prodi selection when fakultas changes
        $this->loadProdis(); // Load prodis for selected fakultas
        $this->loadAmiData();
        $this->initializeRencanaForms();
    }

    public function updatedSelectedProdi()
    {
        $this->loadAmiData();
        $this->initializeRencanaForms();
    }

    public function resetFilter()
    {
        if ($this->user->role->name != 'Prodi') {
            $this->selectedFakultas = null;
            $this->selectedProdi = null;
            $this->prodis = [];
            $this->loadAmiData();
            $this->initializeRencanaForms();
        }
    }

    public function deleteRencanaTindakLanjut($indicatorId)
    {
        $query = RtmRencanaTindakLanjut::where('ami_id', $indicatorId)
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

    public function render()
    {
        return view('livewire.admin.master.rtm.view-ami')
            ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
            ->title('UNG RTM - AMI Data');
    }
}
