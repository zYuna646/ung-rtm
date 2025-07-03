<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'fakultas_id',
        'ami',
        'survei',
        'akreditasi',
    ];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }
}
