<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

// Função para buscar livros na API do Google
function buscarLivroGoogle($titulo) {
    $url = 'https://www.googleapis.com/books/v1/volumes?q=' . urlencode($titulo);
    $response = file_get_contents($url);
    return json_decode($response, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo_livro = $_POST['titulo_livro'];
    $livros = buscarLivroGoogle($titulo_livro);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Livros - Google Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Buscar Livros</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="titulo_livro" class="form-label">Digite o título do livro para buscar na Google Books</label>
                        <input type="text" class="form-control" name="titulo_livro" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </form>

                <?php if (isset($livros)): ?>
                    <h5 class="mt-4">Resultados da Pesquisa</h5>
                    <form action="cadastro_livro.php" method="POST">
                        <ul class="list-group mt-3">
                            <?php foreach ($livros['items'] as $livro): ?>
                                <li class="list-group-item">
                                    <input type="checkbox" name="livros[]" value="<?php echo $livro['id']; ?>">
                                    <strong><?php echo $livro['volumeInfo']['title']; ?></strong>
                                    <p><?php echo implode(', ', $livro['volumeInfo']['authors'] ?? []); ?></p>
                                    <p><small><?php echo $livro['volumeInfo']['publishedDate'] ?? 'Data não disponível'; ?></small></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="submit" class="btn btn-success mt-3 w-100">Cadastrar Livros Selecionados</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <a href="dashboard.php">Dashboard</a>
</body>
</html>
