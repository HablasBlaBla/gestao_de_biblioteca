<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/conn.php';

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