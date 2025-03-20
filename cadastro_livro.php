<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Conexão com o banco

// Função para buscar livros na API do Google
function buscarLivrosGoogle($termo, $tipo = 'nome') {
    $query = $tipo === 'isbn' ? 'isbn:' . urlencode($termo) : urlencode($termo);
    $url = 'https://www.googleapis.com/books/v1/volumes?q=' . $query;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    return $data['items'] ?? [];
}

// Se for uma busca de livros
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar'])) {
    $termo_busca = trim($_POST['termo_busca']);
    $tipo_busca = $_POST['tipo_busca'] ?? 'nome';
    $livros_encontrados = buscarLivrosGoogle($termo_busca, $tipo_busca);
}

// Se for um cadastro de livros
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
    if (!empty($_POST['livros'])) {
        foreach ($_POST['livros'] as $livro_id) {
            $livro_info = buscarLivrosGoogle($livro_id, 'isbn');
            $livro = $livro_info[0] ?? null;
            
            if ($livro) {
                $titulo = $livro['volumeInfo']['title'] ?? 'Sem título';
                $autor = implode(', ', $livro['volumeInfo']['authors'] ?? ['Desconhecido']);
                $descricao = $livro['volumeInfo']['description'] ?? 'Sem descrição';
                $isbn = $livro['volumeInfo']['industryIdentifiers'][0]['identifier'] ?? 'Não informado';
                $quantidade = 1;

                // Verifica se já existe no banco
                $sql_check = "SELECT id FROM livros WHERE isbn = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("s", $isbn);
                $stmt_check->execute();
                $stmt_check->store_result();
                
                if ($stmt_check->num_rows == 0) {
                    // Insere no banco
                    $sql = "INSERT INTO livros (titulo, autor, descricao, isbn, quantidade) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssi", $titulo, $autor, $descricao, $isbn, $quantidade);
                    $stmt->execute();
                }
                $stmt_check->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Gerenciar Livros</h4>
            </div>
            <div class="card-body">
                <a href="dashboard.php" class="btn btn-warning mb-3">Voltar ao Painel</a>

                <form method="POST">
                    <label class="form-label">Buscar Livro</label>
                    <input type="text" name="termo_busca" class="form-control" placeholder="Digite o ISBN ou nome">
                    <div class="mt-2">
                        <input type="radio" name="tipo_busca" value="isbn" checked> ISBN
                        <input type="radio" name="tipo_busca" value="nome"> Nome
                    </div>
                    <button type="submit" name="buscar" class="btn btn-primary mt-2">Buscar</button>
                </form>

                <?php if (isset($livros_encontrados) && count($livros_encontrados) > 0): ?>
                    <h5 class="mt-4">Resultados da Pesquisa</h5>
                    <form method="POST">
                        <ul class="list-group mt-3">
                            <?php foreach ($livros_encontrados as $livro): ?>
                                <?php
                                $id = $livro['id'];
                                $titulo = $livro['volumeInfo']['title'] ?? 'Título Desconhecido';
                                $autores = isset($livro['volumeInfo']['authors']) ? implode(', ', $livro['volumeInfo']['authors']) : 'Autor Desconhecido';
                                ?>
                                <li class="list-group-item">
                                    <input type="checkbox" name="livros[]" value="<?php echo $id; ?>">
                                    <strong><?php echo htmlspecialchars($titulo); ?></strong>
                                    <p><?php echo htmlspecialchars($autores); ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="submit" name="cadastrar" class="btn btn-success mt-3 w-100">Cadastrar Livros Selecionados</button>
                    </form>
                <?php elseif (isset($livros_encontrados)): ?>
                    <p class="text-warning mt-3">Nenhum livro encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>