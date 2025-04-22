<?php
session_start();
include '../includes/conn.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';
require '../includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Função para validar CPF
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $senha = $_POST['senha'];

    // Validações básicas
    if (empty($nome) || empty($email) || empty($cpf) || empty($senha)) {
        $_SESSION['erro_cadastro'] = 'Todos os campos são obrigatórios!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_cadastro'] = 'E-mail inválido!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    if (!validarCPF($cpf)) {
        $_SESSION['erro_cadastro'] = 'CPF inválido!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    if (strlen($senha) < 8) {
        $_SESSION['erro_cadastro'] = 'A senha deve ter pelo menos 8 caracteres!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o email ou CPF já estão cadastrados
    $stmt = $conn->prepare("SELECT id FROM professores WHERE email = ? OR cpf = ?");
    $stmt->bind_param("ss", $email, $cpf);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['erro_cadastro'] = 'Email ou CPF já cadastrados!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    // Insere o novo professor como inativo
    $stmt = $conn->prepare("INSERT INTO professores (nome, email, cpf, senha, ativo) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $nome, $email, $cpf, $senha_hash);
    
    if ($stmt->execute()) {
        // Gerar código de confirmação
        $codigo_confirmacao = rand(100000, 999999);
        $_SESSION['codigo_confirmacao'] = $codigo_confirmacao;
        $_SESSION['email_confirmacao'] = $email;
        $_SESSION['codigo_expira'] = time() + 1800; // 30 minutos
        $_SESSION['tentativas_confirmacao'] = 0;

        // Configurar PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configurações do servidor SMTP (deveriam estar em um arquivo de configuração)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alefteste100@gmail.com'; // Mover para variável de ambiente
            $mail->Password = 'alef.1234'; // Mover para variável de ambiente
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // Remetente e destinatário
            $mail->setFrom('alefteste100@gmail.com', 'Biblioteca Escolar');
            $mail->addAddress($email, $nome);

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Confirmação de Cadastro - Biblioteca Escolar';
            
            // Corpo do e-mail em HTML
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h1 style='color: #2c3e50;'>Olá, $nome!</h1>
                    <p style='font-size: 16px;'>Seu código de confirmação é:</p>
                    <div style='background: #f8f9fa; padding: 20px; text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; letter-spacing: 2px;'>
                        $codigo_confirmacao
                    </div>
                    <p style='font-size: 14px; color: #7f8c8d;'>Este código expira em 30 minutos.</p>
                    <p style='font-size: 16px;'>Por favor, insira este código no site para ativar sua conta.</p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 14px; color: #7f8c8d;'>Se você não solicitou este cadastro, por favor ignore este e-mail.</p>
                    <p style='font-size: 14px; color: #7f8c8d;'>Atenciosamente,<br>Equipe da Biblioteca Escolar</p>
                </div>
            ";
            
            // Versão alternativa em texto simples
            $mail->AltBody = "Olá $nome,\n\nSeu código de confirmação é: $codigo_confirmacao\n\nEste código expira em 30 minutos.\n\nPor favor, insira este código no site para ativar sua conta.\n\nAtenciosamente,\nEquipe da Biblioteca Escolar";

            if ($mail->send()) {
                $_SESSION['sucesso_cadastro'] = 'Cadastro realizado com sucesso! Verifique seu e-mail (incluindo a pasta de spam) para confirmar sua conta.';
                header("Location: ../includes/confirmar_codigo.php");
                exit();
            } else {
                $_SESSION['erro_cadastro'] = 'Cadastro realizado, mas houve um problema ao enviar o e-mail de confirmação. Por favor, entre em contato com o suporte.';
                header("Location: ../frontend/cadastro_professor_principal.php");
                exit();
            }
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
            $_SESSION['erro_cadastro'] = "Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.";
            header("Location: ../frontend/cadastro_professor_principal.php");
            exit();
        }
    } else {
        error_log("Erro ao cadastrar professor: " . $conn->error);
        $_SESSION['erro_cadastro'] = 'Erro ao realizar cadastro. Por favor, tente novamente.';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }
    
    $stmt->close();
    $conn->close();
}
?>