<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlexibilityItem extends Model
{
    protected $table = 'flexibility_items';

    protected $fillable = [
        'item_name',
        'point_cost',
        'type',
        'max_late_minutes',
        'stock_limit',
    ];

    public function tokens()
    {
        return $this->hasMany(UserToken::class, 'item_id');
    }
}
