@extends('layouts.app')

@section('content')
    <div class="chat-container">
        <div class="chat-messages" id="chat-messages">
            <!-- Existing messages will be displayed here -->
        </div>
        <form class="chat-form" id="chat-form" action="#">
            <input type="text" name="message" id="message-input" placeholder="Type your message">
            <button id="send-btn">Send</button>
        </form>
    </div>

    <script>
        // Get the form, input field, and chat messages div
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const chatMessages = document.getElementById('chat-messages');

        // Listen for the form submit event
        chatForm.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent the form from submitting normally

            const message = messageInput.value; // Get the message from the input field

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
        });
    </script>
@endsection
