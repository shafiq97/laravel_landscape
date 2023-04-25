@extends('layouts.app')
<style>
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        /* height: 100vh; */
    }

    .chat-container {
        display: flex;
        flex-direction: column;
        max-width: 500px;
        width: 100%;
        margin: 0 auto;
        height: 100%;
    }

    .chat-form {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 20px;
        gap: 10px;
    }

    .chat-form input[type="text"] {
        width: 100%;
    }

    .message-body {
        word-wrap: break-word;
    }

    .chat-messages {
        overflow-y: auto;
        height: 500px;
        max-height: 500px;
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
    <div class="container">
        <div class="chat-container">
            <h1></h1>
            <div class="chat-messages" id="chat-messages">
                <!-- Existing messages will be displayed here -->
            </div>
            {{-- TODO: center text box --}}
            <form class="chat-form mt-3" id="chat-form" action="{{ route('chats.store') }}" method="POST">
                @csrf
                <input class="" type="text" name="message" id="message-input" placeholder="Type your message">
                <input type="hidden" name="user_id" value="{{ $_GET['user_id'] }}">
                <input type="hidden" name="landscaper_id" value="{{ $_GET['landscaper_id'] }}">
                <button class="btn btn-success" type="submit" id="send-btn">Send</button>
            </form>
        </div>
    </div>


    <script>
        // Get the form, input field, and chat messages div
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const chatMessages = document.getElementById('chat-messages');
        const sendBtn = document.getElementById('send-btn');
        const params = new URLSearchParams(window.location.search);
        const landscaperId = params.get('landscaper_id');
        const userId = params.get('user_id');
        const userName = params.get('user_name');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Update the heading element to display the name
        const heading = document.querySelector('h1');
        heading.textContent = `Chat with ${userName}`;
        // Load the chat history
        // Fetch chat history
        fetch(`/chat/history/${landscaperId}/${userId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Get logged-in user ID
                const userId = {{ auth()->user()->id }}; // <-- get the currently logged-in user ID
                console.log(userId);
                console.log(data);
                // Display chat history in chat messages div
                data.chats.forEach(message => {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    console.log(message.user_id);
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('message');
                    messageDiv.classList.add(message.user_id === userId ? 'sent' : 'received');
                    messageDiv.innerHTML = `
            <div class="message-header">
                <strong>${message.user_id == userId ? 'You' : ''}</strong>
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
            chatMessages.scrollTop = chatMessages.scrollHeight;

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
                })
                .then(response => {
                    if (response.ok) {
                        // Auto-scroll to the bottom of the chat messages div
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    } else {
                        throw new Error('Network response was not ok.');
                    }
                })
                .catch(error => {
                    console.error('Error sending chat message:', error);
                });
        });


        // Listen for the send button click event
        sendBtn.addEventListener('click', () => {
            chatForm.submit();
        });

        setInterval(() => {
            window.location.reload();
        }, 30000); // Reload every 10 seconds (10000 milliseconds)
    </script>
@endsection
