<?php

namespace App\Livewire\Admin\Master\Rtm;

use App\Models\RTM;
use App\Services\AmiService;
use App\Services\SurveiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $master = 'RTM';
    public $data = [];
    private $view = "livewire.admin.master.rtm.index";

    //CURRENT ATTRIBUTE
    public $anchor_ami = [];
    public $anchor_survei = [];

    public $rtm = [
        'name' => '',
        'tahun' => '',
        'ami_anchor' => [],
        'survei_anchor' => [],
        'akreditas_anchor' => [],
    ];

    protected $listeners = ['deleteFakultas'];

    public function mount(AmiService $amiService, SurveiService $surveiService)
    {
        $this->anchor_ami = $amiService->getAnchor()['data'];
        $this->anchor_survei = $surveiService->getAnchor()['data'];
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->data = RTM::all();
    }

    public function render()
    {
        return view($this->view)
            ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
            ->title('UNG RTM - Master RTM');
    }

    public function submit()
    {
        $this->validate([
            'rtm.name' => 'required|string|max:255',
            'rtm.tahun' => 'required|integer',
            'rtm.ami_anchor' => 'array',
            'rtm.survei_anchor' => 'array',
        ]);

        // Create RTM record
        RTM::create([
            'name' => $this->rtm['name'],
            'tahun' => $this->rtm['tahun'],
            'ami_anchor' => $this->rtm['ami_anchor'],
            'survei_anchor' => $this->rtm['survei_anchor'],
        ]);

        session()->flash('toastMessage', 'RTM berhasil ditambahkan!');
        session()->flash('toastType', 'success');

        $this->resetForm();
        $this->refreshData();
        
        return redirect()->route('dashboard.master.rtm.index');
    }

    public function resetForm()
    {
        $this->rtm = [
            'name' => '',
            'tahun' => '',
            'ami_anchor' => [],
            'survei_anchor' => [],
            'akreditas_anchor' => [],
        ];
    }
    
    public function deleteFakultas($id)
    {
        $rtm = RTM::find($id);
        if ($rtm) {
            $rtm->delete();
            session()->flash('toastMessage', 'RTM berhasil dihapus!');
            session()->flash('toastType', 'success');
        } else {
            session()->flash('toastMessage', 'RTM tidak ditemukan!');
            session()->flash('toastType', 'error');
        }
        
        $this->refreshData();
    }
}
