<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Halaman chat
    // TODO Anggota 5: buat view shared.chat
    public function index()
    {
        $messages = Auth::user()
                        ->chatMessages()
                        ->orderBy('created_at')
                        ->get();

        return view('shared.chat', compact('messages'));
    }

    // Kirim pesan & dapat balasan bot
    // TODO Anggota 5: integrasikan dengan AI/NLP service
    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $user = Auth::user();

        // Simpan pesan user
        ChatMessage::create([
            'user_id' => $user->id,
            'sender'  => 'user',
            'message' => $request->message,
        ]);

        // TODO Anggota 5: ganti dengan AI response
        $botReply = 'Terima kasih pesannya. Fitur chatbot sedang dalam pengembangan.';

        $botMessage = ChatMessage::create([
            'user_id' => $user->id,
            'sender'  => 'bot',
            'message' => $botReply,
        ]);

        return response()->json([
            'success' => true,
            'message' => $botMessage->message,
        ]);
    }
}