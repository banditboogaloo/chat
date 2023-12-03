$(document).ready(function () {
    // Handle the "Submit" button click
    $('#submit-button').click(function () {
        submitAdminPanel();
    });
});

function submitAdminPanel() {
    const clearChatlog = $('#clear-chatlog').prop('checked');
    const enteredPassword = $('#admin-password').val();

    // Add your password verification logic here
    // For simplicity, let's assume the correct password is 'admin123'
    if (enteredPassword === 'admin123') {
        // Password is correct, you can now perform admin actions
        if (clearChatlog) {
            clearChatlogAction(enteredPassword);
        } else {
            alert('No admin action selected.');
        }
    } else {
        alert('Incorrect admin password!');
    }
}

function clearChatlogAction(password) {
    // Make a POST request to clear-chatlog.php with the entered password
    $.ajax({
        url: 'clear-chatlog.php',
        method: 'POST',
        data: { password: password },
        success: function (response) {
            alert(response);
        },
        error: function () {
            alert('Failed to clear chat log.');
        }
    });
}
