<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../includes/conn.php';

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Consulta verificando se a conta está ativa
    $sql = "SELECT id, nome, senha FROM professores WHERE email = ? AND ativo = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $professor = $resultado->fetch_assoc();
        
        // Verificar a senha com password_verify()
        if (password_verify($senha, $professor['senha'])) {
            $_SESSION['professor_id'] = $professor['id'];
            $_SESSION['nome'] = $professor['nome'];
            
            // Registrar último login
            $update_sql = "UPDATE professores SET ultimo_login = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $professor['id']);
            $update_stmt->execute();
            $update_stmt->close();
            
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['erro_login'] = "Email ou senha incorretos!";
        }
    } else {
        // Verificar se o usuário existe mas não está ativo
        $sql_inativo = "SELECT id FROM professores WHERE email = ? AND ativo = 0";
        $stmt_inativo = $conn->prepare($sql_inativo);
        $stmt_inativo->bind_param("s", $email);
        $stmt_inativo->execute();
        $stmt_inativo->store_result();
        
        if ($stmt_inativo->num_rows > 0) {
            $_SESSION['erro_login'] = "Conta não ativada. Por favor, verifique seu e-mail para confirmar o cadastro.";
        } else {
            $_SESSION['erro_login'] = "Email ou senha incorretos!";
        }
        $stmt_inativo->close();
    }

    $stmt->close();
    $conn->close();
    header("Location: login.php");
    exit();
}
?>