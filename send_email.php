<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

/* ---------------------------------------------
   ENV LOADING (hPanel env -> Dotenv -> .env parse)
---------------------------------------------- */
function loadEnvIfNeeded(string $dir)
{
  // If running on Hostinger with env set in hPanel, nothing to do.
  if (getenv('SMTP_HOST') || !file_exists($dir.'/.env')) return;

  // Try vlucas/phpdotenv if present
  if (class_exists('Dotenv\Dotenv')) {
    Dotenv\Dotenv::createImmutable($dir)->safeLoad();
    return;
  }

  // Minimal fallback .env parser (no external lib)
  $lines = @file($dir.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!$lines) return;
  foreach ($lines as $line) {
    if ($line[0] === '#' || !str_contains($line, '=')) continue;
    [$k,$v] = explode('=', $line, 2);
    $k = trim($k);
    $v = trim($v);
    if ($v !== '' && ($v[0] === '"' || $v[0] === "'")) {
      $v = trim($v, "\"'");
    }
    $_ENV[$k] = $v;
    $_SERVER[$k] = $v;
    putenv("$k=$v");
  }
}

loadEnvIfNeeded(__DIR__);

/* ---------- helpers ---------- */
function envv($key, $default = '')
{
  $v = getenv($key);
  if ($v === false || $v === '') {
    $v = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
  }
  return is_string($v) ? trim($v) : $v;
}
function field($k){ return isset($_POST[$k]) ? trim(strip_tags($_POST[$k])) : ''; }

$debug = isset($_GET['debug']) && $_GET['debug'] == '1'; // ?debug=1 to see errors

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo 'error';
  exit;
}

/* ---------- collect fields ---------- */
$name        = field('name');
$email       = field('email');
$number      = field('number');
$pref        = field('preferred_contact');
$date        = field('date');
$time        = field('time');
$flight      = field('flight_number');
$passengers  = field('number_of_passenger');
$luggage     = field('number_of_luggage');
$pickup      = field('pickup');
$destination = field('destination');
$message     = field('message');

/* ---------- minimal validation ---------- */
if (
  $name === '' ||
  !filter_var($email, FILTER_VALIDATE_EMAIL) ||
  $number === '' || $pickup === '' || $destination === '' || $message === ''
) {
  echo 'error';
  exit;
}

/* ---------- SMTP config from env ---------- */
$smtpHost  = envv('SMTP_HOST', 'smtp.hostinger.com');
$smtpUser  = envv('SMTP_USER', '');
$smtpPass  = envv('SMTP_PASS', '');
$fromEmail = envv('FROM_EMAIL', 'booking@acetoursandtransfers.co.uk');
$fromName  = envv('FROM_NAME', 'Ace Tours & Transfers');
$ownerTo   = envv('OWNER_TO', 'booking@acetoursandtransfers.co.uk');

/* Fail fast if essential envs are missing */
if ($smtpUser === '' || $smtpPass === '') {
  if ($debug) {
    header('Content-Type: text/plain');
    echo "Missing SMTP env values.\n";
    echo "SMTP_USER='$smtpUser'\n";
    echo "SMTP_PASS length=".strlen($smtpPass)."\n";
    echo "Check hPanel → Advanced → Environment Variables OR the .env file.\n";
  } else {
    echo 'error';
  }
  exit;
}

$logoUrl = 'https://acetoursandtransfers.co.uk/assets/img/ace_logo.png';

/* ---------- message content ---------- */
$ownerSubject = 'New booking request (website)';
$ownerBody =
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

/* Subject includes destination (and pickup if present) */
$toPart   = $destination !== '' ? " to $destination" : '';
$fromPart = $pickup !== '' ? " from $pickup" : '';
$userSubject = "We received your booking request$toPart$fromPart";

$userText =
"Hi $name,\n\n".
"Thanks for choosing Ace Tours & Transfers. We’ve received your request and will get back to you shortly to confirm the details.\n\n".
"Summary:\n".
($date ? "- Date: $date\n" : "").
($time ? "- Time: $time\n" : "").
"- Pickup: $pickup\n".
"- Destination: $destination\n".
($passengers ? "- Passengers: $passengers\n" : "").
($flight ? "- Flight No.: $flight\n" : "").
($luggage ? "- Luggage: $luggage\n" : "").
"\nIf your booking is urgent, please call 07415 739444.\n\n".
"Ace Tours & Transfers\n".
"acetoursandtransfers.co.uk | Bath, United Kingdom\n";

