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

// Processa o cadastro de aluno
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $serie = $_POST['serie'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    if (!empty($nome) && !empty($email) && !empty($serie) && !empty($_POST['senha'])) {
        $stmt = $conn->prepare("INSERT INTO aluno (nome, email, serie, senha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $serie, $senha);

        if ($stmt->execute()) {
            $sucesso = "Aluno cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar o aluno.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}

// Processa a exclusão de aluno
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir'])) {
    $alunoId = $_POST['alunoId'];

    $stmt = $conn->prepare("DELETE FROM aluno WHERE id = ?");
    $stmt->bind_param("i", $alunoId);

    if ($stmt->execute()) {
        $sucesso = "Aluno excluído com sucesso!";
    } else {
        $erro = "Erro ao excluir o aluno.";
    }
}

// Busca todos os alunos
$result = $conn->query("SELECT * FROM aluno");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Alunos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Gerenciar Alunos</h2>
    <a href="painel_admin.php">Voltar</a>

    <?php 
    if ($erro) echo "<p style='color:red;'>$erro</p>"; 
    if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; 
    ?>

    <h3>Cadastrar Aluno</h3>
    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <br>
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Série:</label>
        <input type="text" name="serie" required>
        <br>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <br>
        <button type="submit" name="cadastrar">Cadastrar</button>
    </form>

    <h3>Alunos Cadastrados</h3>
    <table border="1">
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Série</th>
            <th>Ação</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['serie']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="alunoId" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="excluir" onclick="return confirm('Tem certeza que deseja excluir este aluno?')">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
