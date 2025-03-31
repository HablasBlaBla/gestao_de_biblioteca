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
            background: linear-gradient(135deg, #f0f4f8, #e0e7ff); /* Fundo suave */
            font-family: 'Arial', sans-serif;
            color: #212121;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 15px 15px 0 0;
            background-color: #00796b;
            color: white;
        }
        .card-header h2 {
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
        }
        .card-body {
            padding: 2rem;
            background-color: white;
            border-radius: 0 0 15px 15px;
        }
        .list-group-item {
    transition: all 0.4s ease; /* Aumentar a duração da transição */
    cursor: pointer;
    background-color: #ffffff;
    border: none;
    color: #00796b;
}

.list-group-item:hover {
    background-color: #00796b;
    color: white;
    transform: scale(1.05); /* Aumentar a escala para um efeito mais sutil */
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2); /* Adicionar sombra */
}

.list-group-item a {
    text-decoration: none;
    color: inherit; /* Herda a cor do item da lista */
    display: flex;
    align-items: center;
    font-size: 1.1rem;
}

.list-group-item a:hover {
    color: white; /* Cor do link ao passar o mouse */
}

.icon {
    margin-right: 12px;
    color: #00796b; /* Cor inicial do ícone */
    font-size: 1.5rem;
    transition: color 0.3s ease;
}

.list-group-item:hover .icon {
    color: white; /* Mantém o ícone visível em branco ao passar o mouse */
}

.btn-danger {
    background-color: #dc3545;
    color: white;
    padding: 12px;
    border-radius: 8px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease; /* Adicionar transição */
}

.btn-danger:hover {
    background-color: #c82333;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2); /* Adicionar sombra ao passar o mouse */
}
        
        .text-center {
            text-align: center;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .alert-custom {
            margin-top: 20px;
            background-color: #c8e6c9;
            color: #388e3c;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-user"></i> Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>
            </div>
            <div class="card-body">
                <h4 class="text-center mb-4">Acesse as opções abaixo:</h4>
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="cadastro_professor.php">
                            <i class="fas fa-chalkboard-teacher icon"></i> CADASTRAR PROFESSOR
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="cadastro_aluno.php">
                            <i class="fas fa-user-graduate icon"></i> CADASTRAR ALUNO
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="enviar_mensagem.php">
                            <i class="fas fa-user-graduate icon"></i> MENSAGEM
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="buscar_livros.php">
                            <i class="fas fa-book icon"></i> BUSCAR E CADASTRAR LIVROS
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="cadastro_emprestimos.php">
                            <i class="fas fa-exchange-alt icon"></i> CRIAR EMPRÉSTIMOS
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="gerenciar_emprestimos.php">
                            <i class="fas fa-tasks icon"></i> CRIAR EMPRÉSTIMOS
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="listar_emprestimos.php">
                            <i class="fas fa-tasks icon"></i> EMPRÉSTIMOS REGISTADOS
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="editar_livro.php">
                            <i class="fas fa-edit icon"></i> EDITAR LIVRO
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="visualizar_livros.php">
                            <i class="fas fa-eye icon"></i> VISUALIZAR LIVROS
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="relatorios.php">
                            <i class="fas fa-file-alt icon"></i> RELATÓRIOS
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="historico_emprestimos.php">
                            <i class="fas fa-file-alt icon"></i> Histórico
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="logout.php" class="btn btn-danger w-100 text-start">
                            <i class="fas fa-sign-out-alt icon"></i> SAIR
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
