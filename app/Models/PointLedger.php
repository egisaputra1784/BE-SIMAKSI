<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointLedger extends Model
{
    protected $table = 'point_ledgers';

    protected $fillable = [
        'user_id',
        'transaction_type',
        'amount',
        'current_balance',
        'description',
        'absensi_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }
}
