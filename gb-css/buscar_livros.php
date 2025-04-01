<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php';

function buscarLivroGoogle($termo)
{
    $url = 'https://www.googleapis.com/books/v1/volumes?q=' . urlencode($termo);
    $response = file_get_contents($url);
    return json_decode($response, true);
}

function buscarDetalhesLivroGoogle($id_livro)
{
    $url = 'https://www.googleapis.com/books/v1/volumes/' . $id_livro;
    $response = file_get_contents($url);
    return json_decode($response, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['livros']) && !empty($_POST['livros'])) {
        $livros_selecionados = $_POST['livros'];

        foreach ($livros_selecionados as $id_livro) {
            $livro = buscarDetalhesLivroGoogle($id_livro);

            $titulo = $livro['volumeInfo']['title'] ?? 'Título Desconhecido';
            $autor = isset($livro['volumeInfo']['authors']) ? implode(', ', $livro['volumeInfo']['authors']) : 'Autor Desconhecido';
            $isbn = $livro['volumeInfo']['industryIdentifiers'][0]['identifier'] ?? 'ISBN Desconhecido';
            $capa_url = $livro['volumeInfo']['imageLinks']['thumbnail'] ?? 'sem_capa.png';
            $descricao = $livro['volumeInfo']['description'] ?? NULL;
            $categoria = $livro['volumeInfo']['categories'][0] ?? NULL;
            $ano_publicacao = substr($livro['volumeInfo']['publishedDate'], 0, 4) ?? 'Ano não disponível';
            $genero = $livro['volumeInfo']['categories'][0] ?? 'Gênero Desconhecido';
            $quantidade = 1;

            $sql = "INSERT INTO livros (titulo, autor, isbn, capa_url, descricao, categoria, ano_publicacao, genero, quantidade) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssss", $titulo, $autor, $isbn, $capa_url, $descricao, $categoria, $ano_publicacao, $genero, $quantidade);

            if (!$stmt->execute()) {
                echo "Erro ao cadastrar livro: " . $stmt->error;
            }
        }

        header("Location: buscar_livros.php?status=success");
        exit();
    } else {
        header("Location: buscar_livros.php?status=fail");
        exit();
    }
}

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
        :root {
            --primary-color: #00796b;
            --primary-dark: #004d40;
            --secondary-color: #26a69a;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --background-gradient: linear-gradient(135deg, #f8f9fa, #e9ecef);
            --card-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background: var(--background-gradient);
            font-family: 'Arial', sans-serif;
            color: #212121;
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        .page-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
        }

        .card-body {
            padding: 2rem;
        }

        .search-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.8rem 1.2rem;
            border: 2px solid #e0e0e0;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 121, 107, 0.25);
        }

        .btn-search {
            background-color: var(--primary-color);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            border: none;
            transition: var(--transition);
        }

        .btn-search:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .livro-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: var(--transition);
            border: 1px solid #eee;
            display: flex;
            align-items: start;
            gap: 1.5rem;
        }

        .livro-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .livro-capa {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        .livro-capa:hover {
            transform: scale(1.05);
        }

        .livro-info {
            flex: 1;
        }

        .livro-titulo {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .livro-autor {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .livro-isbn {
            font-size: 0.9rem;
            color: #888;
        }

        .checkbox-wrapper {
            position: relative;
            margin-top: 1rem;
        }

        .custom-checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid var(--primary-color);
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
        }

        .custom-checkbox:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert-custom {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: bold;
            animation: slideDown 0.5s ease;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .modal-footer {
            border-top: none;
            padding: 1.5rem;
        }

        .btn-modal {
            padding: 0.8rem 2rem;
            border-radius: 10px;
            transition: var(--transition);
        }

        .btn-modal:hover {
            transform: translateY(-2px);
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .livro-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .livro-capa {
                width: 100px;
                height: 150px;
                margin-bottom: 1rem;
            }

            .checkbox-wrapper {
                margin-top: 0.5rem;
            }

            .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 1.5rem;
            }

            .search-form {
                padding: 1rem;
            }

            .btn-search {
                width: 100%;
                margin-top: 1rem;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
                color: #ffffff;
            }

            .card,
            .search-form,
            .livro-card {
                background-color: #333333;
                color: #ffffff;
            }

            .form-control {
                background-color: #404040;
                border-color: #555555;
                color: #ffffff;
            }

            .livro-titulo {
                color: #26a69a;
            }

            .livro-autor,
            .livro-isbn {
                color: #cccccc;
            }

            .alert-custom {
                background-color: #404040;
                color: #ffffff;
            }
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="container">
            <h1 class="text-center mb-0">
                <i class="fas fa-book"></i> Sistema de Busca e Cadastro de Livros
            </h1>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_GET['status'])): ?>
            <div class="alert-custom <?php echo $_GET['status'] == 'success' ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $_GET['status'] == 'success' ?
                    '<i class="fas fa-check-circle"></i> Livros cadastrados com sucesso!' :
                    '<i class="fas fa-exclamation-circle"></i> Erro ao cadastrar livros.'; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0">
                    <i class="fas fa-search"></i> Buscar Livros
                </h2>
            </div>
            <div class="card-body">
                <form method="GET" action="buscar_livros.php" class="search-form">
                    <div class="row align-items-end">
                        <div class="col-md-9">
                            <label for="termo_busca" class="form-label">
                                <i class="fas fa-keyboard"></i> Digite o ISBN ou Título do livro
                            </label>
                            <input type="text" class="form-control" name="termo_busca" id="termo_busca"
                                placeholder="Ex: 9788535902775 ou Dom Casmurro" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-search w-100">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>

                <div id="loading" class="loading-spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Buscando livros...</p>
                </div>

                <?php if (isset($livros) && isset($livros['items'])): ?>
                    <form id="formCadastro" method="POST" action="buscar_livros.php">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3>Resultados da Pesquisa</h3>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#confirmacaoModal" id="btnCadastrar" disabled>
                                <i class="fas fa-plus-circle"></i> Cadastrar Selecionados
                            </button>
                        </div>

                        <?php foreach ($livros['items'] as $livro): ?>
                            <?php
                            $id = $livro['id'];
                            $titulo = $livro['volumeInfo']['title'] ?? 'Título Desconhecido';
                            $autores = isset($livro['volumeInfo']['authors']) ? implode(', ', $livro['volumeInfo']['authors']) : 'Autor Desconhecido';
                            $isbn = isset($livro['volumeInfo']['industryIdentifiers'][0]['identifier']) ? $livro['volumeInfo']['industryIdentifiers'][0]['identifier'] : 'ISBN Desconhecido';
                            $capa_url = $livro['volumeInfo']['imageLinks']['thumbnail'] ?? 'img/sem_capa.png';
                            ?>
                            <div class="livro-card">
                                <img src="<?php echo $capa_url; ?>" alt="Capa do livro <?php echo htmlspecialchars($titulo); ?>"
                                    class="livro-capa">
                                <div class="livro-info">
                                    <h4 class="livro-titulo"><?php echo htmlspecialchars($titulo); ?></h4>
                                    <p class="livro-autor">
                                        <i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($autores); ?>
                                    </p>
                                    <p class="livro-isbn">
                                        <i class="fas fa-barcode"></i> ISBN: <?php echo htmlspecialchars($isbn); ?>
                                    </p>
                                    <div class="checkbox-wrapper">
                                        <input type="checkbox" name="livros[]" value="<?php echo $id; ?>"
                                            class="custom-checkbox" onchange="verificarSelecao()">
                                        <label class="ms-2">Selecionar para cadastro</label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </form>
                <?php elseif (isset($livros)): ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i> Nenhum livro encontrado.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação -->
    <div class="modal fade" id="confirmacaoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle"></i> Confirmar Cadastro
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja cadastrar os livros selecionados?</p>
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-modal" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success btn-modal" id="confirmarCadastro">
                        <i class="fas fa-check"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Habilitar/desabilitar botão de cadastro baseado na seleção
        function verificarSelecao() {
            const checkboxes = document.querySelectorAll('input[name="livros[]"]');
            const btnCadastrar = document.getElementById('btnCadastrar');
            const selecionados = Array.from(checkboxes).some(cb => cb.checked);
            btnCadastrar.disabled = !selecionados;
        }

        // Mostrar loading durante a busca
        document.querySelector('form[action="buscar_livros.php"]').addEventListener('submit', function () {
            document.getElementById('loading').style.display = 'block';
        });

        // Confirmar cadastro
        document.getElementById('confirmarCadastro').addEventListener('click', function () {
            document.getElementById('formCadastro').submit();
        });

        // Animação suave ao scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>