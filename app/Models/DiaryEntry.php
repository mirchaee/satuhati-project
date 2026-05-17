<?php
// app/Models/DiaryEntry.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DiaryEntry extends Model
{
    protected $fillable = [
        'user_id', 'mood', 'content',
        'water_intake', 'nutrition_status', 'entry_date',
    ];

    protected $casts = ['entry_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}