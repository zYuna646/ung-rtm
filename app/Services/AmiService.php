<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Stmt\TryCatch;
use function PHPUnit\Framework\returnArgument;

class AmiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('api.AMI');
    }

    public function getAllFaculty()
    {
        $response = Http::get($this->baseUrl . 'fakultas');

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getAmi($id, $fakultasId)
    {
        $response = Http::get($this->baseUrl . 'ami/' . $id . '/' . $fakultasId);
        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getAnchor()
    {
        try {
            $response = Http::get($this->baseUrl . 'periodes');
            return $response->json();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to fetch data', 'message' => $th->getMessage()], 500);
        }
    }

    public function getDetail($id)
    {
        try {
            $response = Http::get($this->baseUrl . 'periodes/' . $id);
            return $response->json();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to fetch data', 'message' => $th->getMessage()], 500);
        }
    }

    public function getAllDepartement()
    {
        $response = Http::get($this->baseUrl . 'departements');

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getProgram()
    {
        $response = Http::get($this->baseUrl . 'programs');
        if ($response->successful()) {
            return $response->json();
        }
    }

    public function getAllProdi()
    {
        try {
            $response = Http::get($this->baseUrl . 'programs');
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Throwable $th) {
            return null;
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
}
