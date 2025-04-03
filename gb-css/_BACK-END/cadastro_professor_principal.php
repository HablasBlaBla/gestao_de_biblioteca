<?php
session_start();
include '../conn.php'; // Arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $senha = md5($_POST['senha']); // Hash da senha com MD5

    // Verifica se o email ou CPF já estão cadastrados
    $stmt = $conn->prepare("SELECT id FROM professores WHERE email = ? OR cpf = ?");
    $stmt->bind_param("ss", $email, $cpf);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email ou CPF já cadastrados!');</script>";
    } else {
        // Insere o novo professor
        $stmt = $conn->prepare("INSERT INTO professores (nome, email, cpf, senha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $cpf, $senha);
        if ($stmt->execute()) {
            echo "<script>alert('Cadastro realizado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar!');</script>";
        }
    }
    $stmt->close();
    $conn->close();
}
?>
