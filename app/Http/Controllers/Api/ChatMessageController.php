<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    // Send a message in a session
    public function store(Request $request)
    {
        $request->validate([
            'chat_session_id' => 'required|exists:chat_sessions,id',
            'message' => 'required|string',
        ]);

        // Ensure session belongs to user
        ChatSession::where('id', $request->chat_session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Save user message
        ChatMessage::create([
            'chat_session_id' => $request->chat_session_id,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        // Simple chatbot reply
        $botReply = "This is a general legal information response.";

        ChatMessage::create([
            'chat_session_id' => $request->chat_session_id,
            'sender' => 'bot',
            'message' => $botReply,
        ]);

        return response()->json(['reply' => $botReply]);
    }

    // Get messages for a session
    public function index($sessionId)
    {
        return ChatMessage::where('chat_session_id', $sessionId)
            ->orderBy('created_at')
            ->get();
    }
}
