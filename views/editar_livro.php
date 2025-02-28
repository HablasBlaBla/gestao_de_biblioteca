<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o professor está logado
if (!isset($_SESSION['professor'])) {
    header("Location: login_professor.php");
    exit();
}

$erro = "";
$sucesso = "";

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    header("Location: listar_livros.php");
    exit();
}

$id = $_GET['id'];

// Busca o livro pelo ID
$stmt = $conn->prepare("SELECT nomeLivro, nomeAutor FROM livro WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$livro = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeLivro = $_POST['nomeLivro'];
    $nomeAutor = $_POST['nomeAutor'];

    if (!empty($nomeLivro) && !empty($nomeAutor)) {
        $stmt = $conn->prepare("UPDATE livro SET nomeLivro = ?, nomeAutor = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nomeLivro, $nomeAutor, $id);

        if ($stmt->execute()) {
            $sucesso = "Livro atualizado com sucesso!";
        } else {
            $erro = "Erro ao atualizar o livro!";
        }
    } else {
        $erro = "Todos os campos são obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Livro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Editar Livro</h2>
    <a href="listar_livros.php">Voltar</a>

    <?php 
    if ($erro) echo "<p style='color:red;'>$erro</p>"; 
    if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; 
    ?>

    <form method="POST">
        <label>Nome do Livro:</label>
        <input type="text" name="nomeLivro" value="<?php echo $livro['nomeLivro']; ?>" required>
        <br>
        <label>Autor:</label>
        <input type="text" name="nomeAutor" value="<?php echo $livro['nomeAutor']; ?>" required>
        <br>
        <button type="submit">Atualizar</button>
    </form>
</body>
</html>
