<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::select('chats.*', 'users.first_name')
            ->join('users', 'chats.landscaper_id', '=', 'users.id')
            ->where('chats.user_id', auth()->user()->id)
            ->groupBy('chats.landscaper_id')
            ->get();

        return view('chat.index', compact('chats'));
    }

    public function chat_landscaper()
    {
        $chats = Chat::select('chats.*', 'users.first_name')
            ->join('users', 'chats.landscaper_id', '=', 'users.id')
            ->where('chats.user_id', auth()->user()->id)
            ->distinct('chats.landscaper_id')
            ->get();
        return view('chat.chat', compact('chats'));
    }

    public function store(Request $request)
    {
        // Get the message from the request
        $message       = $request->input('message');
        $user_id       = $request->input('user_id');
        $landscaper_id = $request->input('landscaper_id');

        // dd($request->all());

        // Save the message to the database
        $chat                = new Chat;
        $chat->message       = $message;
        $chat->user_id       = $user_id;
        $chat->landscaper_id = $landscaper_id;
        $chat->save();

        return back();
    }

    public function history()
    {
        // Retrieve the chat history along with the user information for the landscaper
        $chats = DB::table('chats')
            ->join('users', 'chats.user_id', '=', 'users.id')
            ->select('chats.*', 'users.first_name')
            ->get()
            ->toArray();

        // Return the chat history as a JSON response
        return response()->json([
            'chats' => $chats
        ]);
    }

}