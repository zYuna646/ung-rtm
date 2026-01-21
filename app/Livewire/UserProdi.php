<?php

namespace App\Livewire;
use App\Models\Jurusan;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserProdi extends Component
{
    public $showNavbar = true;
    public $showFooter = true;
    public $master = 'User Prodi';
    public $showModal = false;

    public $userProdi = [
        'nama' => '',
        'email' => '',
        'password' => '',
        'prodi_id' => '',
        'fakultas_id' => '',
    ];

    public $dataUserProdi = [];
    public $dataProdi = [];
    public $dataJurusan = [];
    public $dataFakultas;
    private $prodiRoleId;

    protected $listeners = ['refreshData' => '$refresh'];

    public function mount()
    {
        $this->dataFakultas = Fakultas::all();
        $this->prodiRoleId = 3;
        Log::info('ProdiRoleId set to: ' . $this->prodiRoleId);
        $this->loadUserProdi();
    }

    public function loadUserProdi()
    {
        $this->dataUserProdi = User::where('role_id', $this->prodiRoleId)
            ->with(['fakultas', 'prodi'])
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.pengguna.prodi.user-prodi')
            ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
            ->title('UNG RTM - Pengguna Prodi');
    }

    public function getProdiByFakultas()
    {
        $fakultasId = $this->userProdi['fakultas_id'];
        $this->dataProdi = Prodi::where('fakultas_id', $fakultasId)->get();
    }

    public function addUserProdi()
    {
        // Validate the input
        $this->validate([
            'userProdi.nama' => 'required|string|max:255',
            'userProdi.email' => 'required|string|email|max:255|unique:users,email',
            'userProdi.password' => 'required|string|min:8',
            'userProdi.fakultas_id' => 'required',
            'userProdi.prodi_id' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $prodiRole = Role::where('name', 'Prodi')->first();
            if (!$prodiRole) {
                $prodiRole = Role::create([
                    'name' => 'Prodi',
                ]);
            }


            // Create user with explicit role_id
            $user = new User();
            $user->name = $this->userProdi['nama'];
            $user->email = $this->userProdi['email'];
            $user->password = bcrypt($this->userProdi['password']);
            $user->role_id = $prodiRole->id;
            $user->fakultas_id = $this->userProdi['fakultas_id'];
            $user->prodi_id = $this->userProdi['prodi_id'];
            $user->save();

            if (!$user || !$user->role_id) {
                throw new \Exception('Failed to set role_id for user');
            }

            DB::commit();

            // Reset the form
            $this->reset('userProdi');
            // Refresh the user list
            $this->loadUserProdi();
            
            $this->dispatch('closeModal');
            $this->dispatch('refreshDatatable');

            session()->flash('toastMessage', 'Data berhasil ditambahkan');
            session()->flash('toastType', 'success');

        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            Log::error('Error adding user prodi: ' . $e->getMessage());
            session()->flash('toastMessage', 'Terjadi kesalahan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
    }

    public function export()
    {
        return Excel::download(new \App\Exports\UserProdi(), 'user_prodi.xlsx');
    }

    public function deleteUser($id)
    {
        try {
            DB::beginTransaction();

            User::findOrFail($id)->delete();

            DB::commit();
            
            // Refresh the user list
            $this->loadUserProdi();
            $this->dispatch('refreshDatatable');

            session()->flash('toastMessage', 'Data berhasil dihapus');
            session()->flash('toastType', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user prodi: ' . $e->getMessage());
            session()->flash('toastMessage', 'Terjadi kesalahan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
    }
}
