<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o admin está logado
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

$erro = "";
$sucesso = "";

// Processa o cadastro de livro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $ano = $_POST['ano'];
    $disponivel = 1; // Livro sempre começa disponível

    if (!empty($titulo) && !empty($autor) && !empty($ano)) {
        $stmt = $conn->prepare("INSERT INTO livro (titulo, autor, ano, disponivel) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $titulo, $autor, $ano, $disponivel);

        if ($stmt->execute()) {
            $sucesso = "Livro cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar o livro.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}

// Processa a exclusão de livro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir'])) {
    $livroId = $_POST['livroId'];

    $stmt = $conn->prepare("DELETE FROM livro WHERE id = ?");
    $stmt->bind_param("i", $livroId);

    if ($stmt->execute()) {
        $sucesso = "Livro excluído com sucesso!";
    } else {
        $erro = "Erro ao excluir o livro.";
    }
}

// Busca todos os livros
$result = $conn->query("SELECT * FROM livro");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Gerenciar Livros</h2>
    <a href="painel_admin.php">Voltar</a>

    <?php 
    if ($erro) echo "<p style='color:red;'>$erro</p>"; 
    if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; 
    ?>

    <h3>Cadastrar Livro</h3>
    <form method="POST">
        <label>Título:</label>
        <input type="text" name="titulo" required>
        <br>
        <label>Autor:</label>
        <input type="text" name="autor" required>
        <br>
        <label>Ano:</label>
        <input type="number" name="ano" required>
        <br>
        <button type="submit" name="cadastrar">Cadastrar</button>
    </form>

    <h3>Livros Cadastrados</h3>
    <table border="1">
        <tr>
            <th>Título</th>
            <th>Autor</th>
            <th>Ano</th>
            <th>Disponibilidade</th>
            <th>Ação</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['titulo']; ?></td>
                <td><?php echo $row['autor']; ?></td>
                <td><?php echo $row['ano']; ?></td>
                <td><?php echo $row['disponivel'] ? "Disponível" : "Emprestado"; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="livroId" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="excluir" onclick="return confirm('Tem certeza que deseja excluir este livro?')">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
