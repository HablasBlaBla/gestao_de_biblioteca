<?php
session_start();
include 'conn.php';

// Verificar se o usuário veio do processo de cadastro
if (!isset($_SESSION['codigo_confirmacao']) || !isset($_SESSION['email_confirmacao'])) {
    header("Location: cadastro_professor_principal.php");
    exit();
}

// Verificar se o código expirou
if (time() > $_SESSION['codigo_expira']) {
    unset($_SESSION['codigo_confirmacao']);
    unset($_SESSION['email_confirmacao']);
    unset($_SESSION['codigo_expira']);
    $_SESSION['erro_confirmacao'] = "O código expirou. Por favor, cadastre-se novamente.";
    header("Location: cadastro_professor_principal.php");
    exit();
}

$erro = '';
$sucesso = '';

// Processar o formulário de confirmação
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo_digitado = trim($_POST['codigo']);
    
    if (empty($codigo_digitado)) {
        $erro = "Por favor, digite o código recebido por e-mail.";
    } else {
        // Incrementar tentativas
        $_SESSION['tentativas_confirmacao']++;
        
        // Verificar limite de tentativas
        if ($_SESSION['tentativas_confirmacao'] > 5) {
            $erro = "Número máximo de tentativas excedido. O código foi invalidado.";
            unset($_SESSION['codigo_confirmacao']);
            unset($_SESSION['email_confirmacao']);
            unset($_SESSION['codigo_expira']);
        } elseif ($codigo_digitado == $_SESSION['codigo_confirmacao']) {
            // Código correto - ativar a conta no banco de dados
            $email = $_SESSION['email_confirmacao'];
            
            $stmt = $conn->prepare("UPDATE professores SET ativo = 1, data_ativacao = NOW() WHERE email = ?");
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $sucesso = "Conta ativada com sucesso! Você será redirecionado para o login.";
                
                // Limpar sessão
                unset($_SESSION['codigo_confirmacao']);
                unset($_SESSION['email_confirmacao']);
                unset($_SESSION['codigo_expira']);
                unset($_SESSION['tentativas_confirmacao']);
                
                // Redirecionar após 3 segundos
                header("Refresh: 3; url=login.php");
            } else {
                $erro = "Erro ao ativar conta. Por favor, tente novamente.";
            }
            $stmt->close();
        } else {
            $tentativas_restantes = 5 - $_SESSION['tentativas_confirmacao'];
            $erro = "Código inválido. Tentativas restantes: $tentativas_restantes";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .confirmacao-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .codigo-input {
            letter-spacing: 5px;
            font-size: 24px;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmacao-container">
            <h2 class="text-center mb-4">Confirmação de Cadastro</h2>
            
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <?php else: ?>
                <p class="text-center mb-4">Enviamos um código de confirmação para o e-mail <strong><?php echo $_SESSION['email_confirmacao']; ?></strong>. Por favor, digite o código abaixo:</p>
                
                <form method="POST" action="confirmar_codigo.php">
                    <div class="mb-3">
                        <input type="text" name="codigo" class="form-control codigo-input" maxlength="6" placeholder="______" required autofocus>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                        <a href="reenviar_codigo.php" class="btn btn-outline-secondary">Reenviar Código</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto avançar entre os campos (se quiser dividir em 6 inputs)
        document.querySelector('.codigo-input').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>