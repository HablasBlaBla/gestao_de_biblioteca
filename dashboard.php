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
    <title>Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="favicon/favicon-32x32.png" type="image/x-icon">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-family: 'Arial', sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 15px 15px 0 0;
        }
        .list-group-item {
            transition: all 0.3s ease;
            cursor: pointer;
            background-color: #ffffff;
        }
        .list-group-item:hover {
            background-color: #e0e0e0;
            transform: scale(1.02);
        }
        .list-group-item a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
        }
        .list-group-item a:hover {
            color: #007bff;
        }
        .icon {
            margin-right: 12px;
            color: black;
            font-size: 1.5rem;
        }
        .card-header h2 {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-link {
            text-decoration: none;
            color: #333;
        }
        .btn-link:hover {
            color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h2><i class="fas fa-user"></i> Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>
            </div>
            <div class="card-body">
                <h4 class="text-center mb-4">Acesse as opções abaixo:</h4>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="cadastro_professor.php">
                            <i class="fas fa-chalkboard-teacher icon"></i> Cadastrar Professor
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="cadastro_aluno.php">
                            <i class="fas fa-user-graduate icon"></i> Cadastrar Aluno
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="buscar_livros.php">
                            <i class="fas fa-book icon"></i> Buscar e Cadastrar Livros
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="cadastro_emprestimos.php">
                            <i class="fas fa-exchange-alt icon"></i> Criar Empréstimo
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="gerenciar_emprestimos.php">
                            <i class="fas fa-tasks icon"></i> Gerenciar Empréstimos
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="editar_livro.php">
                            <i class="fas fa-edit icon"></i> Editar Livro
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="visualizar_livros.php">
                            <i class="fas fa-eye icon"></i> Visualizar Livros
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="relatorios.php">
                            <i class="fas fa-file-alt icon"></i> Relatórios
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="logout.php" class="btn btn-danger w-100 text-start">
                            <i class="fas fa-sign-out-alt icon"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
