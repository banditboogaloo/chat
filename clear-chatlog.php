<?php
// File path to the chat log file
$chatlogFilePath = 'chatlog.html';

// Check if the password is provided in the POST request
if (isset($_POST['password']) && $_POST['password'] === 'admin123') {
    // Open the chat log file in write mode to clear its content
    file_put_contents($chatlogFilePath, '');

    // Respond with a success message
    echo 'Chat log has been cleared.';
} else {
    // Respond with an error message if the password is incorrect or not provided
    http_response_code(403);
    echo 'Incorrect or missing password.';
}
?>
