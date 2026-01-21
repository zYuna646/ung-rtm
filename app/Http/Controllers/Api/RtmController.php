<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\RTM;
use App\Models\RtmLampiran;
use App\Models\RtmRencanaTindakLanjut;
use App\Models\RtmReport;
use App\Services\AkreditasiService;
use App\Services\AmiService;
use App\Services\SurveiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RtmController extends Controller
{
    /**
     * Get list of all RTMs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rtms = RTM::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $rtms
        ]);
    }

    /**
     * Get RTM detail by ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rtm = RTM::with(['reports', 'lampiran'])->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $rtm
        ]);
    }

    /**
     * Get all fakultas
     *
     * @return \Illuminate\Http\Response
     */
    public function fakultas()
    {
        $fakultas = Fakultas::with('prodis')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $fakultas
        ]);
    }

    /**
     * Get all prodi
     *
     * @return \Illuminate\Http\Response
     */
    public function prodi()
    {
        $prodi = Prodi::with('fakultas')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $prodi
        ]);
    }

    /**
     * Get prodi by fakultas ID
     *
     * @param  int  $fakultasId
     * @return \Illuminate\Http\Response
     */
    public function prodiByFakultas($fakultasId)
    {
        $fakultas = Fakultas::findOrFail($fakultasId);
        $prodi = $fakultas->prodis;
        
        return response()->json([
            'status' => 'success',
            'data' => $prodi
        ]);
    }

    /**
     * Get RTM lampiran
     *
     * @param  int  $rtmId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lampiran($rtmId, Request $request)
    {
        $query = RtmLampiran::where('rtm_id', $rtmId);
        
        // Filter by fakultas if provided
        if ($request->has('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }
        
        // Filter by prodi if provided
        if ($request->has('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }
        
        $lampiran = $query->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $lampiran
        ]);
    }

    /**
     * Get RTM report
     *
     * @param  int  $rtmId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report($rtmId, Request $request)
    {
        $query = RtmReport::where('rtm_id', $rtmId);
        
        // Filter by fakultas if provided
        if ($request->has('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }
        
        // Filter by prodi if provided
        if ($request->has('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }
        
        $report = $query->first();
        
        return response()->json([
            'status' => 'success',
            'data' => $report
        ]);
    }

    /**
     * Get akreditasi data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function akreditasi(Request $request, AkreditasiService $akreditasiService)
    {
        $fakultasId = null;
        
        if ($request->has('fakultas_id')) {
            $fakultas = Fakultas::find($request->fakultas_id);
            if ($fakultas && !empty($fakultas->akreditasi)) {
                $fakultasId = $fakultas->akreditasi;
            }
        }
        
        $result = $akreditasiService->getAkreditas($fakultasId);
        
        if ($result && isset($result['data']) && isset($result['data']['akreditasi_expiring'])) {
            $akreditasiExpiringSoon = collect($result['data']['akreditasi_expiring']);
            
            // Filter by prodi if provided
            if ($request->has('prodi_id')) {
                $prodi = Prodi::find($request->prodi_id);
                if ($prodi) {
                    $akreditasiExpiringSoon = $akreditasiExpiringSoon->filter(function ($item) use ($prodi) {
                        return $item['akre_prodi'] == $prodi->akreditasi;
                    });
                }
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $akreditasiExpiringSoon->values()
            ]);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => []
        ]);
    }

    /**
     * Get AMI data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ami(Request $request, AmiService $amiService)
    {
        $anchor = $amiService->getAnchor();
        
        return response()->json([
            'status' => 'success',
            'data' => $anchor['data'] ?? []
        ]);
    }

    /**
     * Get survei data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function survei(Request $request, SurveiService $surveiService)
    {
        $anchor = $surveiService->getAnchor();
        
        return response()->json([
            'status' => 'success',
            'data' => $anchor['data'] ?? []
        ]);
    }
}