<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use PhpParser\Node\Stmt\TryCatch;

class SurveiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('api.SUVEI');
    }

    public function getAllFaculty()
    {
        $response = Http::get($this->baseUrl . 'fakultas');

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getAllDepartement()
    {
        $response = Http::get($this->baseUrl . 'departements');

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getAllProdi()
    {
        $response = Http::get($this->baseUrl . 'prodi');

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getAnchor()
    {
        try {
            $response = Http::get($this->baseUrl . 'survey');
            return $response->json();
        } catch (\Throwable $th) {
            return [
                'data' => [],
                'error' => 'Failed to fetch data',
                'message' => $th->getMessage(),
            ];
        }
    }

    public function getSurveyDetail($id, $fakultas, $prodi, $total_item = 0)
    {
        try {
            $queryParams = [];

            if ($prodi) {
                $queryParams['prodi_id'] = $prodi;
            }
            if ($fakultas) {
                $queryParams['fakultas_id'] = $fakultas;
            }

            $queryParams['total_item'] = $total_item;

            $response = Http::get($this->baseUrl . 'survey/'. $id.'/detail', $queryParams);
            return $response->json();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to fetch data', 'message' => $th->getMessage()], 500);
        }
    }

    // public function getAllPeriode()
    // {
    //     $response = Http::get($this->baseUrl . 'periodes');

    //     if ($response->successful()) {
    //         return $response->json();
    //     }
    //     return null;  
    // }

    // public function getAllProdi()
    // {
    //     $response = Http::get($this->baseUrl . 'prodi');

    //     if ($response->successful()) {
    //         return $response->json();
    //     }
    //     return null;  
    // }

    public function getSurvei($id, $fakultasId, $totalItem = 5)
    {
        $response = Http::get(
            $this->baseUrl . 'survei/' . $id . '/' . $fakultasId,
            ['total_item' => $totalItem]
        );
        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getSurveiProdi($id, $prodiId, $totalItem = 5)
    {
        $response = Http::get(
            $this->baseUrl . 'survei/' . $id . '/prodi/' . $prodiId,
            ['total_item' => $totalItem]
        );
        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
