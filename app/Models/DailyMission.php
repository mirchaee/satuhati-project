<?php
// app/Models/DailyMission.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyMission extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description',
        'is_completed', 'mission_date', 'target_week',
        'completed_at', 
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'mission_date' => 'date',
        'completed_at' => 'datetime', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}