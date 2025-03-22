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

// Função para buscar detalhes do livro na API do Google
function buscarDetalhesLivroGoogle($id_livro) {
    $url = 'https://www.googleapis.com/books/v1/volumes/' . $id_livro;
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Verifica se o formulário foi enviado para cadastrar os livros
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['livros']) && !empty($_POST['livros'])) {
        $livros_selecionados = $_POST['livros'];
        
        // Prepara os dados para inserção no banco
        foreach ($livros_selecionados as $id_livro) {
            $livro = buscarDetalhesLivroGoogle($id_livro);
            
            $titulo = $livro['volumeInfo']['title'] ?? 'Título Desconhecido';
            $autor = isset($livro['volumeInfo']['authors']) ? implode(', ', $livro['volumeInfo']['authors']) : 'Autor Desconhecido';
            $isbn = $livro['volumeInfo']['industryIdentifiers'][0]['identifier'] ?? 'ISBN Desconhecido';
            $capa_url = $livro['volumeInfo']['imageLinks']['thumbnail'] ?? 'sem_capa.png';  // Imagem padrão de capa
            $descricao = $livro['volumeInfo']['description'] ?? NULL;
            $categoria = $livro['volumeInfo']['categories'][0] ?? NULL;
            $ano_publicacao = substr($livro['volumeInfo']['publishedDate'], 0, 4) ?? 'Ano não disponível';
            $genero = $livro['volumeInfo']['categories'][0] ?? 'Gênero Desconhecido';
            $quantidade = 1; // Você pode definir uma quantidade padrão ou permitir que o professor edite isso.
            
            // Insere o livro no banco de dados
            $sql = "INSERT INTO livros (titulo, autor, isbn, capa_url, descricao, categoria, ano_publicacao, genero, quantidade) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssss", $titulo, $autor, $isbn, $capa_url, $descricao, $categoria, $ano_publicacao, $genero, $quantidade);

            if (!$stmt->execute()) {
                echo "Erro ao cadastrar livro: " . $stmt->error;
            }
        }
        
        // Redireciona após o cadastro
        header("Location: buscar_livros.php?status=success"); // Passa um parâmetro de sucesso
        exit();
    } else {
        header("Location: buscar_livros.php?status=fail"); // Caso contrário, falha
        exit();
    }
}

// Função de busca de livros
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['termo_busca'])) {
    $termo_busca = $_GET['termo_busca'];
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
            background-color: #007bff;
            color: white;
        }

        .card-header h2 {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .card-body {
            padding: 2rem;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #0056b3;
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

        .icon {
            margin-right: 12px;
            color: black;
            font-size: 1.5rem;
        }

        .modal-footer .btn-success {
            background-color: #28a745;
        }

        .modal-footer .btn-danger {
            background-color: #dc3545;
        }

        /* Capa do livro */
        .livro-capa {
            max-width: 100px;
            max-height: 150px;
            object-fit: cover;
        }

        /* Mensagens de status */
        .alert-custom {
            font-size: 1.2rem;
            text-align: center;
            border-radius: 5px;
            padding: 1rem;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php
        // Verifica o status para mostrar a mensagem de sucesso ou falha
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo '<div class="alert-custom alert-success">LIVROS CADASTRADOS COM SUCESSO</div>';
            } elseif ($_GET['status'] == 'fail') {
                echo '<div class="alert-custom alert-danger">LIVROS NÃO CADASTRADOS</div>';
            }
        }
        ?>

        <div class="card">
            <div class="card-header text-center">
                <h2><i class="fas fa-book"></i> Buscar e Cadastrar Livros</h2>
            </div>
            <div class="card-body">
                <!-- Formulário de busca -->
                <form method="GET" action="buscar_livros.php">
                    <div class="mb-3">
                        <label for="termo_busca" class="form-label">Digite o ISBN ou Título do livro</label>
                        <input type="text" class="form-control" name="termo_busca" required>
                    </div>
                    <button type="submit" class="btn btn-custom w-100">Buscar</button>
                </form>

                <?php if (isset($livros) && isset($livros['items'])): ?>
                    <h5 class="mt-4">Resultados da Pesquisa</h5>
                    <form id="formCadastro" method="POST" action="buscar_livros.php">
                        <ul class="list-group mt-3">
                            <?php foreach ($livros['items'] as $livro): ?>
                                <?php
                                $id = $livro['id'];
                                $titulo = $livro['volumeInfo']['title'] ?? 'Título Desconhecido';
                                $autores = isset($livro['volumeInfo']['authors']) ? implode(', ', $livro['volumeInfo']['authors']) : 'Autor Desconhecido';
                                $isbn = isset($livro['volumeInfo']['industryIdentifiers'][0]['identifier']) ? $livro['volumeInfo']['industryIdentifiers'][0]['identifier'] : 'ISBN Desconhecido';
                                $capa_url = $livro['volumeInfo']['imageLinks']['thumbnail'] ?? 'img/sem_capa.png';  // Imagem padrão de capa
                                ?>
                                <li class="list-group-item">
                                    <div class="d-flex">
                                        <!-- Exibe a capa do livro -->
                                        <img src="<?php echo $capa_url; ?>" alt="Capa do livro" class="livro-capa me-3">
                                        <div>
                                            <strong><?php echo htmlspecialchars($titulo); ?></strong>
                                            <p><?php echo htmlspecialchars($autores); ?></p>
                                            <p><small>ISBN: <?php echo htmlspecialchars($isbn); ?></small></p>
                                        </div>
                                    </div>
                                    <input type="checkbox" name="livros[]" value="<?php echo $id; ?>" class="mt-2">
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn btn-success mt-3 w-100" data-bs-toggle="modal" data-bs-target="#confirmacaoModal">Cadastrar Livros Selecionados</button>
                    </form>
                <?php elseif (isset($livros)): ?>
                    <p class="text-warning mt-3">Nenhum livro encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação -->
    <div class="modal fade" id="confirmacaoModal" tabindex="-1" aria-labelledby="confirmacaoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmacaoModalLabel">Confirmar Cadastro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja cadastrar os livros selecionados?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirmarCadastro">Confirmar Cadastro</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Função para enviar o formulário quando o usuário confirmar no modal
        document.getElementById("confirmarCadastro").addEventListener("click", function() {
            document.getElementById("formCadastro").submit();
        });
    </script>
</body>
</html>
