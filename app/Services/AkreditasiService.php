<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AkreditasiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('api.AKREDITAS');
    }

    public function getAllFaculty()
    {
        try {
            $response = Http::get($this->baseUrl . 'fakultas');
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getAllProdi()
    {
        try {
            $response = Http::get($this->baseUrl . 'prodi');
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getFakultasProdiUniv($fakultasId = null)
    {
        try {
            $url = $this->baseUrl . 'data';
            
            if ($fakultasId) {
                $url .= '/' . $fakultasId;
            }
            
            $response = Http::get($url);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getAkreditas($fakultasId = null)
    {
        try {
            $url = $this->baseUrl . 'dashboard';
            
            if ($fakultasId) {
                $url .= '/' . $fakultasId;
            }
            
            $response = Http::get($url);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Throwable $th) {
            return null;
        }
    }
} 