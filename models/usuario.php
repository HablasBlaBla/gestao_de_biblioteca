<?php
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Função para cadastrar um novo usuário
    public function cadastrar($nome, $email, $senha, $perfil) {
        // Gera o hash da senha
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        
        // Insere o novo usuário no banco de dados
        $stmt = $this->pdo->prepare("INSERT INTO usuario (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senhaHash, $perfil]);
    }
}
?>
