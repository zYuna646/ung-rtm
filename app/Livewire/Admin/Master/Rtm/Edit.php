<?php

namespace App\Livewire\Admin\Master\Rtm;

use App\Models\RTM;
use App\Services\AmiService;
use App\Services\SurveiService;
use Livewire\Component;

class Edit extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $master = 'RTM';

    public $rtmId;
    public $rtm = [
        'name' => '',
        'tahun' => '',
        'ami_anchor' => [],
        'survei_anchor' => [],
        'akreditas_anchor' => [],
    ];

    public $anchor_ami = [];
    public $anchor_survei = [];

    public function mount(AmiService $amiService, SurveiService $surveiService, $id)
    {
        $this->rtmId = $id;
        $this->anchor_ami = $amiService->getAnchor()['data'];
        $this->anchor_survei = $surveiService->getAnchor()['data'];

        $rtm = RTM::find($id);
        if ($rtm) {
            $this->rtm = $rtm->toArray();
            $this->rtm['ami_anchor'] = $rtm->ami_anchor;
            $this->rtm['survei_anchor'] = $rtm->survei_anchor;
        } else {
            session()->flash('toastMessage', 'RTM tidak ditemukan!');
            session()->flash('toastType', 'error');
            return redirect()->route('dashboard.master.rtm.index');
        }
    }

    public function submit()
    {
        $this->validate([
            'rtm.name' => 'required|string|max:255',
            'rtm.tahun' => 'required|integer',
            'rtm.ami_anchor' => 'array',
            'rtm.survei_anchor' => 'array',
        ]);

        $rtm = RTM::find($this->rtmId);
        if ($rtm) {
            $rtm->update([
                'name' => $this->rtm['name'],
                'tahun' => $this->rtm['tahun'],
                'ami_anchor' => $this->rtm['ami_anchor'],
                'survei_anchor' => $this->rtm['survei_anchor'],
            ]);

            session()->flash('toastMessage', 'RTM berhasil diperbarui!');
            session()->flash('toastType', 'success');
        } else {
            session()->flash('toastMessage', 'RTM tidak ditemukan!');
            session()->flash('toastType', 'error');
        }

        return redirect()->route('dashboard.master.rtm.index');
    }

    public function cancel()
    {
        return redirect()->route('dashboard.master.rtm.index');
    }

    public function render()
    {
        return view('livewire.admin.master.rtm.edit')
            ->layout('components.layouts.app', [
                'showNavbar' => $this->showNavbar,
                'showFooter' => $this->showFooter
            ])
            ->title('Edit RTM');
    }
}
