<?php
session_start();  // Inicia a sessão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../conn.php'; // Arquivo de conexão com o banco

    $email = $_POST['email'];
    $senha = md5($_POST['senha']); // Criptografando a senha com MD5

    // Consultando o banco para verificar se o professor existe
    $sql = "SELECT * FROM professores WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $professor = $resultado->fetch_assoc();
        $_SESSION['professor_id'] = $professor['id'];  // Armazenando a sessão do professor
        $_SESSION['nome'] = $professor['nome'];  // Armazenando o nome do professor
        header("Location: dashboard.php");  // Redireciona para a página principal
        exit();
    } else {
        $erro = "Email ou senha incorretos!";
    }

    $stmt->close();
    $conn->close();
}
?>