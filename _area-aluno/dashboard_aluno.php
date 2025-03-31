<?php
session_start();

// Verifica se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit();
}

require '../conn.php'; // Arquivo de conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Para ícones -->
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3 class="text-center text-white">Dashboard</h3>
        <a href="perfil.php">Perfil</a>
        <a href="historico.php">Histórico do Aluno</a>
        <a href="mensagens.php">Mensagens</a>
        <a href="configuracoes.php">Configurações</a>
        <a href="sair.php">Sair</a>
    </div>

    <div class="content">
        <h1>Bem-vindo, <?php echo $_SESSION['aluno_nome']; ?>!</h1>
        <p>Email: <?php echo $_SESSION['aluno_email']; ?></p>
        <p>Aqui você pode acessar suas páginas de perfil, histórico, mensagens e configurações.</p>

        <div class="mt-5">
            <h3>Conteúdo da Página</h3>
            <p>Escolha uma opção no menu à esquerda para acessar as páginas.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
