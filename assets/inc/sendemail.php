<?php

/**
 * CIE South Korea contact form mail handler
 *
 * SECURITY NOTE:
 * - Do NOT commit real SMTP credentials to git.
 * - Prefer environment variables on the server where possible.
 */

// Define Host Info || Who is sending emails?
define("HOST_NAME", "CIE South Korea");
define("HOST_EMAIL", "no-reply@ciesouthkorea.org");

// Define SMTP Credentials
// Recommended: set these as environment variables on the server.
// If env vars are not present, these placeholders will be used (and sending will fail).
define("SMTP_HOST", getenv("CIE_SMTP_HOST") ?: "smtp.gmail.com");
define("SMTP_PORT", getenv("CIE_SMTP_PORT") ?: "465");
define(
    "SMTP_EMAIL",
    getenv("CIE_SMTP_EMAIL") ?: "your-smtp-username@domain.com",
);
define("SMTP_PASSWORD", getenv("CIE_SMTP_PASSWORD") ?: "your-smtp-password");

// Define Recipient Info || Who will get this email?
define("RECIPIENT_NAME", "CIE South Korea");
define("RECIPIENT_EMAIL", "info@ciesouthkorea.org");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fix include paths: this file lives in assets/inc/, so PHPMailer is in assets/inc/PHPMailer/
require __DIR__ . "/PHPMailer/src/Exception.php";
require __DIR__ . "/PHPMailer/src/PHPMailer.php";
require __DIR__ . "/PHPMailer/src/SMTP.php";

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 0; // Enable verbose debug output
    $mail->isSMTP(); // Send using SMTP
    $mail->Host = SMTP_HOST; // SMTP server
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = SMTP_EMAIL; // SMTP username
    $mail->Password = SMTP_PASSWORD; // SMTP password

    // Use implicit TLS for 465, STARTTLS for 587
    if ((int) SMTP_PORT === 587) {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    }

    $mail->Port = (int) SMTP_PORT;

    // Recipients
    $mail->setFrom(HOST_EMAIL, HOST_NAME);
    $mail->addAddress(RECIPIENT_EMAIL, RECIPIENT_NAME);

    // Content
    $name = isset($_POST["name"])
        ? preg_replace("/[^\.\-\' a-zA-Z0-9]/", "", $_POST["name"])
        : "";
    $senderEmail = isset($_POST["email"])
        ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST["email"])
        : "";
    $phone = isset($_POST["phone"])
        ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST["phone"])
        : "";
    $services = isset($_POST["services"])
        ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST["services"])
        : "";
    $subject = isset($_POST["subject"])
        ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST["subject"])
        : "";
    $address = isset($_POST["address"])
        ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST["address"])
        : "";
    $website = isset($_POST["website"])
        ? preg_replace("/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST["website"])
        : "";
    $message = isset($_POST["message"])
        ? preg_replace(
            "/(From:|To:|BCC:|CC:|Subject:|Content-Type:)/",
            "",
            $_POST["message"],
        )
        : "";

    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject =
        "CIE South Korea contact request" . ($name ? " - " . $name : "");

    $mail->Body = "Name: " . ($name ?: "-") . "<br>";
    $mail->Body .= "Email: " . ($senderEmail ?: "-") . "<br>";

    if ($phone) {
        $mail->Body .= "Phone: " . $phone . "<br>";
    }
    if ($services) {
        $mail->Body .= "Services: " . $services . "<br>";
    }
    if ($subject) {
        $mail->Body .= "Subject: " . $subject . "<br>";
    }
    if ($address) {
        $mail->Body .= "Address: " . $address . "<br>";
    }
    if ($website) {
        $mail->Body .= "Website: " . $website . "<br>";
    }

    $mail->Body .= "Message: <br>" . ($message ?: "-");

    $mail->send();
    echo "<div class='inner success'><p class='success'>Thanks for contacting CIE South Korea. We will get back to you as soon as possible.</p></div><!-- /.inner -->";
} catch (Exception $e) {
    echo "<div class='inner error'><p class='error'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p></div><!-- /.inner -->";
}
