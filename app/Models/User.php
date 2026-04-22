<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nisn',
        'nip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // wali kelas (guru)
    public function waliKelas()
    {
        return $this->hasMany(Kelas::class, 'wali_guru_id');
    }

    // anggota kelas (murid)
    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'murid_id');
    }

    // jadwal ngajar (guru)
    public function jadwalMengajar()
    {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }

    // absensi (murid)
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'murid_id');
    }

    public function guruMapel()
    {
        return $this->hasMany(GuruMapel::class, 'guru_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function pointLedgers()
    {
        return $this->hasMany(PointLedger::class);
    }

    public function tokens()
    {
        return $this->hasMany(UserToken::class);
    }

    public function kelasMurid()
    {
        return $this->belongsToMany(
            Kelas::class,
            'anggota_kelas',
            'murid_id',
            'kelas_id'
        );
    }
}
