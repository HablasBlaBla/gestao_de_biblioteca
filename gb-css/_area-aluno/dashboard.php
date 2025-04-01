<?php
session_start();

// Verifica se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit();
}

require '../conn.php'; // Arquivo de conexão com o banco de dados

// Verifica se foi feito uma pesquisa
$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// Consulta para pegar os livros
$sql = "SELECT id, titulo, autor, isbn, capa_url FROM livros WHERE titulo LIKE ? OR isbn LIKE ?";
$stmt = $conn->prepare($sql);
$pesquisa_param = "%$pesquisa%";
$stmt->bind_param("ss", $pesquisa_param, $pesquisa_param);
$stmt->execute();
$result = $stmt->get_result();

// Consulta para verificar se o aluno já pegou um livro emprestado
$aluno_id = $_SESSION['aluno_id'];
$sql_emprestimo = "SELECT id FROM emprestimos WHERE aluno_id = ? AND devolvido = 'não'";
$stmt_emprestimo = $conn->prepare($sql_emprestimo);
$stmt_emprestimo->bind_param("i", $aluno_id);
$stmt_emprestimo->execute();
$result_emprestimo = $stmt_emprestimo->get_result();
$ja_pegou_livro = $result_emprestimo->num_rows > 0; // Verifica se já pegou um livro emprestado
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="favicon/favicon-32x32.png" type="image/x-icon">
    <link rel="stylesheet" href="../_css/dashboard.css">
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <h1 class="text-center mb-0">Sistema de Gestão de Biblioteca - Aluno</h1>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="card animate-fade-in">
                    <div class="card-header">
                        <h2><i class="fas fa-user"></i> Bem-vindo, <?php echo $_SESSION['aluno_nome']; ?>!</h2>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a href="perfil.php">
                                    <i class="fas fa-user-circle icon"></i> Perfil
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="historico.php">
                                    <i class="fas fa-history icon"></i> Histórico do Aluno
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="mensagens.php">
                                    <i class="fas fa-envelope icon"></i> Mensagens
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="configuracoes.php">
                                    <i class="fas fa-cog icon"></i> Configurações
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="sair.php" class="btn btn-danger w-100 text-start">
                                    <i class="fas fa-sign-out-alt icon"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <h3>Catálogo de Livros</h3>
                <form method="get" class="mb-4">
                    <input type="text" name="pesquisa" class="form-control" placeholder="Pesquise pelo nome ou ISBN do livro" value="<?php echo htmlspecialchars($pesquisa); ?>">
                    <button type="submit" class="btn btn-primary mt-2">Pesquisar</button>
                </form>

                <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $livro_id = $row['id'];
                    $titulo = $row['titulo'];
                    $autor = $row['autor'];
                    $isbn = $row['isbn'];
                    $capa_url = $row['capa_url'] ?: 'https://via.placeholder.com/100x150'; // Imagem padrão caso não tenha capa
            ?>
                    <div class="col-md-4">
                        <div class="card book-card">
                            <img src="<?php echo $capa_url; ?>" class="card-img-top" alt="Capa do livro">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $titulo; ?></h5>
                                <p class="card-text">Autor: <?php echo $autor; ?></p>
                                <p class="card-text">ISBN: <?php echo $isbn; ?></p>
                                <a href="detalhes_livro.php?id=<?php echo $livro_id; ?>" class="btn btn-info">Detalhes</a>

                                <?php if (!$ja_pegou_livro) { ?>
                                    <a href="pegar_livro.php?id=<?php echo $livro_id; ?>" class="btn btn-success mt-2">Pegar Emprestado</a>
                                <?php } else { ?>
                                    <p class="text-warning mt-2">Você já pegou um livro emprestado.</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='alert alert-info'>Nenhum livro encontrado.</div>";
            }
            ?>
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

<?php
$stmt->close();
$conn->close();
?>
