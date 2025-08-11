<?php
// send_email.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo 'error';
  exit;
}

function field($k){ return isset($_POST[$k]) ? trim(strip_tags($_POST[$k])) : ''; }

$name        = field('name');
$email       = field('email');
$number      = field('number');
$pickup      = field('pickup');
$destination = field('destination');
$message     = field('message');
$pref        = field('preferred_contact');
$date        = field('date');
$time        = field('time');
$flight      = field('flight_number');
$passengers  = field('number_of_passenger');
$luggage     = field('number_of_luggage'); 

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $number === '' || $pickup === '' || $destination === '' || $message === '') {
  echo 'error';
  exit;
}

$to      = 'contact@acetoursandtransfers.co.uk';
$from    = 'no-reply@acetoursandtransfers.co.uk'; // must be your domain mailbox
$subject = 'New Booking Request from Website';

$body =
"You have received a new booking request:\r\n\r\n".
"Name: $name\r\n".
"Email: $email\r\n".
"Phone: $number\r\n".
"Preferred contact: $pref\r\n".
"Date: $date\r\n".
"Time: $time\r\n".
"Flight number: $flight\r\n".
"Passengers: $passengers\r\n".
"Luggage: $luggage\r\n".
"Pickup: $pickup\r\n".
"Destination: $destination\r\n\r\n".
"Message:\r\n$message\r\n";

$headers  = "From: Ace Tours & Transfers <{$from}>\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// better deliverability on shared hosting:
$sent = @mail($to, $subject, $body, $headers, "-f {$from}");

echo $sent ? 'success' : 'error';
