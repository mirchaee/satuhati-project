<?php
// app/Models/EducationalContent.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EducationalContent extends Model
{
    protected $fillable = [
        'title', 'content', 'category',
        'week_start', 'week_end', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    // Ambil konten yang relevan untuk minggu tertentu
    public function scopeForWeek($query, int $week)
    {
        return $query->where('week_start', '<=', $week)
                     ->where('week_end', '>=', $week)
                     ->where('is_active', true);
    }
}