<?php
include('../_BACK-END/cadastro_professor_principal.php')
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="_css/cadastro_professor_principal.css">
    
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Cadastro de Professores</h2>
            <form method="POST">
                <div class="mb-4">
                    <label for="nome" class="form-label"><i class="fas fa-user"></i> Nome</label>
                    <input type="text" class="form-control" name="nome" id="nome" required aria-label="Nome" placeholder="Digite seu nome">
                </div>
                <div class="mb-4">
                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" class="form-control" name="email" id="email" required aria-label="Email" placeholder="Digite seu email">
                </div>
                <div class="mb-4">
                    <label for="cpf" class="form-label"><i class="fas fa-id-card"></i> CPF</label>
                    <input type="text" class="form-control" name="cpf" id="cpf" required aria-label="CPF" placeholder="Digite seu CPF">
                </div>
                <div class="mb-4">
                    <label for="senha" class="form-label"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" class="form-control" name="senha" id="senha" required aria-label="Senha" placeholder="Digite sua senha">
                </div>
                <button type="submit" class="btn btn-custom">Cadastrar</button>
            </form>
            <p class="text-center mt-4">Já tem uma conta? <a href="login.php" class="hover-effect">Faça login</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
