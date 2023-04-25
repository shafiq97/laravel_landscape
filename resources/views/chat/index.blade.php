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
                            @php
                                $loggedInUser = \Illuminate\Support\Facades\Auth::user();
                                $role = $loggedInUser->userRoles->pluck('name')->toArray();
                            @endphp
                            @if ($role[0] == 'Landscaper')
                                <td><a href="{{ route('chat.landscaper', ['user_id' => $chat->user_id, 'landscaper_id' => $chat->landscaper_id, 'user_name' => $chat->first_name]) }}"
                                        class="btn btn-warning">Chat</a></td>
                            @else
                                <td><a href="{{ route('chat.landscaper', ['user_id' => $chat->landscaper_id, 'landscaper_id' => $chat->user_id, 'user_name' => $chat->first_name]) }}"
                                        class="btn btn-warning">Chat</a></td>
                            @endif
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        {{-- TODO: ONly render the table if current user role is use --}}
        @php
            $loggedInUser = \Illuminate\Support\Facades\Auth::user();
            $role = $loggedInUser->userRoles->pluck('name')->toArray();
        @endphp
        @if ($role[0] == 'User')
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
        @endif
    </div>
@endsection
