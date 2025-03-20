<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Conexão com o banco

// Função para buscar livros na API do Google pelo título ou ISBN
function buscarLivroGoogle($termo) {
    $url = 'https://www.googleapis.com/books/v1/volumes?q=' . urlencode($termo);
    $response = file_get_contents($url);
    return json_decode($response, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $termo_busca = $_POST['termo_busca'];
    $livros = buscarLivroGoogle($termo_busca);
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
                <h4>Buscar e Cadastrar Livros</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="termo_busca" class="form-label">Digite o ISBN ou Título do livro</label>
                        <input type="text" class="form-control" name="termo_busca" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </form>

                <?php if (isset($livros) && isset($livros['items'])): ?>
                    <h5 class="mt-4">Resultados da Pesquisa</h5>
                    <form action="cadastro_livro.php" method="POST">
                        <ul class="list-group mt-3">
                            <?php foreach ($livros['items'] as $livro): ?>
                                <?php
                                $id = $livro['id'];
                                $titulo = $livro['volumeInfo']['title'] ?? 'Título Desconhecido';
                                $autores = isset($livro['volumeInfo']['authors']) ? implode(', ', $livro['volumeInfo']['authors']) : 'Autor Desconhecido';
                                $data_publicacao = $livro['volumeInfo']['publishedDate'] ?? 'Data não disponível';
                                ?>
                                <li class="list-group-item">
                                    <input type="checkbox" name="livros[]" value="<?php echo $id; ?>">
                                    <strong><?php echo htmlspecialchars($titulo); ?></strong>
                                    <p><?php echo htmlspecialchars($autores); ?></p>
                                    <p><small><?php echo htmlspecialchars($data_publicacao); ?></small></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="submit" class="btn btn-success mt-3 w-100">Cadastrar Livros Selecionados</button>
                    </form>
                <?php elseif (isset($livros)): ?>
                    <p class="text-warning mt-3">Nenhum livro encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <a href="dashboard.php">Dashboard</a>
</body>
</html>