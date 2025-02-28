<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o aluno está logado
if (!isset($_SESSION['aluno'])) {
    header("Location: login_aluno.php");
    exit();
}

$alunoId = $_SESSION['aluno']['id'];

// Busca todos os livros disponíveis
$result = $conn->query("SELECT * FROM livro");

$erro = "";
$sucesso = "";

// Processa a solicitação de empréstimo
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $livroId = $_POST['livroId'];
    $dataRetirada = date("Y-m-d"); // Data atual

    if (!empty($livroId)) {
        // Registra o empréstimo
        $stmt = $conn->prepare("INSERT INTO emprestimo (alunoId, livroId, dataRetirada) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $alunoId, $livroId, $dataRetirada);

        if ($stmt->execute()) {
            $sucesso = "Empréstimo realizado com sucesso!";
        } else {
            $erro = "Erro ao registrar o empréstimo!";
        }
    } else {
        $erro = "Escolha um livro!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Livro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Escolher um Livro</h2>
    <a href="painel_aluno.php">Voltar</a>

    <?php 
    if ($erro) echo "<p style='color:red;'>$erro</p>"; 
    if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; 
    ?>

    <form method="POST">
        <label>Escolha um livro:</label>
        <select name="livroId">
            <option value="">Selecione um livro</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo $row['nomeLivro'] . " - " . $row['nomeAutor']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br>
        <button type="submit">Pegar Emprestado</button>
    </form>
</body>
</html>
