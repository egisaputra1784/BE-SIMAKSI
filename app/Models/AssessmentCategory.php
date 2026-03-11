<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentCategory extends Model
{
    use HasFactory;

    protected $table = 'assessment_categories';

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    public function details()
    {
        return $this->hasMany(AssessmentDetail::class, 'category_id');
    }
}
