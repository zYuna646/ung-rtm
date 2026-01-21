<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RTM extends Model
{
    use HasFactory;

    protected $table = 'rtms';

    protected $fillable = [
        'name',
        'tahun',
        'ami_anchor',
        'survei_anchor',
        // 'akreditas_anchor',
        // 'is_temuan',
    ];

    protected $casts = [
        'ami_anchor' => 'array',
        'survei_anchor' => 'array',
        'akreditas_anchor' => 'array',
        'is_temuan' => 'boolean',
    ];
    
    /**
     * Get all reports for this RTM
     */
    public function reports()
    {
        return $this->hasMany(RtmReport::class, 'rtm_id');
    }
    
    /**
     * Get all lampiran for this RTM
     */
    public function lampiran()
    {
        return $this->hasMany(RtmLampiran::class, 'rtm_id');
    }
}
