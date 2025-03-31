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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        .book-card {
            margin-bottom: 20px;
        }
        .book-card img {
            max-width: 100px;
            max-height: 150px;
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
