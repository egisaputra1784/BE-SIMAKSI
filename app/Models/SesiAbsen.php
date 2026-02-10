<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiAbsen extends Model
{
    protected $table = 'sesi_absen';
    public $timestamps = false;
    protected $guarded = [];

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'sesi_absen_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

}
