$(document).ready(function () {
    // Check if username is set in local storage
    let username = localStorage.getItem('username');

    if (username) {
        // If username is already set, show the chat container
        showChatContainer(username);

        // Set up periodic chat log updates
        setInterval(function () {
            loadChat();
        }, 5000); // Adjust the interval as needed (e.g., 5000 milliseconds for every 5 seconds)
    } else {
        // If username is not set, show the username input container
        showUsernameContainer();
    }

    // Submit username form
    $('#username-form').submit(function (e) {
        e.preventDefault();
        setUsername();
    });

    // Submit chat form
    $('#chat-form').submit(function (e) {
        e.preventDefault();
        sendMessage(username);
    });

    function showUsernameContainer() {
        // Load rules from the server before showing the username input container
        $.ajax({
            url: 'rules.html',
            method: 'GET',
            success: function (rules) {
                // Display rules in an iframe
                $('#rules-frame').attr('srcdoc', rules);

                // After displaying rules, show the username input container
                $('#username-container').show();
                $('#chat-container').hide();
            }
        });
    }

    function showChatContainer(username) {
        $('#username-container').hide();
        $('#chat-container').show();
        loadChat();
        $('#message').focus();
    }

    function setUsername() {
        let username = $('#username').val();

        if (username.trim() !== '') {
            localStorage.setItem('username', username);
            showChatContainer(username);

            // Set up periodic chat log updates
            setInterval(function () {
                loadChat();
            }, 5000); // Adjust the interval as needed (e.g., 5000 milliseconds for every 5 seconds)
        }
    }

    function loadChat() {
        // Load chat log from the server (you need to implement this)
        $.ajax({
            url: 'chatlog.html',
            method: 'GET',
            success: function (response) {
                $('#chat-box').html(response);
                scrollToBottom();
            }
        });
    }

    function sendMessage(username) {
    let message = $('#message').val();
    let attachment = $('#attachment')[0].files[0];

    // Limit message length to 256 characters
    if (message.length > 256) {
        alert('Message exceeds the maximum limit of 256 characters.');
        return;
    }

    if (message.trim() !== '' || attachment) {
        let formData = new FormData();
        formData.append('username', username);
        formData.append('message', message);
        formData.append('attachment', attachment);

        $.ajax({
            url: 'chat.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                loadChat();
                $('#message').val('');
                $('#attachment').val('');
            }
        });
    }
}


    function scrollToBottom() {
        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    }
});