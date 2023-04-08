<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Service $service)
    {
        // Your chat logic goes here
        return view('chat.index', compact('service'));
    }
}
