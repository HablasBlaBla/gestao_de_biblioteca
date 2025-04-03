<?php
include('../_BACK-END/excluir_livro.php')
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Livro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="_css/excluir_livro.css">
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
