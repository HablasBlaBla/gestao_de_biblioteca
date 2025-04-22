<?php
session_start();
include '../includes/conn.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';
require '../includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Verificar se há um e-mail na sessão
if (!isset($_SESSION['email_confirmacao'])) {
    header("Location: cadastro_professor_principal.php");
    exit();
}

// Buscar nome do professor
$stmt = $conn->prepare("SELECT nome FROM professores WHERE email = ?");
$stmt->bind_param("s", $_SESSION['email_confirmacao']);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();
$stmt->close();

// Gerar novo código com nova validade
$novo_codigo = rand(100000, 999999);
$_SESSION['codigo_confirmacao'] = $novo_codigo;
$_SESSION['codigo_expira'] = time() + 1800; // 30 minutos
$_SESSION['tentativas_confirmacao'] = 0; // Resetar tentativas

// Configurar PHPMailer
$mail = new PHPMailer(true);

try {
    // Configurações do servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'alefteste100@gmail.com'; // Seu e-mail Gmail
    $mail->Password = 'alef.1234'; // Sua senha do Gmail ou App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Remetente e destinatário
    $mail->setFrom('alefteste100@gmail.com', 'Biblioteca Escolar');
    $mail->addAddress($_SESSION['email_confirmacao'], $professor['nome']);

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Novo Código de Confirmação';
    
    // Corpo do e-mail em HTML
    $mail->Body = "
        <h1>Olá, {$professor['nome']}!</h1>
        <p>Seu novo código de confirmação é: <strong>$novo_codigo</strong></p>
        <p>Este código expira em 30 minutos.</p>
        <p>Por favor, insira este código no site para ativar sua conta.</p>
        <p>Atenciosamente,<br>Equipe da Biblioteca Escolar</p>
    ";
    
    // Versão alternativa em texto simples
    $mail->AltBody = "Olá {$professor['nome']},\n\nSeu novo código de confirmação é: $novo_codigo\n\nEste código expira em 30 minutos.\n\nPor favor, insira este código no site para ativar sua conta.";

    if ($mail->send()) {
        $_SESSION['sucesso_reenvio'] = "Novo código enviado com sucesso!";
    } else {
        $_SESSION['erro_reenvio'] = "Falha ao enviar novo código. Por favor, tente novamente.";
    }
} catch (Exception $e) {
    $_SESSION['erro_reenvio'] = "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}

header("Location: confirmar_codigo.php");
exit();
?>