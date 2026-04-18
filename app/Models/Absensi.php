<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    public $timestamps = false;
    protected $guarded = [];

    public function sesiAbsen()
    {
        return $this->belongsTo(SesiAbsen::class, 'sesi_absen_id');
    }

}
