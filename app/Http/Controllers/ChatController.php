<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::select('chats.*', 'users.first_name')
            ->join('users', 'chats.user_id', '=', 'users.id')
            ->where('chats.landscaper_id', auth()->user()->id)
            ->groupBy('chats.user_id')
            ->get();
        return view('chat.index', compact('chats'));
    }



    public function chat_landscaper(Request $request)
    {
        $userId       = $request->user_id;
        $landscaperId = $request->landscaper_id;

        $chats = DB::table('chats')
            ->select(DB::raw('DISTINCT chats.*, users.first_name'))
            ->join('users', 'chats.landscaper_id', '=', 'users.id')
            ->where('chats.user_id', '=', $userId)
            ->where('chats.landscaper_id', '=', $landscaperId)
            ->get();

        if (is_null($chats)) {
            $message       = "";
            $user_id       = $request->input('user_id');
            $landscaper_id = $request->input('landscaper_id');

            // Save the message to the database
            $chat                = new Chat;
            $chat->message       = $message;
            $chat->user_id       = $user_id;
            $chat->landscaper_id = $landscaper_id;
            $chat->save();

            $chats = DB::table('chats')
                ->select(DB::raw('DISTINCT chats.*, users.first_name'))
                ->join('users', 'chats.landscaper_id', '=', 'users.id')
                ->where('chats.user_id', '=', $userId)
                ->where('chats.landscaper_id', '=', $landscaperId)
                ->get();
        }

        return view('chat.chat', compact('chats'));
    }

    public function store(Request $request)
    {
        // Get the message from the request
        $message       = $request->input('message');
        $user_id       = $request->input('user_id');
        $landscaper_id = $request->input('landscaper_id');
        /** @var ?\App\Models\User $user */
        $user = Auth::user();
        if ($user->userRoles()->pluck('name')->contains('Landscaper')) {
            $user_id       = $request->input('landscaper_id');
            $landscaper_id = $request->input('user_id');
        }

        // dd($request->all());

        // Save the message to the database
        $chat                = new Chat;
        $chat->message       = $message;
        $chat->user_id       = $user_id;
        $chat->landscaper_id = $landscaper_id;
        $chat->save();

        return back();
    }

    public function history($landscaper_id, $user_id)
    {
        //    dd($landscaper_id);
        // Retrieve the chat history along with the user information for the landscaper
        // $userId = auth()->user()->id; // Get the ID of the currently logged-in user
        $chats = DB::select(
            DB::raw("SELECT DISTINCT `chats`.*, `users`.`first_name` 
        FROM `chats` 
        INNER JOIN `users` ON `chats`.`landscaper_id` = `users`.`id` 
        WHERE (`chats`.`user_id` = $user_id AND `chats`.`landscaper_id` = $landscaper_id)
        OR (`chats`.`user_id` = $landscaper_id AND `chats`.`landscaper_id` = $user_id)
        ORDER BY `chats`.`created_at`")
        );
        // dd($chats);


        // Return the chat history as a JSON response
        return response()->json([
            'chats' => $chats
        ]);
    }

}