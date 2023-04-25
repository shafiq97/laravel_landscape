@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>My Chats</h1>
        <table class="table" style="margin-bottom: 50px">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Date</th>
                    <th>Chat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chats as $chat)
                    @if (true)
                        <tr>
                            <td>{{ $chat->first_name }}</td>
                            <td>{{ $chat->created_at->format('d/m/Y H:i') }}</td>
                            <td><a href="{{ route('chat.landscaper', ['user_id' => $chat->user_id, 'landscaper_id' => $chat->landscaper_id, 'user_name' => $chat->first_name]) }}"
                                    class="btn btn-warning">Chat</a></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <h1>Pending Landscaper Reply</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Date</th>
                    <th>Chat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pending_chats as $chat)
                    @if (true)
                        <tr>
                            <td>{{ $chat->first_name }}</td>
                            <td>{{ $chat->created_at->format('d/m/Y H:i') }}</td>
                            <td><a href="{{ route('chat.landscaper', ['user_id' => $chat->user_id, 'landscaper_id' => $chat->landscaper_id, 'user_name' => $chat->first_name]) }}"
                                    class="btn btn-warning">Chat</a></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
