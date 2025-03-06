<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="favicon/favicon-32x32.png" type="image/x-icon">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h2>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>
            </div>
            <div class="card-body">
                <h4 class="text-center mb-4">Acesse as opções abaixo:</h4>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="cadastro_aluno.php" class="btn btn-link w-100 text-start">Cadastrar Aluno</a>
                    </li>
                    <li class="list-group-item">
                        <a href="cadastro_livro.php" class="btn btn-link w-100 text-start">Cadastrar Livro</a>
                    </li>
                    <li class="list-group-item">
                        <a href="cadastro_emprestimos.php" class="btn btn-link w-100 text-start">Empréstimos</a>
                    </li>
                    <!-- <li class="list-group-item">
                        <a href="gerenciar_emprestimos.php" class="btn btn-link w-100 text-start">Gerenciar Empréstimos</a> -->
                    </li>
                    <li class="list-group-item">
                        <a href="visualizar_livros.php" class="btn btn-link w-100 text-start">Visualizar Livros</a>
                    </li>
                    <li class="list-group-item">
                        <a href="relatorios.php" class="btn btn-link w-100 text-start">Relatórios</a>
                    </li>
                    <li class="list-group-item">
                        <a href="logout.php" class="btn btn-danger w-100 text-start">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
