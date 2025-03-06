<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Verifica se o ID foi passado pela URL
if (!isset($_GET['id'])) {
    header("Location: visualizar_livros.php");
    exit();
}

$id = $_GET['id'];

// Exclui o livro do banco de dados
$sql = "DELETE FROM livros WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<div class='alert alert-success'>Livro excluído com sucesso!</div>";
} else {
    echo "<div class='alert alert-danger'>Erro ao excluir o livro!</div>";
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Livro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Excluir Livro</h4>
            </div>
            <div class="card-body">
                <p class="text-center">O livro foi excluído com sucesso.</p>
                <a href="visualizar_livros.php" class="btn btn-primary w-100">Voltar</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
