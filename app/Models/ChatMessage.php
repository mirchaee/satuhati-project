<?php
// app/Models/ChatMessage.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id', 'sender', 'message', 'quick_replies',
    ];

    protected $casts = ['quick_replies' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}