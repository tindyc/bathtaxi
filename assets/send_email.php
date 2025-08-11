<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "contact@acetoursandtransfers.co.uk"; 
    $subject = "New Booking Request from Website";

    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $number = htmlspecialchars($_POST["number"]);
    $pickup = htmlspecialchars($_POST["pickup"]);
    $destination = htmlspecialchars($_POST["destination"]);
    $message = htmlspecialchars($_POST["message"]);

    $body = "You have received a new booking request:\n\n";
    $body .= "Name: $name\n";
    $body .= "Email: $email\n";
    $body .= "Phone: $number\n";
    $body .= "Pickup: $pickup\n";
    $body .= "Destination: $destination\n";
    $body .= "Message:\n$message\n";

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
