<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Conexão com o banco

// Função para buscar livro na API do Google pelo ISBN
function buscarLivroGoogleByISBN($isbn) {
    $url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . urlencode($isbn);
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['items'][0])) {
        return $data['items'][0];
    }
    return null;
}

// Função para buscar livro na API do Google pelo nome
function buscarLivroGoogleByNome($nome) {
    $url = 'https://www.googleapis.com/books/v1/volumes?q=' . urlencode($nome);
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['items'])) {
        return $data['items'];
    }
    return [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['livros'])) {
    $livros_selecionados = $_POST['livros'];

    foreach ($livros_selecionados as $livro_id) {
        $livro = buscarLivroGoogleByISBN($livro_id);

        if ($livro) {
            $titulo = $livro['volumeInfo']['title'] ?? 'Sem título';
            $autor = implode(', ', $livro['volumeInfo']['authors'] ?? ['Desconhecido']);
            $descricao = $livro['volumeInfo']['description'] ?? 'Sem descrição';
            $isbn = $livro['volumeInfo']['industryIdentifiers'][0]['identifier'] ?? 'Não informado';
            $quantidade = 1; // Definindo quantidade inicial

            // Verifica se o ISBN já existe no banco
            $sql_check = "SELECT id FROM livros WHERE isbn = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $isbn);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                echo "<div class='alert alert-warning'>O livro <strong>$titulo</strong> já está cadastrado.</div>";
            } else {
                // Inserindo o livro no banco
                $sql = "INSERT INTO livros (titulo, autor, descricao, isbn, quantidade) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $titulo, $autor, $descricao, $isbn, $quantidade);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Livro <strong>$titulo</strong> cadastrado com sucesso!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Erro ao cadastrar o livro <strong>$titulo</strong>!</div>";
                }
            }
            $stmt_check->close();
        } else {
            echo "<div class='alert alert-danger'>Livro não encontrado para ID: $livro_id</div>";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function buscarLivro() {
            let input = document.getElementById('buscaLivro').value;
            let tipoBusca = document.querySelector('input[name="tipoBusca"]:checked').value;
            let resultadoDiv = document.getElementById('resultadoBusca');

            if (input.trim() === "") {
                resultadoDiv.innerHTML = "<p class='text-danger'>Digite um ISBN ou nome de livro.</p>";
                return;
            }

            fetch(`buscar_livros.php?tipo=${tipoBusca}&valor=${encodeURIComponent(input)}`)
                .then(response => response.json())
                .then(data => {
                    resultadoDiv.innerHTML = "";
                    if (data.length > 0) {
                        data.forEach(livro => {
                            resultadoDiv.innerHTML += `
                                <div class='form-check'>
                                    <input class='form-check-input' type='checkbox' name='livros[]' value='${livro.id}'>
                                    <label class='form-check-label'>${livro.titulo} - ${livro.autor}</label>
                                </div>`;
                        });
                    } else {
                        resultadoDiv.innerHTML = "<p class='text-warning'>Nenhum livro encontrado.</p>";
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar livro:', error);
                    resultadoDiv.innerHTML = "<p class='text-danger'>Erro ao buscar livros.</p>";
                });
        }
    </script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Cadastro de Livros</h4>
            </div>
            <div class="card-body">
                <a href="dashboard.php" class="btn btn-warning mb-3">Voltar ao Painel</a>

                <div class="mb-3">
                    <label for="buscaLivro" class="form-label">Buscar Livro</label>
                    <input type="text" id="buscaLivro" class="form-control" placeholder="Digite o ISBN ou nome do livro">
                    <div class="mt-2">
                        <input type="radio" name="tipoBusca" value="isbn" checked> ISBN
                        <input type="radio" name="tipoBusca" value="nome"> Nome
                    </div>
                    <button type="button" class="btn btn-primary mt-2" onclick="buscarLivro()">Buscar</button>
                </div>

                <form method="POST">
                    <div id="resultadoBusca"></div>
                    <button type="submit" class="btn btn-success mt-3 w-100">Cadastrar Selecionados</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
