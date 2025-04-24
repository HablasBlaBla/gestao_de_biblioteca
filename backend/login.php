<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../includes/conn.php';

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Validações básicas
    if (empty($email) || empty($senha)) {
        $_SESSION['erro_login'] = 'Todos os campos são obrigatórios!';
        header("Location: ../frontend/login.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_login'] = 'E-mail inválido!';
        header("Location: ../frontend/login.php");
        exit();
    }

    // Consulta verificando apenas se o usuário existe (não verificamos mais 'ativo')
    $sql = "SELECT id, nome, senha FROM professores WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $professor = $resultado->fetch_assoc();
        
        if (password_verify($senha, $professor['senha'])) {
            $_SESSION['professor_id'] = $professor['id'];
            $_SESSION['nome'] = $professor['nome'];
            
            // Registrar último login
            $update_sql = "UPDATE professores SET ultimo_login = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $professor['id']);
            $update_stmt->execute();
            $update_stmt->close();
            
            header("Location: ../frontend/dashboard.php");
            exit();
        } else {
            $_SESSION['erro_login'] = "Email ou senha incorretos!";
        }
    } else {
        $_SESSION['erro_login'] = "Email ou senha incorretos!";
    }

    $stmt->close();
    $conn->close();
    header("Location: ../frontend/login.php");
    exit();
}