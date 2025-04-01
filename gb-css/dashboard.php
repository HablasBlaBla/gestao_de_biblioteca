<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
include('conn.php');

// Query to get the total number of students
$result_students = $conn->query("SELECT COUNT(*) as total FROM alunos");
$total_students = $result_students->fetch_assoc()['total'];

// Query to get the total number of books
$result_books = $conn->query("SELECT COUNT(*) as total FROM livros");
$total_books = $result_books->fetch_assoc()['total'];

// Query to get the total number of active loans
$result_loans = $conn->query("SELECT COUNT(*) as total FROM emprestimos WHERE devolvido = '0'");
$total_loans = $result_loans->fetch_assoc()['total'];

// Query to get the total number of pending returns
$result_pending_returns = $conn->query("SELECT COUNT(*) as total FROM emprestimos WHERE data_devolucao IS NULL");
$total_pending_returns = $result_pending_returns->fetch_assoc()['total'];
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
    <link rel="stylesheet" href="_css/dashboard.css">
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <h1 class="text-center mb-0">Sistema de Gestão de Biblioteca</h1>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="card animate-fade-in">
                    <div class="card-header">
                        <h2><i class="fas fa-user"></i> Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>
                    </div>
                    <div class="card-body">
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
                            <li class="list-group-item position-relative">
                                <a href="enviar_mensagem.php">
                                    <i class="fas fa-envelope icon"></i> MENSAGEM
                                    <span class="notification-badge">3</span>
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
                                    <i class="fas fa-tasks icon"></i> GERENCIAR EMPRÉSTIMOS
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="listar_emprestimos.php">
                                    <i class="fas fa-list icon"></i> EMPRÉSTIMOS REGISTADOS
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
                                    <i class="fas fa-history icon"></i> HISTÓRICO
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
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-6">
                        <div class="stats-card animate-fade-in">
                            <div class="text-center">
                                <i class="fas fa-users stats-icon"></i>
                                <div class="stats-number"><?php echo $total_students; ?></div>
                                <div class="stats-label">Alunos Cadastrados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stats-card animate-fade-in">
                            <div class="text-center">
                                <i class="fas fa-book stats-icon"></i>
                                <div class="stats-number"><?php echo $total_books; ?></div>
                                <div class="stats-label">Livros no Acervo</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stats-card animate-fade-in">
                            <div class="text-center">
                                <i class="fas fa-exchange-alt stats-icon"></i>
                                <div class="stats-number"><?php echo $total_loans; ?></div>
                                <div class="stats-label">Empréstimos Ativos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stats-card animate-fade-in">
                            <div class="text-center">
                                <i class="fas fa-clock stats-icon"></i>
                                <div class="stats-number"><?php echo $total_pending_returns; ?></div>
                                <div class="stats-label">Devoluções Pendentes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <a href="cadastro_emprestimos.php" class="quick-action-btn">
                        <i class="fas fa-plus-circle"></i> Novo Empréstimo
                    </a>
                    <a href="buscar_livros.php" class="quick-action-btn">
                        <i class="fas fa-search"></i> Buscar Livro
                    </a>
                    <a href="relatorios.php" class="quick-action-btn">
                        <i class="fas fa-chart-bar"></i> Relatório Rápido
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Adiciona animação aos elementos quando eles entram na viewport
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.animate-fade-in');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });

        // Adiciona interatividade aos cards de estatísticas
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        });
    </script>
</body>
</html>
