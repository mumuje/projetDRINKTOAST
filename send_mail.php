<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = $_POST['pseudo'];
    $message = $_POST['message'];

    // Formatage du message
    $formattedMessage = "Pseudo: " . $pseudo . "\nMessage: " . $message . "\n\n";

    // Écriture du message dans le fichier
    file_put_contents('messages.txt', $formattedMessage, FILE_APPEND);
}
?>