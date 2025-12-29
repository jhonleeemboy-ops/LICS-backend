<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatSessionController extends Controller
{
    // Start new chat session
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:legal_categories,id',
        ]);

        $session = ChatSession::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'started_at' => now(),
        ]);

        return response()->json($session, 201);
    }

    // List user chat sessions
    public function index()
    {
        return ChatSession::where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    // End a chat session
    public function end($id)
    {
        $session = ChatSession::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $session->update([
            'ended_at' => now(),
        ]);

        return response()->json(['message' => 'Chat session ended']);
    }
}
