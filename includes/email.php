<?php
require_once('PHPMailer/src/PHPMailer.php');
require_once('PHPMailer/src/SMTP.php');
require_once('PHPMailer/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = 'alefteste100@gmail.com'; // SMTP username
    $mail->Password = '123'; // SMTP password
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port = 587; // TCP port to connect to

    $mail->setFrom('alefteste100@gmail.com', 'Mailer'); // Set the sender's email and name
    $mail->addAddress('alefteste100@gmail.com', 'Recipient Name'); // Add a recipient

    $mail->isHTML(true);
    $mail->Subject = 'Teste envio de email'; // Set the email subject
    $mail->Body    = 'chegou email de teste'; // Set the email body

    if ($mail->send()) {
        echo 'Mensagem enviada com sucesso!';
        } else {
        echo 'Mensagem não enviada.';
    }

} catch (Exception $e) {
    echo "Falha ao enviar mensagem: {$mail->ErrorInfo}";
}
