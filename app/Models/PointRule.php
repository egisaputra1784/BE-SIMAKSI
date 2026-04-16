<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    protected $table = 'point_rules';

    protected $fillable = [
        'rule_name',
        'target_role',
        'condition_type',
        'min_value',
        'max_value',
        'point_modifier',
    ];
}
