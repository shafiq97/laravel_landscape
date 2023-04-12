<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        // Your chat logic goes here
        return view('chat.index');
    }

    public function store(Request $request)
    {
        // Get the message from the request
        $message = $request->input('message');

        // Save the message to the database
        $chat          = new Chat;
        $chat->message = $message;
        $chat->user_id = 11;
        $chat->save();

        return back();
    }

    public function history()
    {
        // Retrieve the chat history from the database
        $chats = Chat::all()->toArray();
        // Return the chat history as a JSON response
        return response()->json([
            'chats' => $chats
        ]);
    }
}