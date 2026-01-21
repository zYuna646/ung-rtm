<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtmLampiran extends Model
{
    use HasFactory;

    protected $table = 'rtm_lampiran';
    
    protected $fillable = [
        'rtm_id',
        'fakultas_id',
        'prodi_id',
        'judul',
        'file_path',
        'file_name',
        'file_type',
        'file_size'
    ];

    /**
     * Get the RTM that owns the lampiran
     */
    public function rtm()
    {
        return $this->belongsTo(RTM::class, 'rtm_id');
    }

    /**
     * Get the fakultas that owns the lampiran if applicable
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    /**
     * Get the prodi that owns the lampiran if applicable
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
} 