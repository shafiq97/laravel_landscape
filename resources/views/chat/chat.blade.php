@extends('layouts.app')
<style>
    .message-body {
        word-wrap: break-word;
    }

    .message-container {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .message {
        display: flex;
        flex-direction: column;
        margin: 10px;
        padding: 10px;
        max-width: 50%;
        border-radius: 5px;
        font-size: 14px;
    }

    .message.sent {
        align-self: flex-end;
        background-color: #dcf8c6;
    }

    .message.received {
        align-self: flex-start;
        background-color: #fff;
    }
</style>
@section('content')
    <div class="chat-container">
        <div class="chat-messages" id="chat-messages">
            <!-- Existing messages will be displayed here -->
        </div>
        <form class="chat-form" id="chat-form" action="{{ route('chats.store') }}" method="POST">
            @csrf
            <input type="text" name="message" id="message-input" placeholder="Type your message">
            <input type="hidden" name="user_id" value="{{ $_GET['user_id'] }}">
            <input type="hidden" name="landscaper_id" value="{{ $_GET['landscaper_id'] }}">
            <button type="submit" id="send-btn">Send</button>
        </form>
    </div>

    <script>
        // Get the form, input field, and chat messages div
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const chatMessages = document.getElementById('chat-messages');
        const sendBtn = document.getElementById('send-btn');

        // Load the chat history
        // Fetch chat history
        fetch('/chat/history', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Get logged-in user ID
                const userId = {{ auth()->user()->id }}; // <-- get the currently logged-in user ID
                // Display chat history in chat messages div
                data.chats.forEach(message => {
                    console.log(message.user_id);
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('message');
                    messageDiv.classList.add(message.user_id === userId ? 'sent' : 'received');
                    messageDiv.innerHTML = `
            <div class="message-header">
                <strong>${message.user_id == userId ? 'You' : message.first_name}</strong>
                <span class="timestamp">${new Date(message.created_at).toLocaleTimeString()}</span>
            </div>
            <div class="message-body">
                ${message.message}
            </div>
        `;
                    chatMessages.appendChild(messageDiv);
                });
            })
            .catch(error => {
                console.error('Error fetching chat history:', error);
            });

        // Listen for the form submit event
        chatForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent the form from submitting normally

            const message = messageInput.value; // Get the message from the input field
            const urlParams = new URLSearchParams(window.location.search);
            const user_id = urlParams.get('user_id');
            const landscaper_id = urlParams.get('landscaper_id');
            // Create a new message element and append it to the chat messages div
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message');
            messageDiv.innerHTML = `
          <div class="message-header">
              <strong>You</strong>
              <span class="timestamp">${new Date().toLocaleTimeString()}</span>
          </div>
          <div class="message-body">
              ${message}
          </div>
      `;
            chatMessages.appendChild(messageDiv);

            // Clear the input field
            messageInput.value = '';
            const body = {
                message: message,
                user_id: user_id,
                landscaper_id: landscaper_id
            };

            // Send an AJAX request to save the message to the database
            fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify(body)
            });
        });

        // Listen for the send button click event
        sendBtn.addEventListener('click', () => {
            chatForm.submit();
        });
    </script>
@endsection
