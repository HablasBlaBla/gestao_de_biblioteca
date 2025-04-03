<?php
include('../_BACK-END/cadastro_aluno.php')
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="_css/cadastro_aluno.css">
    <link rel="stylesheet" href="_css/theme.css">
    <script src="_static/theme.js"></script>
   
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex">
                    <h4><i class="fas fa-user-plus"></i> Cadastro de Aluno</h4>
                    <a href="lista_alunos.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-list"></i> Ver Lista de Alunos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="serie" class="form-label">Série</label>
                        <input type="text" class="form-control" name="serie" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Cadastrar
                    </button>
                </form>
            </div>
        </div>
        <br>
                <a href="dashboard.php" class="btn btn-primary w-100" id="voltaDashboardId">Voltar para o Painel</a>
                <br>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
