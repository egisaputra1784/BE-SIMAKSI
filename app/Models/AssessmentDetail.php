<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentDetail extends Model
{
    use HasFactory;

    protected $table = 'assessment_details';

    protected $fillable = [
        'assessment_id',
        'category_id',
        'score'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }
}
