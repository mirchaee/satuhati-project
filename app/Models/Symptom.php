<?php
// app/Models/Symptom.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    protected $fillable = [
        'name', 'category', 'weight', 'is_critical',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
    ];

    public function assessments()
    {
        return $this->belongsToMany(
            HealthAssessment::class,
            'assessment_symptoms',
            'symptom_id',
            'assessment_id'
        );
    }
}
