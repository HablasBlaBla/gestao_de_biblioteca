<?php
session_start();
require 'conn.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];

// Registrar empréstimo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['livro_id']) && isset($_POST['aluno_id'])) {
    $livro_id = $_POST['livro_id'];
    $aluno_id = $_POST['aluno_id'];
    $data_emprestimo = date("Y-m-d");
    $data_devolucao = date("Y-m-d", strtotime("+7 days")); // Definindo devolução para 7 dias

    // Verificar se o livro está disponível
    $sql = "SELECT quantidade FROM livros WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $livro_id);
    $stmt->execute();
    $stmt->bind_result($quantidade);
    $stmt->fetch();
    
    if ($quantidade > 0) {
        // Inserir novo empréstimo
        $stmt->close();
        $sql = "INSERT INTO emprestimos (livro_id, aluno_id, professor_id, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $livro_id, $aluno_id, $professor_id, $data_emprestimo, $data_devolucao);
        if ($stmt->execute()) {
            // Atualizar a quantidade do livro
            $sql = "UPDATE livros SET quantidade = quantidade - 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $livro_id);
            $stmt->execute();
            echo "<div class='alert alert-success'>Empréstimo registrado com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao registrar empréstimo.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-warning'>Livro indisponível para empréstimo.</div>";
    }
}

// Marcar como devolvido
if (isset($_GET['devolver_id'])) {
    $emprestimo_id = $_GET['devolver_id'];

    // Atualizar a devolução no banco
    $sql = "SELECT livro_id FROM emprestimos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emprestimo_id);
    $stmt->execute();
    $stmt->bind_result($livro_id);
    $stmt->fetch();
    
    // Marcar como devolvido
    $sql = "DELETE FROM emprestimos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emprestimo_id);
    if ($stmt->execute()) {
        // Atualizar a quantidade de livros
        $sql = "UPDATE livros SET quantidade = quantidade + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $livro_id);
        $stmt->execute();
        echo "<div class='alert alert-success'>Livro devolvido com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger'>Erro ao devolver livro.</div>";
    }
    $stmt->close();
}

// Obter lista de empréstimos
$professor_id = 1; // Aqui você pode definir o ID do professor, ou obter de uma sessão, por exemplo

$sql = "SELECT e.id, l.titulo, a.nome, e.data_emprestimo, e.data_devolucao 
        FROM emprestimos e 
        JOIN livros l ON e.livro_id = l.id
        JOIN alunos a ON e.aluno_id = a.id
        WHERE e.professor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $professor_id); // 'i' é para um inteiro (ID do professor)
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Empréstimos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Gerenciar Empréstimos</h2>
        <hr>

        <!-- Formulário para registrar empréstimo -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4>Registrar Novo Empréstimo</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="livro_id" class="form-label">Livro</label>
                        <select class="form-select" name="livro_id" required>
                            <?php
                            // Exibe todos os livros disponíveis para empréstimo
                            $sql = "SELECT id, titulo FROM livros WHERE quantidade > 0";
                            $livros = $conn->query($sql);
                            while ($livro = $livros->fetch_assoc()) {
                                echo "<option value='" . $livro['id'] . "'>" . $livro['titulo'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="aluno_id" class="form-label">Aluno</label>
                        <select class="form-select" name="aluno_id" required>
                            <?php
                                $sql = "SELECT id, nome_completo FROM alunos";
                                $alunos = $conn->query($sql);
                                
                                if ($alunos->num_rows > 0) {
                                    while ($aluno = $alunos->fetch_assoc()) {
                                        echo "<option value='" . $aluno['id'] . "'>" . $aluno['nome_completo'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>Nenhum aluno encontrado</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrar Empréstimo</button>
                </form>
            </div>
        </div>

        <!-- Lista de Empréstimos -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4>Empréstimos Registrados</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Aluno</th>
                            <th>Data Empréstimo</th>
                            <th>Data Devolução</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['titulo']; ?></td>
                                <td><?php echo $row['nome_completo']; ?></td>
                                <td><?php echo $row['data_emprestimo']; ?></td>
                                <td><?php echo $row['data_devolucao']; ?></td>
                                <td>
                                    <a href="?devolver_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Devolver</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>

<?php $conn->close(); ?>
