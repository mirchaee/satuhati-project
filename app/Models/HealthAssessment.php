<?php
// app/Models/HealthAssessment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthAssessment extends Model
{
    protected $fillable = [
        'user_id', 'mood_status', 'notes',
        'pain_scale', 'weight_kg', 'blood_pressure',
        'fetal_heart_rate', 'risk_level', 'risk_score',
        'is_synced', 'is_emergency',
    ];

    protected $casts = [
        'is_synced'    => 'boolean',
        'is_emergency' => 'boolean',
    ];

    // ════════════════════════════════════════
    //  RELATIONSHIPS
    // ════════════════════════════════════════

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Gejala yang terpilih dalam assessment ini
    public function symptoms()
    {
        return $this->belongsToMany(
            Symptom::class,
            'assessment_symptoms',
            'assessment_id',
            'symptom_id'
        );
    }

    // ════════════════════════════════════════
    //  HELPER
    // ════════════════════════════════════════

    // Class CSS untuk badge risiko di UI
    public function getRiskBadgeClass(): string
    {
        return match($this->risk_level) {
            'Aman'    => 'bg-green-100 text-green-700 border-green-200',
            'Waspada' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'Bahaya'  => 'bg-red-100 text-red-700 border-red-200',
            default   => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    // Emoji untuk mood
    public function getMoodEmoji(): string
    {
        return match($this->mood_status) {
            'Senang'  => '😊',
            'Baik'    => '🙂',
            'Biasa'   => '😐',
            'Cemas'   => '😰',
            'Lelah'   => '😴',
            'Sedih'   => '😢',
            default   => '😶',
        };
    }
}