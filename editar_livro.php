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

// Busca os dados do livro a ser editado
$sql = "SELECT * FROM livros WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Livro não encontrado!</div>";
    exit();
}

$livro = $result->fetch_assoc();

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $ano_publicacao = $_POST['ano_publicacao'];
    $genero = $_POST['genero'];
    $isbn = $_POST['isbn'];
    $quantidade = $_POST['quantidade'];

    // Atualiza o livro no banco de dados
    $sql_update = "UPDATE livros SET titulo = ?, autor = ?, ano_publicacao = ?, genero = ?, isbn = ?, quantidade = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssssi", $titulo, $autor, $ano_publicacao, $genero, $isbn, $quantidade, $id);

    if ($stmt_update->execute()) {
        echo "<div class='alert alert-success'>Livro atualizado com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger'>Erro ao atualizar o livro!</div>";
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Livro</title>
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
            text-align: center;
        }
        .card-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
            border-radius: 10px;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border-radius: 10px;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            color: white;
        }
        .list-group-item {
            background-color: #ffffff;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .list-group-item:hover {
            background-color: #e0e0e0;
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-book"></i> Editar Livro</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" name="titulo" value="<?php echo $livro['titulo']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="autor" class="form-label">Autor</label>
                        <input type="text" class="form-control" name="autor" value="<?php echo $livro['autor']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="ano_publicacao" class="form-label">Ano de Publicação</label>
                        <input type="text" class="form-control" name="ano_publicacao" value="<?php echo $livro['ano_publicacao']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="genero" class="form-label">Gênero</label>
                        <input type="text" class="form-control" name="genero" value="<?php echo $livro['genero']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" name="isbn" value="<?php echo $livro['isbn']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade Disponível</label>
                        <input type="number" class="form-control" name="quantidade" value="<?php echo $livro['quantidade']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Atualizar Livro</button>
                </form>
            </div>
        </div>
        <a href="visualizar_livros.php" class="btn btn-secondary mt-3 w-100">Voltar</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
