<?php

namespace App\Livewire\Admin\Master\Rtm;

use App\Models\Fakultas;
use App\Models\RTM;
use App\Models\RtmLampiran;
use App\Services\AmiService;
use App\Services\SurveiService;
use App\Services\AkreditasiService;
use App\Models\RtmRencanaTindakLanjut;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\returnArgument;
use App\Models\RtmReport;

class Detail extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $showNavbar = true;
    public $showFooter = true;
    public $master = 'RTM';
    public $rtm = null;
    private $view = "livewire.admin.master.rtm.detail";
    public $fakultas = [];

    public $prodi = [];
    public $akreditasi = [];
    public $survei = [];
    public $ami = [];

    public $anchor_ami = [];
    public $anchor_survei = [];
    public $anchor_akreditas = [];
    public $akreditasiExpiringSoon = [];

    public $selectedFakultas = null;
    public $reportFakultas = null;
    public $surveyFakultas = null;
    public $surveyData = [];
    public $dataParameter = [];

    // Properties for akreditasi rencana tindak lanjut
    public $akreditasiRencanaForms = [];
    public $akreditasiFormIsOpen = false;
    public $currentAkreditasiId = null;
    public $user = null;
    public $currentAkreditasiProdi = null;

    public $rtmReport = [
        'mengetahui1_nama' => '',
        'mengetahui1_jabatan' => '',
        'mengetahui1_nip' => '',
        'mengetahui2_nama' => '',
        'mengetahui2_jabatan' => '',
        'mengetahui2_nip' => '',
        'pemimpin_rapat' => '',
        'notulis' => '',
        'tanggal_pelaksanaan' => '',
        'waktu_pelaksanaan' => '',
        'tempat_pelaksanaan' => '',
        'agenda' => '',
        'agenda_kegiatan' => '',
        'peserta' => '',
        'tahun_akademik' => '',
        'tujuan' => '',
        'hasil' => '',
        'kesimpulan' => '',
        'penutup' => ''
    ];

    // New lampiran properties
    public $newLampiran = [
        'judul' => '',
        'file' => null,
    ];

    public $lampiran = [];

    protected $listeners = ['refreshLampiran' => 'loadLampiran', 'updatedSelectedFakultas' => 'loadLampiran'];
    protected $paginationTheme = 'tailwind';

    // Add queryString to track the page parameter in the URL
    protected $queryString = ['page' => ['except' => 1]];
    public $page = 1;

    public function mount(AmiService $amiService, SurveiService $surveiService, AkreditasiService $akreditasiService, $id)
    {
        $this->anchor_ami = $amiService->getAnchor()['data'];
        $this->anchor_survei = $surveiService->getAnchor()['data'];
        $this->rtm = RTM::find($id);
        $this->user = Auth::user();
        if ($this->user->role->name == 'Fakultas') {
            $this->selectedFakultas = $this->user->fakultas_id;
        }
        else
        {
            $this->selectedFakultas = null;
        }
        $rtmReport = RtmReport::where('rtm_id', $this->rtm->id)->where('fakultas_id', $this->selectedFakultas ? $this->selectedFakultas : null)->first();
        if ($rtmReport) {
            $this->rtmReport = $rtmReport->toArray();
        } else {
            $this->rtmReport = null;
        }
        $this->fakultas = Fakultas::all();
        $this->loadLampiran();
        $this->loadAkreditasiData($akreditasiService);
        $this->initializeAkreditasiRencanaForms();

        // Load default values for tujuan, agenda, hasil, kesimpulan, penutup
        $this->loadDefaultReportValues();
    }

    public function loadAkreditasiData(AkreditasiService $akreditasiService)
    {
        $fakultasId = null;

        if ($this->selectedFakultas) {
            $fakultas = Fakultas::find($this->selectedFakultas);
            if ($fakultas && !empty($fakultas->akreditasi)) {
                $fakultasId = $fakultas->akreditasi;
            }
        } else {
            $fakultasId = null;
        }

        $result = $akreditasiService->getAkreditas($fakultasId);
        if ($result && isset($result['data']) && isset($result['data']['akreditasi_expiring'])) {
            $this->akreditasiExpiringSoon = collect($result['data']['akreditasi_expiring']);
        } else {
            $this->akreditasiExpiringSoon = collect([]);
        }
    }

    public function getPaginatedAkreditasiProperty()
    {
        $perPage = 10;
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $this->akreditasiExpiringSoon->forPage($this->page, $perPage),
            $this->akreditasiExpiringSoon->count(),
            $perPage,
            $this->page
        );

        return $paginator->withPath(request()->url())->onEachSide(1);
    }

    protected function initializeAkreditasiRencanaForms()
    {
        $this->akreditasiRencanaForms = [];

        if ($this->akreditasiExpiringSoon->count() > 0) {
            foreach ($this->akreditasiExpiringSoon as $akreditasi) {
                $this->loadAkreditasiRencanaTindakLanjut($akreditasi['akre_id']);
            }
        }
    }

    protected function loadAkreditasiRencanaTindakLanjut($akreditasiId)
    {
        // Find existing rencana tindak lanjut for this akreditasi
        $rencana = RtmRencanaTindakLanjut::where('akreditasi_id', $akreditasiId)
            ->where('rtm_id', $this->rtm->id)
            ->where('fakultas_id', $this->selectedFakultas)
            ->first();

        // Initialize the form data
        $this->akreditasiRencanaForms[$akreditasiId] = [
            'rencana_tindak_lanjut' => $rencana ? $rencana->rencana_tindak_lanjut : '',
            'target_penyelesaian' => $rencana ? $rencana->target_penyelesaian : '',
        ];
    }

    public function openAkreditasiRencanaForm($akreditasiId, $prodiNama)
    {
        $this->currentAkreditasiId = $akreditasiId;
        $this->currentAkreditasiProdi = $prodiNama;
        $this->akreditasiFormIsOpen = true;
    }

    public function closeAkreditasiRencanaForm()
    {
        $this->akreditasiFormIsOpen = false;
        $this->currentAkreditasiId = null;
        $this->currentAkreditasiProdi = null;
    }

    public function saveAkreditasiRencanaTindakLanjut()
    {
        $this->validate([
            'akreditasiRencanaForms.' . $this->currentAkreditasiId . '.rencana_tindak_lanjut' => 'required|string',
            'akreditasiRencanaForms.' . $this->currentAkreditasiId . '.target_penyelesaian' => 'required|string',
        ], [
            'akreditasiRencanaForms.' . $this->currentAkreditasiId . '.rencana_tindak_lanjut.required' => 'Rencana tindak lanjut tidak boleh kosong',
            'akreditasiRencanaForms.' . $this->currentAkreditasiId . '.target_penyelesaian.required' => 'Target penyelesaian tidak boleh kosong',
        ]);

        // Check if we already have a record
        $rencana = RtmRencanaTindakLanjut::updateOrCreate(
            [
                'akreditasi_id' => $this->currentAkreditasiId,
                'rtm_id' => $this->rtm->id,
                'fakultas_id' => $this->selectedFakultas,
            ],
            [
                'rencana_tindak_lanjut' => $this->akreditasiRencanaForms[$this->currentAkreditasiId]['rencana_tindak_lanjut'],
                'target_penyelesaian' => $this->akreditasiRencanaForms[$this->currentAkreditasiId]['target_penyelesaian'],
            ]
        );

        session()->flash('toastMessage', 'Rencana tindak lanjut berhasil disimpan!');
        session()->flash('toastType', 'success');

        $this->closeAkreditasiRencanaForm();
    }

    public function deleteAkreditasiRencanaTindakLanjut($akreditasiId)
    {
        $query = RtmRencanaTindakLanjut::where('akreditasi_id', $akreditasiId)
            ->where('rtm_id', $this->rtm->id);
        if ($this->selectedFakultas) {
            $query->where('fakultas_id', $this->selectedFakultas);
        } else {
            $query->whereNull('fakultas_id');
        }
        $query->delete();

        // Reset the form data for this akreditasi
        $this->akreditasiRencanaForms[$akreditasiId] = [
            'rencana_tindak_lanjut' => '',
            'target_penyelesaian' => '',
        ];

        session()->flash('toastMessage', 'Rencana tindak lanjut berhasil dihapus!');
        session()->flash('toastType', 'success');
    }

    public function loadLampiran()
    {
        $query = RtmLampiran::where('rtm_id', $this->rtm->id)->where('fakultas_id', $this->selectedFakultas);
        $this->lampiran = $query->get();
    }

    public function resetFilter()
    {
        $this->selectedFakultas = null;
        $this->loadLampiran();
        $this->loadAkreditasiData(app(AkreditasiService::class));
        $this->initializeAkreditasiRencanaForms();
        $this->page = 1;
    }

    public function render()
    {
        // Always reload lampiran on render to ensure it's filtered by current selectedFakultas
        $this->loadLampiran();

        return view($this->view, [
            'paginatedAkreditasi' => $this->getPaginatedAkreditasiProperty()
        ])
            ->layout('components.layouts.app', ['showNavbar' => $this->showNavbar, 'showFooter' => $this->showFooter])
            ->title('UNG RTM - Master RTM');
    }

    public function updatedSelectedFakultas()
    {
        $this->loadLampiran();
        $this->loadAkreditasiData(app(AkreditasiService::class));
        $this->initializeAkreditasiRencanaForms();
        $this->page = 1;

        // Reload default values when faculty changes
        $this->loadDefaultReportValues();
    }

    public function uploadLampiran()
    {
        $this->validate([
            'newLampiran.judul' => 'required|string|max:255',
            'newLampiran.file' => 'required|file|max:10240', // 10MB max file size
        ]);

        try {
            $file = $this->newLampiran['file'];
            $originalName = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();

            $fileName = 'lampiran_rtm_' . $this->rtm->id . '_' . time() . '.' . $fileType;
            $file->storeAs('public/rtm_lampiran', $fileName);
            $path = 'rtm_lampiran/' . $fileName;

            $fakultasId = null; // Default for university level

            // Use the selectedFakultas for fakultasId if it's set
            if ($this->selectedFakultas) {
                $fakultasId = $this->selectedFakultas;
            }

            // Save to database
            RtmLampiran::create([
                'rtm_id' => $this->rtm->id,
                'fakultas_id' => $fakultasId,
                'judul' => $this->newLampiran['judul'],
                'file_path' => $path,
                'file_name' => $originalName,
                'file_type' => $fileType,
                'file_size' => $fileSize
            ]);

            // Reset the form
            $this->newLampiran = [
                'judul' => '',
                'file' => null,
            ];

            // Refresh lampiran list
            $this->loadLampiran();

            session()->flash('toastMessage', 'Lampiran berhasil diunggah');
            session()->flash('toastType', 'success');
        } catch (\Exception $e) {
            session()->flash('toastMessage', 'Gagal mengunggah lampiran: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
    }

    public function deleteLampiran($id)
    {
        try {
            $lampiran = RtmLampiran::findOrFail($id);

            // Delete the physical file
            if (Storage::exists($lampiran->file_path)) {
                Storage::delete($lampiran->file_path);
            }

            // Delete the record
            $lampiran->delete();

            // Refresh lampiran list
            $this->loadLampiran();

            session()->flash('toastMessage', 'Lampiran berhasil dihapus');
            session()->flash('toastType', 'success');
        } catch (\Exception $e) {
            session()->flash('toastMessage', 'Gagal menghapus lampiran: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
    }

    /**
     * Validate form, save report data, and generate RTM report
     * 
     * @return mixed
     */
    public function generateReport()
    {
        // Validate the form data
        $this->validate();

        try {
            // First save report data to database
            $this->saveToDatabase();

            // Then generate and download the document
            return $this->downloadDocument();

        } catch (\Exception $e) {
            // Log the error and display a message to the user
            \Log::error('Failed to generate report: ' . $e->getMessage());
            session()->flash('toastMessage', 'Gagal membuat laporan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
            return null;
        }
    }

    /**
     * Save report data to database
     */
    private function saveToDatabase()
    {
        // Save report data to database - don't sanitize HTML fields
        RtmReport::updateOrCreate([
            'rtm_id' => $this->rtm->id,
            'fakultas_id' => $this->selectedFakultas,
        ], [
            'mengetahui1_nama' => $this->rtmReport['mengetahui1_nama'],
            'mengetahui1_jabatan' => $this->rtmReport['mengetahui1_jabatan'],
            'mengetahui1_nip' => $this->rtmReport['mengetahui1_nip'],
            'mengetahui2_nama' => $this->rtmReport['mengetahui2_nama'],
            'mengetahui2_jabatan' => $this->rtmReport['mengetahui2_jabatan'],
            'mengetahui2_nip' => $this->rtmReport['mengetahui2_nip'],
            'pemimpin_rapat' => $this->rtmReport['pemimpin_rapat'],
            'notulis' => $this->rtmReport['notulis'],
            'tanggal_pelaksanaan' => $this->rtmReport['tanggal_pelaksanaan'],
            'waktu_pelaksanaan' => $this->rtmReport['waktu_pelaksanaan'],
            'tempat_pelaksanaan' => $this->rtmReport['tempat_pelaksanaan'],
            'agenda' => $this->rtmReport['agenda'],
            'agenda_kegiatan' => $this->rtmReport['agenda_kegiatan'],
            'peserta' => $this->rtmReport['peserta'],
            'tahun_akademik' => $this->rtmReport['tahun_akademik'],
            'tujuan' => $this->rtmReport['tujuan'],
            'hasil' => $this->rtmReport['hasil'],
            'kesimpulan' => $this->rtmReport['kesimpulan'],
            'penutup' => $this->rtmReport['penutup'],
        ]);
    }

    /**
     * Save report data to database without generating PDF
     */
    public function saveReport()
    {
        $this->validate();

        try {
            // Save the report data
            $this->saveToDatabase();

            // Show success message
            session()->flash('toastMessage', 'Laporan berhasil disimpan');
            session()->flash('toastType', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to save report: ' . $e->getMessage());
            session()->flash('toastMessage', 'Gagal menyimpan laporan: ' . $e->getMessage());
            session()->flash('toastType', 'error');
        }
    }

    private function resetReportForm()
    {
        $this->rtmReport = [
            'mengetahui1_nama' => '',
            'mengetahui1_jabatan' => '',
            'mengetahui1_nip' => '',
            'mengetahui2_nama' => '',
            'mengetahui2_jabatan' => '',
            'mengetahui2_nip' => '',
            'pemimpin_rapat' => '',
            'notulis' => '',
            'tanggal_pelaksanaan' => '',
            'waktu_pelaksanaan' => '',
            'tempat_pelaksanaan' => '',
            'agenda' => '',
            'agenda_kegiatan' => '',
            'peserta' => '',
            'tahun_akademik' => '',
            'tujuan' => '',
            'hasil' => '',
            'kesimpulan' => '',
            'penutup' => ''
        ];
    }

    /**
     * Collect all data needed for the RTM report
     *
     * @return array
     */
    private function collectReportData()
    {
        // Get the services
        $amiService = app(AmiService::class);
        $surveiService = app(SurveiService::class);
        $akreditasiService = app(AkreditasiService::class);

        $fakultas = Fakultas::find($this->selectedFakultas);
        // Process AMI data
        $ami_data_by_period = [];
        if (!empty($this->rtm->ami_anchor)) {
            foreach ($this->rtm->ami_anchor as $anchorId) {
                $amiResult = $amiService->getAmi($anchorId, $this->selectedFakultas ? $fakultas->ami : 'null');
                if (isset($amiResult['data']) && !empty($amiResult['data'])) {
                    // Store the period name and data separately
                    $matchedAnchor = collect($this->anchor_ami)->firstWhere('id', $anchorId);
                    $periodName = $matchedAnchor['periode_name'] ?? 'Periode AMI ' . $anchorId;

                    // Get Rencana Tindak Lanjut data for each AMI indicator in this period
                    // $amiRtlData = RtmRencanaTindakLanjut::where('rtm_id', $this->rtm->id)
                    //     ->when(isset($this->selectedFakultas), function ($query) {
                    //         if ($this->selectedFakultas) {
                    //             $query->where('fakultas_id', $this->selectedFakultas);
                    //         } else {
                    //             $query->whereNull('fakultas_id');
                    //         }
                    //     })
                    //     ->where('ami_id', '!=', null)
                    //     ->get()
                    //     ->keyBy('ami_id');

                    $amiRtlData = RtmRencanaTindakLanjut::where('rtm_id', $this->rtm->id)
                        ->where('fakultas_id', $this->selectedFakultas)
                        ->where('ami_id', '!=', null)
                        ->get()
                        ->keyBy('ami_id');


                    // Group AMI data by category for this period
                    $grouped_data = [];
                    $all_scores = [];
                    $category_averages = [];

                    foreach ($amiResult['data'] as $category => $items) {
                        $tmp = [];
                        $category_scores = [];

                        foreach ($items as $key => $indicator) {
                            if (isset($amiRtlData[$indicator['id']])) {
                                $indicator['rencana_tindak_lanjut'] = $amiRtlData[$indicator['id']]->rencana_tindak_lanjut;
                                $indicator['target_penyelesaian'] = $amiRtlData[$indicator['id']]->target_penyelesaian;

                                // Only include indicators that have RTL data (not empty)
                                if (!empty($indicator['rencana_tindak_lanjut']) || !empty($indicator['target_penyelesaian'])) {
                                    // Collect scores for averaging
                                    if (isset($indicator['score']) && is_numeric($indicator['score'])) {
                                        $category_scores[] = $indicator['score'];
                                        $all_scores[] = $indicator['score'];
                                    }
                                    $tmp[] = $indicator;
                                }
                            }
                            // Skip indicators without RTL data
                        }

                        // Only add category if it has indicators with RTL
                        if (!empty($tmp)) {
                            // Calculate category average
                            if (count($category_scores) > 0) {
                                $category_averages[$category] = round(array_sum($category_scores) / count($category_scores), 2);
                            } else {
                                $category_averages[$category] = 0;
                            }

                            $grouped_data[$category] = $tmp;
                        }
                    }

                    // Only add period if it has data with RTL
                    if (!empty($grouped_data)) {
                        // Calculate overall average
                        $overall_average = count($all_scores) > 0 ? round(array_sum($all_scores) / count($all_scores), 2) : 0;

                        $ami_data_by_period[$periodName] = [
                            'categories' => $grouped_data,
                            'category_averages' => $category_averages,
                            'overall_average' => $overall_average
                        ];
                    }
                }
            }
        }

        // Process Survei data
        $survei_data_by_period = [];
        if (!empty($this->rtm->survei_anchor)) {
            foreach ($this->rtm->survei_anchor as $anchorId) {
                $surveiResult = $surveiService->getSurvei($anchorId, $this->selectedFakultas ? $fakultas->survei : 'null');
                if (isset($surveiResult['data']) && !empty($surveiResult['data'])) {
                    // Store the period name and data separately
                    $matchedAnchor = collect($this->anchor_survei)->firstWhere('id', $anchorId);
                    $periodName = $matchedAnchor['name'] ?? 'Periode Survei ' . $anchorId;

                    // Get Rencana Tindak Lanjut data for each survey indicator in this period
                    // $surveiRtlData = RtmRencanaTindakLanjut::where('rtm_id', $this->rtm->id)
                    //     ->when(isset($this->selectedFakultas), function ($query) {
                    //         if ($this->selectedFakultas) {
                    //             $query->where('fakultas_id', $this->selectedFakultas);
                    //         } else {
                    //             $query->whereNull('fakultas_id');
                    //         }
                    //     })
                    //     ->where('survei_id', '!=', null)
                    //     ->get()
                    //     ->keyBy('survei_id');

                    $surveiRtlData = RtmRencanaTindakLanjut::where('rtm_id', $this->rtm->id)
                        ->where('fakultas_id', $this->selectedFakultas)
                        ->where('survei_id', '!=', null)
                        ->get()
                        ->keyBy('survei_id');

                    // Group survey data for this period
                    $grouped_data = [];
                    if (isset($surveiResult['data']['tabel'])) {
                        $indicators = $surveiResult['data']['tabel'];
                        foreach ($indicators as $index => $indicator) {
                            // Add RTL data if exists
                            if (isset($surveiRtlData[$indicator['id']])) {
                                $indicator['rencana_tindak_lanjut'] = $surveiRtlData[$indicator['id']]->rencana_tindak_lanjut;
                                $indicator['target_penyelesaian'] = $surveiRtlData[$indicator['id']]->target_penyelesaian;

                                // Only include indicators that have RTL data (not empty)
                                if (!empty($indicator['rencana_tindak_lanjut']) || !empty($indicator['target_penyelesaian'])) {
                                    $grouped_data[] = $indicator;
                                }
                            }
                            // Skip indicators without RTL data
                        }
                    }

                    // Only add period if it has data with RTL
                    if (!empty($grouped_data)) {
                        $survei_data_by_period[$periodName] = $grouped_data;
                    }
                }
            }
        }

        // Process Akreditasi data
        $akreditasi_data = [];
        $universitas_data = [];
        $akreditasi_fakultas_id = null;

        if ($this->selectedFakultas) {
            $fakultas = Fakultas::find($this->selectedFakultas);
            if ($fakultas && !empty($fakultas->akreditasi)) {
                $akreditasi_fakultas_id = $fakultas->akreditasi;
            }
        }

        // Get akreditasi data from service
        $akreditasiResult = $akreditasiService->getAkreditas($akreditasi_fakultas_id);
        if ($akreditasiResult && isset($akreditasiResult['data'])) {
            $universitas_data = $akreditasiResult['data']['universitas'] ?? [];
            $akreditasi_data = $akreditasiResult['data']['akreditasi_expiring'] ?? [];

            // Get Rencana Tindak Lanjut data for each akreditasi
            if (!empty($akreditasi_data)) {
                // $akreditasiRtlData = RtmRencanaTindakLanjut::where('rtm_id', $this->rtm->id)
                //     ->when($this->selectedFakultas, function ($query) {
                //         $query->where('fakultas_id', $this->selectedFakultas);
                //     })
                //     ->where('akreditasi_id', '!=', null)
                //     ->get()
                //     ->keyBy('akreditasi_id');

                $akreditasiRtlData = RtmRencanaTindakLanjut::where('rtm_id', $this->rtm->id)
                    ->where('fakultas_id', $this->selectedFakultas)
                    ->where('akreditasi_id', '!=', null)
                    ->get()
                    ->keyBy('akreditasi_id');


                // Add RTL data to each akreditasi record
                foreach ($akreditasi_data as $key => $akreditasi) {
                    if (isset($akreditasiRtlData[$akreditasi['akre_id']])) {
                        $akreditasi_data[$key]['rencana_tindak_lanjut'] = $akreditasiRtlData[$akreditasi['akre_id']]->rencana_tindak_lanjut;
                        $akreditasi_data[$key]['target_penyelesaian'] = $akreditasiRtlData[$akreditasi['akre_id']]->target_penyelesaian;
                    }
                }

                // Filter to only include akreditasi with RTL data
                $akreditasi_data = array_filter($akreditasi_data, function ($akreditasi) {
                    return !empty($akreditasi['rencana_tindak_lanjut']) || !empty($akreditasi['target_penyelesaian']);
                });
            }
        }


        // Return all collected data
        return [
            'rtm' => $this->rtm,
            'reportData' => $this->rtmReport,
            'tanggal' => !empty($this->rtmReport['tanggal_pelaksanaan']) ?
                Carbon::parse($this->rtmReport['tanggal_pelaksanaan'])->format('d F Y') :
                Carbon::now()->format('d F Y'),
            'waktu' => !empty($this->rtmReport['waktu_pelaksanaan']) ?
                Carbon::parse($this->rtmReport['waktu_pelaksanaan'])->format('H:i') :
                Carbon::now()->format('H:i'),
            'fakultas' => $this->selectedFakultas ? Fakultas::find($this->selectedFakultas)->name : 'Universitas',
            'lampiran' => $this->lampiran,
            'ami_data_by_period' => $ami_data_by_period,
            'survei_data_by_period' => $survei_data_by_period,
            'universitas_data' => $universitas_data,
            'akreditasi_data' => $akreditasi_data
        ];
    }

    /**
     * Generate a PDF file for a specific section
     *
     * @param string $view The blade view to render
     * @param string $title The title of the section (for logging)
     * @param array $data The data to pass to the view
     * @param string $orientation The paper orientation (portrait/landscape)
     * @return string The path to the saved PDF file
     */
    private function generatePdfSection($view, $title, $data, $orientation = 'portrait')
    {
        try {
            // Create storage directory if it doesn't exist
            $storage_path = 'public/rtm_pdf';
            if (!Storage::exists($storage_path)) {
                Storage::makeDirectory($storage_path);
            }

            // Generate a unique filename
            $timestamp = now()->format('YmdHis');
            $filename = "rtm_{$this->rtm->id}_{$title}_{$timestamp}.pdf";
            $filePath = "{$storage_path}/{$filename}";

            // Define PDF options
            $pdfOptions = [
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => public_path()
            ];

            // Generate the PDF
            $pdf = Pdf::loadView($view, $data)
                ->setPaper('a4', $orientation)
                ->setOptions($pdfOptions);

            // Save to storage
            Storage::put($filePath, $pdf->output());

            // Return the full storage path
            return $filePath;
        } catch (\Exception $e) {
            \Log::error("Error generating {$title}: " . $e->getMessage());
            throw new \Exception("Error generating {$title}: " . $e->getMessage());
        }
    }

    /**
     * Download the generated RTM report
     *
     * @return mixed
     */
    public function downloadDocument()
    {
        try {
            // Collect all data needed for the report
            $data = $this->collectReportData();

            // Create a new PDF merger instance
            $pdfMerger = PDFMerger::init();

            // Generate each section and save to storage
            $generatedFiles = [];

            // 1. Cover page
            $coverFile = $this->generatePdfSection('pdf.cover', 'cover', $data);
            $generatedFiles[] = $coverFile;

            // 2. Lembar Pengesahan
            $pengesahanFile = $this->generatePdfSection('pdf.lembaran_pengesahan', 'lembar_pengesahan', $data);
            $generatedFiles[] = $pengesahanFile;

            // 3. Bab 1
            $bab1File = $this->generatePdfSection('pdf.bab1', 'bab1', $data);
            $generatedFiles[] = $bab1File;

            // 4. Lampiran
            $lampiranFile = $this->generatePdfSection('pdf.lampiran', 'lampiran', $data, 'landscape');
            $generatedFiles[] = $lampiranFile;

            // Add all generated PDF files to merger
            foreach ($generatedFiles as $file) {
                $pdfMerger->addPDF(storage_path('app/' . $file), 'all');
            }

            // Add lampiran files from database if available
            foreach ($this->lampiran as $lampiran) {
                if (\Storage::exists($lampiran->file_path) && in_array($lampiran->file_type, ['pdf'])) {
                    $pdfMerger->addPDF(storage_path('app/' . $lampiran->file_path), 'all');
                }
            }

            // Generate the final merged PDF filename
            $rtmName = str_replace([' ', '/'], ['_', '_'], $this->rtm->name);
            $timestamp = Carbon::now()->format('Ymd_His');
            $finalFilename = "Laporan_RTM_{$rtmName}_{$timestamp}.pdf";
            $finalFilePath = "public/rtm_pdf/{$finalFilename}";

            // Merge all PDFs and save to storage
            $pdfMerger->merge();
            Storage::put($finalFilePath, $pdfMerger->output());

            // Set success message
            session()->flash('toastMessage', 'Laporan RTM berhasil dibuat!');
            session()->flash('toastType', 'success');

            // Return download response
            return response()->download(
                storage_path('app/' . $finalFilePath),
                "Laporan RTM {$this->rtm->name} Tahun {$this->rtm->tahun} {$data['fakultas']}.pdf"
            )->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('PDF generation error: ' . $e->getMessage());
            session()->flash('toastMessage', 'Gagal membuat PDF: ' . $e->getMessage());
            session()->flash('toastType', 'error');
            return null;
        }
    }

    public function updateSurvey()
    {
        try {
            $this->surveyData = app(SurveiService::class)->getSurvei(
                $this->rtm->survei_id,
                $this->surveyFakultas
            );

            // Merge the survey data for each parameter with the existing $dataParameter
            $surveyDataByParameter = collect($this->surveyData)->groupBy('parameter_id');
            foreach ($this->dataParameter as $key => $parameter) {
                $parameterSurveyData = $surveyDataByParameter->get($parameter['id'], collect());
                $this->dataParameter[$key]['survey_data'] = $parameterSurveyData->toArray();
            }

            session()->flash('message', 'Data survei berhasil dimuat.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memuat data survei: ' . $e->getMessage());
        }
    }

    public function gotoPage($page)
    {
        $this->page = $page;
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function nextPage()
    {
        if ($this->page < $this->getPaginatedAkreditasiProperty()->lastPage()) {
            $this->page++;
        }
    }

    protected function loadDefaultReportValues()
    {
        $fakultasName = $this->selectedFakultas ? Fakultas::find($this->selectedFakultas)->name : 'Universitas Negeri Gorontalo';

        // Default values that match the bab1.blade.php template
        $defaultAgenda = "Hasil AMI {$this->rtm->tahun}";
        $defaultAgendaKegiatan = "<ol>
<li>Pembukaan</li>
<li>Doa</li>
<li>Sambutan / arahan dari pimpinan</li>
<li>Tinjauan terhadap Hasil RTM tahun lalu</li>
<li>Pembahasan hasil audit mutu internal</li>
</ol>";

        $defaultPeserta = "<ul>
<li>Wakil Dekan</li>
<li>Ketua Jurusan</li>
<li>Sekretaris Jurusan</li>
<li>Ketua Program Studi</li>
<li>Kepala Laboratorium</li>
</ul>";

        $defaultTujuan = "<p>Rapat Tinjauan Manajmen (RTM) {$fakultasName} adalah pertemuan yang dilakukan oleh pimpinan di lingkungan {$fakultasName} secara periodik minimal 1 tahun sekali yang merupakan implementasi pelaksanaan siklus SPMI yaitu siklus Pengendalian yang bertujuan untuk mengevaluasi kinerja system secara menyeluruh.</p>

<p>Namun pada kesempatan ini RTM dilaksankan untuk menyampaian garis-garis besar hasil evaluasi pelaksanaan penjaminan mutu di {$fakultasName} dintaranya akan membahas:</p>

<ol>
<li>Hasil Audit Mutu Internal tahun {$this->rtm->tahun}</li>
<li>Hasil umpan balik dari stakeholder</li>
</ol>

<p>Permasalahan manajemen system penjaminan mutu internal untuk meninjau kinerja system manajemen mutu, dan kinerja pelayanan atau upaya {$fakultasName} guna memastikan kelanjutan, kesesuaian, kecukupan, dan efektifitas system manajemen mutu.</p>";

        $defaultHasil = "<ul>
<li>RTM dihadiri 33 peserta termasuk yang mewakili seluruh UPPS</li>
<li>Hasil kegiatan audit pada tahun {$this->rtm->tahun}</li>
</ul>";

        $defaultKesimpulan = "<ul>
<li>Seluruh hasil temuan audit dan permasalahan manajemen lainnya telah dipaparkan dan telah ditunjuk penanggungjawab untuk melaksanakan tindak lanjut</li>
<li>Setiap tindak lanjut akan dilaporkan kepada " . ($this->selectedFakultas ? 'Dekan' : 'Rektor') . "</li>
</ul>";

        $defaultPenutup = "<p>Demikian laporan RTM tahun {$this->rtm->tahun} ini dibuat untuk digunakan sebagai data dukung dokumen pelaksanaan SPMI untuk mencapai Visi Misi UNG</p>";

        // Only set default values if the field is empty
        if ($this->rtmReport == null) {
            $this->rtmReport = [
                'agenda' => $defaultAgenda,
                'agenda_kegiatan' => $defaultAgendaKegiatan,
                'peserta' => $defaultPeserta,
                'tujuan' => $defaultTujuan,
                'hasil' => $defaultHasil,
                'kesimpulan' => $defaultKesimpulan,
                'penutup' => $defaultPenutup,
                'mengetahui1_nama' => '',
                'mengetahui1_jabatan' => '',
                'mengetahui1_nip' => '',
                'mengetahui2_nama' => '',
                'mengetahui2_jabatan' => '',
                'mengetahui2_nip' => '',
                'pemimpin_rapat' => '',
                'notulis' => '',
                'tanggal_pelaksanaan' => '',
                'waktu_pelaksanaan' => '',
                'tempat_pelaksanaan' => '',
                'tahun_akademik' => '',
            ];
        }
    }

    protected function rules()
    {
        return array_merge([
            'rtmReport.mengetahui1_nama' => 'required|string',
            'rtmReport.mengetahui1_jabatan' => 'required|string',
            'rtmReport.mengetahui1_nip' => 'required|string',
            'rtmReport.mengetahui2_nama' => 'required|string',
            'rtmReport.mengetahui2_jabatan' => 'required|string',
            'rtmReport.mengetahui2_nip' => 'required|string',
            'rtmReport.pemimpin_rapat' => 'required|string',
            'rtmReport.notulis' => 'required|string',
            'rtmReport.tanggal_pelaksanaan' => 'required|date',
            'rtmReport.waktu_pelaksanaan' => 'required',
            'rtmReport.tempat_pelaksanaan' => 'required|string',
            'rtmReport.agenda' => 'required|string',
            'rtmReport.tahun_akademik' => 'required|string',
        ], $this->getRichTextEditorRules());
    }
}
