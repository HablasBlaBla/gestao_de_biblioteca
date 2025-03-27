<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Deletar aluno
if (isset($_GET['deletar'])) {
    $aluno_id = $_GET['deletar'];

    // Deletando o aluno
    $sql = "DELETE FROM alunos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $aluno_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success fade show' role='alert'>
                Aluno deletado com sucesso!
              </div>";
    } else {
        echo "<div class='alert alert-danger fade show' role='alert'>
                Erro ao deletar aluno!
              </div>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Alunos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
    background: linear-gradient(135deg, #f0f4f8, #e0e7ff); /* Fundo suave */
    font-family: 'Arial', sans-serif;
    color: #212121;
}

.container {
    margin-top: 40px;
}

.card {
    border-radius: 15px; /* Aumentar o raio da borda */
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1); /* Sombra mais suave */
}

.card-header {
    background-color: #00796b; /* Cor de fundo do cabeçalho */
    color: white;
    border-radius: 15px 15px 0 0; /* Aumentar o raio da borda */
    padding: 1rem; /* Adicionar padding */
}

.table-hover tbody tr:hover {
    background-color: #e0f2f1; /* Cor de fundo ao passar o mouse */
    transition: background-color 0.3s ease; /* Transição suave */
}

.btn-primary {
    background-color: #00796b; /* Cor do botão primário */
    border-color: #00796b;
    padding: 12px 20px; /* Adicionar padding */
    border-radius: 8px; /* Raio da borda */
    transition: all 0.3s ease; /* Transição suave */
}

.btn-primary:hover {
    background-color: #004d40; /* Cor ao passar o mouse */
    border-color: #004d40; /* Cor da borda ao passar o mouse */
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2); /* Sombra ao passar o mouse */
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    padding: 12px 20px; /* Adicionar padding */
    border-radius: 8px; /* Raio da borda */
    transition: all 0.3s ease; /* Transição suave */
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #c82333;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2); /* Sombra ao passar o mouse */
}
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-users"></i> Lista de Alunos</h4>
                    <a href="cadastro_aluno.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i> Cadastrar Aluno
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php
                // Exibindo a lista de alunos cadastrados
                require 'conn.php';
                $sql = "SELECT id, nome, serie, email FROM alunos";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table class='table table-hover'>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Série</th>
                                    <th>Email</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['id'] . "</td>
                                <td>" . $row['nome'] . "</td>
                                <td>" . $row['serie'] . "</td>
                                <td>" . $row['email'] . "</td>
                                <td>
                                    <a href='?deletar=" . $row['id'] . "' class='btn btn-danger btn-sm'>
                                        <i class='fas fa-trash-alt'></i> Deletar
                                    </a>
                                </td>
                            </tr>";
                    }

                    echo "</tbody></table>";
                } else {
                    echo "<p>Nenhum aluno cadastrado.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>
        <br>
                <a href="dashboard.php" class="btn btn-primary w-100" id="voltaDashboardId">Voltar para o Painel</a>
                <br>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
