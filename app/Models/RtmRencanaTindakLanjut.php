<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtmRencanaTindakLanjut extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rtm_rencana_tindak_lanjut';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rencana_tindak_lanjut',
        'target_penyelesaian',
        'ami_id',
        'survei_id',
        'akreditasi_id',
        'rtm_id',
        'fakultas_id',
    ];

    /**
     * Get the RTM that owns this rencana tindak lanjut.
     */
    public function rtm()
    {
        return $this->belongsTo(RTM::class, 'rtm_id');
    }

    /**
     * Get the fakultas that owns this rencana tindak lanjut (if applicable).
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }
} 