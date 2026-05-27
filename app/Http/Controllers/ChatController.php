<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    // Halaman chat
    public function index()
    {
        $messages = Auth::user()
            ->chatMessages()
            ->orderBy('created_at')
            ->get();

        return view('shared.chat', compact('messages'));
    }

    // Kirim pesan
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $user = Auth::user();

        // Simpan pesan user
        ChatMessage::create([
            'user_id' => $user->id,
            'sender'  => 'user',
            'message' => $request->message,
        ]);

        // Ambil response AI
        $botReply = $this->getAIReply($request->message);

        // Simpan pesan bot
        $botMessage = ChatMessage::create([
            'user_id' => $user->id,
            'sender'  => 'bot',
            'message' => $botReply,
        ]);

        return response()->json([
            'success' => true,
            'message' => $botReply,
        ]);
    }

    // =========================
    // AI GROQ FUNCTION
    // =========================
private function getAIReply($message)
{
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
        'Content-Type'  => 'application/json',
    ])->post('https://api.groq.com/openai/v1/chat/completions', [
        'model' => 'llama-3.1-8b-instant',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Kamu adalah asisten kesehatan ibu hamil yang ramah dan membantu.'
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'temperature' => 0.7
    ]);

    if ($response->failed()) {
        return "Maaf AI sedang tidak tersedia.";
    }

    return $response['choices'][0]['message']['content']
        ?? "Tidak ada jawaban.";
}
}