$userHtml = '
<div style="font-family:Arial,Helvetica,sans-serif;max-width:640px;margin:auto;border:1px solid #eaeaea;border-radius:10px;overflow:hidden;background:#ffffff">
  <div style="background:#0f0f0f;padding:12px;text-align:center">
    <img src="'.htmlspecialchars($logoUrl, ENT_QUOTES).'" alt="Ace Tours & Transfers" style="max-width:160px;height:auto">
  </div>
  <div style="padding:20px;color:#222;line-height:1.5">
    <h2 style="margin:0 0 12px;font-weight:600;color:#0f0f0f">We received your booking request</h2>
    <p>Hi '.htmlspecialchars($name, ENT_QUOTES).',</p>
    <p>Thanks for choosing <strong>Ace Tours & Transfers</strong>. We’ve received your request and will get back to you shortly to confirm the details.</p>

    <h3 style="margin:18px 0 8px;font-weight:600;color:#0f0f0f">Summary</h3>
    <table cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse">
      '.($date ? '<tr><td style="border-bottom:1px solid #eee"><strong>Date</strong></td><td style="border-bottom:1px solid #eee">'.htmlspecialchars($date, ENT_QUOTES).'</td></tr>' : '').'
      '.($time ? '<tr><td style="border-bottom:1px solid #eee"><strong>Time</strong></td><td style="border-bottom:1px solid #eee">'.htmlspecialchars($time, ENT_QUOTES).'</td></tr>' : '').'
      <tr><td style="border-bottom:1px solid #eee"><strong>Pickup</strong></td><td style="border-bottom:1px solid #eee">'.htmlspecialchars($pickup, ENT_QUOTES).'</td></tr>
      <tr><td style="border-bottom:1px solid #eee"><strong>Destination</strong></td><td style="border-bottom:1px solid #eee">'.htmlspecialchars($destination, ENT_QUOTES).'</td></tr>
      '.($passengers ? '<tr><td style="border-bottom:1px solid #eee"><strong>Passengers</strong></td><td style="border-bottom:1px solid #eee">'.htmlspecialchars($passengers, ENT_QUOTES).'</td></tr>' : '').'
      '.($luggage ? '<tr><td style="border-bottom:1px solid #eee"><strong>Luggage</strong></td><td style="border-bottom:1px solid #eee">'.htmlspecialchars($luggage, ENT_QUOTES).'</td></tr>' : '').'
      '.($flight ? '<tr><td style="border-bottom:1px solid #eee"><strong>Flight No.</strong></td><td style="border-bottom:1px solid #eee">'.htmlspecialchars($flight, ENT_QUOTES).'</td></tr>' : '').'
    </table>

    <p style="margin-top:18px">If your booking is urgent, please call <a href="tel:07415739444">07415 739444</a>.</p>
    <p style="margin-top:12px">Safe travels,<br><strong>Ace Tours & Transfers</strong><br>
      <a href="https://acetoursandtransfers.co.uk">acetoursandtransfers.co.uk</a><br>
      Bath, United Kingdom
    </p>
  </div>
</div>';

/* ---------- mailer factory ---------- */
function makeMailer($host,$user,$pass,$fromEmail,$fromName){
  $m = new PHPMailer(true);
  $m->CharSet    = 'UTF-8';
  $m->isSMTP();
  $m->Host       = $host;
  $m->SMTPAuth   = true;
  $m->Username   = $user;
  $m->Password   = $pass;
  $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $m->Port       = 587;
  $m->Hostname   = 'acetoursandtransfers.co.uk'; // EHLO domain
  $m->setFrom($fromEmail, $fromName);
  $m->Sender     = $fromEmail; // Return-Path
  $m->MessageID  = sprintf('<%s.%s@acetoursandtransfers.co.uk>', time(), bin2hex(random_bytes(6)));
  $m->addCustomHeader('X-Mailer', 'Ace Tours & Transfers / PHPMailer');
  return $m;
}

/* ---------- send ---------- */
try {
  // 1) Owner notification
  $owner = makeMailer($smtpHost,$smtpUser,$smtpPass,$fromEmail,$fromName);
  $owner->SMTPDebug = $debug ? 2 : 0;
  $owner->addAddress($ownerTo, 'Ace Tours & Transfers');
  $owner->addReplyTo($email, $name);
  $owner->Subject = $ownerSubject;
  $owner->isHTML(false);
  $owner->Body    = $ownerBody;
  $owner->addCustomHeader('X-Entity-Type','Notification');

  if (!$owner->send()) {
    if ($debug) { header('Content-Type: text/plain'); echo $owner->ErrorInfo; }
    echo 'error';
    exit;
  }

  // 2) Customer auto-reply
  $userM = makeMailer($smtpHost,$smtpUser,$smtpPass,$fromEmail,$fromName);
  $userM->SMTPDebug = $debug ? 2 : 0;
  $userM->addAddress($email, $name);
  $userM->addReplyTo($fromEmail, $fromName);
  $userM->Subject = $userSubject;
  $userM->isHTML(true);
  $userM->Body    = $userHtml;
  $userM->AltBody = $userText;

  $userM->addCustomHeader('Auto-Submitted', 'auto-replied');
  $userM->addCustomHeader('List-Unsubscribe', '<mailto:'.$fromEmail.'>, <https://acetoursandtransfers.co.uk/#contact>');

  $ok = $userM->send();
  if ($debug && !$ok) { header('Content-Type: text/plain'); echo $userM->ErrorInfo; }
  echo $ok ? 'success' : 'error';

} catch (Exception $e) {
  if ($debug) { header('Content-Type: text/plain'); echo $e->getMessage(); }
  echo 'error';
}
