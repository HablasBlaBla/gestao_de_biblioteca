<?php
// Função para validar login
function validarLogin($email, $senha, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        return $usuario;
    }
    
    return null;
}

// Função para gerar hash da senha
function gerarHashSenha($senha) {
    return password_hash($senha, PASSWORD_BCRYPT);
}
?>
