<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the posted data
    $username = isset($_POST["username"]) ? htmlspecialchars($_POST["username"]) : "";
    $message = isset($_POST["message"]) ? htmlspecialchars($_POST["message"]) : "";
    $attachment = isset($_FILES["attachment"]) ? $_FILES["attachment"] : null;

    // Format the message with timestamp
    $formattedMessage = '<div>' . date("H:i:s") . ' ' . $username . ': ' . str_replace(array("\r", "\n"), '', $message) . '</div>';

    if ($attachment && $attachment['size'] > 0) {
        // Check if the message is an image, video, audio, or a file
        if (isImage($attachment)) {
            $randomNumber = mt_rand(); // Generate a random number
            $filename = $randomNumber . '_' . $attachment['name']; // Concatenate with the original filename
            $formattedMessage .= '<img src="' . saveImage($attachment, $filename) . '" alt="Image">';
        } elseif (isVideo($attachment)) {
            $videoSource = saveFile($attachment);
            $formattedMessage .= '<video width="320" height="240" controls>';
            $formattedMessage .= '<source src="' . $videoSource . '" type="' . $attachment['type'] . '">';
            $formattedMessage .= 'Your browser does not support the video tag.';
            $formattedMessage .= '</video>';
        } elseif (isAudio($attachment)) {
            $audioSource = saveFile($attachment);
            $formattedMessage .= '<audio controls>';
            $formattedMessage .= '<source src="' . $audioSource . '" type="' . $attachment['type'] . '">';
            $formattedMessage .= 'Your browser does not support the audio tag.';
            $formattedMessage .= '</audio>';
        } else {
            // Assume the message is a regular file
            $formattedMessage .= 'uploaded a file: <a href="' . saveFile($attachment) . '" download>' . $attachment['name'] . '</a>';
        }
    }

    // Path to the chat log file
    $chatlogFilePath = 'chatlog.html';

    // Open the chat log file in append mode
    $file = fopen($chatlogFilePath, 'a');

    // Write the formatted message to the file
    fwrite($file, $formattedMessage . PHP_EOL);

    // Close the file
    fclose($file);

    // Respond with a success message
    echo 'Message posted successfully.';
} else {
    // Respond with an error message if the request method is not POST
    http_response_code(400);
    echo 'Invalid request method.';
}

function isImage($file)
{
    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
    return in_array($file['type'], $allowedImageTypes);
}

function isVideo($file)
{
    $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
    return in_array($file['type'], $allowedVideoTypes);
}

function isAudio($file)
{
    $allowedAudioTypes = ['audio/mpeg', 'audio/ogg', 'audio/wav'];
    return in_array($file['type'], $allowedAudioTypes);
}

function saveImage($file, $filename)
{
    // Specify the directory to save images
    $uploadsDirectory = 'uploads/images/';
    $targetPath = $uploadsDirectory . $filename;

    // Resize the image to a maximum of 1024x1024 pixels
    list($width, $height) = getimagesize($file['tmp_name']);
    $aspectRatio = $width / $height;

    $newWidth = min($width, 1024);
    $newHeight = $newWidth / $aspectRatio;

    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    $sourceImage = imagecreatefromstring(file_get_contents($file['tmp_name']));
    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save the resized image
    imagejpeg($resizedImage, $targetPath, 85);

    // Free up memory
    imagedestroy($resizedImage);
    imagedestroy($sourceImage);

    return $targetPath;
}

function saveFile($file)
{
    $uploadsDirectory = 'uploads/files/';
    $targetPath = $uploadsDirectory . $file['name'];
    move_uploaded_file($file['tmp_name'], $targetPath);

    return $targetPath;
}

?>
