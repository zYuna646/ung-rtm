<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\RTM;
use App\Models\RtmRencanaTindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RencanaTindakLanjutController extends Controller
{
    /**
     * Get rencana tindak lanjut for RTM
     *
     * @param  int  $rtmId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index($rtmId, Request $request)
    {
        $query = RtmRencanaTindakLanjut::where('rtm_id', $rtmId);
        
        // Filter by type (ami, survei, akreditasi)
        if ($request->has('type')) {
            switch ($request->type) {
                case 'ami':
                    $query->whereNotNull('ami_id');
                    break;
                case 'survei':
                    $query->whereNotNull('survei_id');
                    break;
                case 'akreditasi':
                    $query->whereNotNull('akreditasi_id');
                    break;
            }
        }
        
        // Filter by fakultas if provided
        if ($request->has('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }
        
        // Filter by prodi if provided
        if ($request->has('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }
        
        $rencanaTindakLanjut = $query->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $rencanaTindakLanjut
        ]);
    }

    /**
     * Store a newly created rencana tindak lanjut
     *
     * @param  int  $rtmId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($rtmId, Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'rencana_tindak_lanjut' => 'required|string',
            'target_penyelesaian' => 'required|string',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'prodi_id' => 'nullable|exists:prodis,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Check if RTM exists
        $rtm = RTM::findOrFail($rtmId);
        
        // Determine which type of ID to set
        $amiId = null;
        $surveiId = null;
        $akreditasiId = null;
        
        if ($request->has('ami_id')) {
            $amiId = $request->ami_id;
        } elseif ($request->has('survei_id')) {
            $surveiId = $request->survei_id;
        } elseif ($request->has('akreditasi_id')) {
            $akreditasiId = $request->akreditasi_id;
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Harus menyediakan salah satu dari ami_id, survei_id, atau akreditasi_id'
            ], 422);
        }
        
        // Create or update rencana tindak lanjut
        $rencanaTindakLanjut = RtmRencanaTindakLanjut::updateOrCreate(
            [
                'rtm_id' => $rtmId,
                'ami_id' => $amiId,
                'survei_id' => $surveiId,
                'akreditasi_id' => $akreditasiId,
                'fakultas_id' => $request->fakultas_id,
                'prodi_id' => $request->prodi_id,
            ],
            [
                'rencana_tindak_lanjut' => $request->rencana_tindak_lanjut,
                'target_penyelesaian' => $request->target_penyelesaian,
            ]
        );
        
        return response()->json([
            'status' => 'success',
            'message' => 'Rencana tindak lanjut berhasil disimpan',
            'data' => $rencanaTindakLanjut
        ]);
    }

    /**
     * Display the specified rencana tindak lanjut
     *
     * @param  int  $rtmId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($rtmId, $id)
    {
        $rencanaTindakLanjut = RtmRencanaTindakLanjut::where('rtm_id', $rtmId)
            ->where('id', $id)
            ->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'data' => $rencanaTindakLanjut
        ]);
    }

    /**
     * Update the specified rencana tindak lanjut
     *
     * @param  int  $rtmId
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($rtmId, $id, Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'rencana_tindak_lanjut' => 'required|string',
            'target_penyelesaian' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Find the rencana tindak lanjut
        $rencanaTindakLanjut = RtmRencanaTindakLanjut::where('rtm_id', $rtmId)
            ->where('id', $id)
            ->firstOrFail();
        
        // Update the rencana tindak lanjut
        $rencanaTindakLanjut->update([
            'rencana_tindak_lanjut' => $request->rencana_tindak_lanjut,
            'target_penyelesaian' => $request->target_penyelesaian,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Rencana tindak lanjut berhasil diperbarui',
            'data' => $rencanaTindakLanjut
        ]);
    }

    /**
     * Remove the specified rencana tindak lanjut
     *
     * @param  int  $rtmId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($rtmId, $id)
    {
        // Find the rencana tindak lanjut
        $rencanaTindakLanjut = RtmRencanaTindakLanjut::where('rtm_id', $rtmId)
            ->where('id', $id)
            ->firstOrFail();
        
        // Delete the rencana tindak lanjut
        $rencanaTindakLanjut->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Rencana tindak lanjut berhasil dihapus'
        ]);
    }
}