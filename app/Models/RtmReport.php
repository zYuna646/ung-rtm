<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtmReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'rtm_id',
        'fakultas_id',
        'mengetahui1_nama',
        'mengetahui1_jabatan',
        'mengetahui1_nip',
        'mengetahui2_nama',
        'mengetahui2_jabatan',
        'mengetahui2_nip',
        'pemimpin_rapat',
        'notulis',
        'tanggal_pelaksanaan',
        'waktu_pelaksanaan',
        'tempat_pelaksanaan',
        'agenda',
        'agenda_kegiatan',
        'peserta',
        'tahun_akademik',
        'tujuan',
        'hasil',
        'kesimpulan',
        'penutup'
    ];

    /**
     * Get the RTM that this report belongs to
     */
    public function rtm()
    {
        return $this->belongsTo(RTM::class, 'rtm_id');
    }

    /**
     * Get the fakultas that this report belongs to (if applicable)
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }
}
