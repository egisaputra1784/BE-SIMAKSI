<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $guarded = [];

    public function tahunAjar()
    {
        return $this->belongsTo(TahunAjar::class);
    }

    public function wali()
    {
        return $this->belongsTo(User::class, 'wali_guru_id');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaKelas::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
