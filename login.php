<?php
session_start();  // Inicia a sessão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'conn.php'; // Arquivo de conexão com o banco

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

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #007bff, #28a745);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        .card-header {
            background: #007bff;
        }
        .btn-login {
            background: #28a745;
            border: none;
        }
        .btn-login:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card shadow-lg">
            <div class="card-header text-white text-center">
                <h4>Login de Professor</h4>
            </div>
            <div class="card-body">
                <?php if (isset($erro)) { echo "<div class='alert alert-danger text-center'>$erro</div>"; } ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" required autocomplete="off">
                    </div>
                    <button type="submit" class="btn btn-login w-100 text-white">Entrar</button>
                </form>
            </div>
        </div>
        <footer class="text-center text-white mt-3">
            <p>&copy; 2025 Biblioteca Escolar</p>
        </footer>
    </div>

    <!-- <script src="bloquear_devtools.js"></script> -->
    <script>
        // Impede o clique direito do mouse
document.addEventListener("contextmenu", (event) => event.preventDefault());

// Bloqueia atalhos para abrir o DevTools
document.addEventListener("keydown", (event) => {
    if (
        event.key === "F12" || 
        (event.ctrlKey && event.shiftKey && (event.key === "I" || event.key === "J" || event.key === "C")) || 
        (event.ctrlKey && event.key === "U") || 
        (event.altKey && event.key === "ArrowLeft") // Bloqueia ALT + Seta para voltar
    ) {
        event.preventDefault();
        bloquearAcesso();
    }
});

// Detecta se o DevTools está aberto
let devtoolsOpen = false;
const element = new Image();
Object.defineProperty(element, 'id', {
    get: function () {
        devtoolsOpen = true;
        bloquearAcesso();
    }
});

// Detecta mudanças no tamanho da tela (indicando DevTools aberto)
let prevWidth = window.innerWidth;
let prevHeight = window.innerHeight;

window.addEventListener("resize", () => {
    if (window.innerWidth < prevWidth || window.innerHeight < prevHeight) {
        bloquearAcesso();
    }
    prevWidth = window.innerWidth;
    prevHeight = window.innerHeight;
});

// Checa a cada 500ms se o DevTools foi aberto
setInterval(() => {
    devtoolsOpen = false;
    console.log(element); // Isso ativa a detecção do DevTools
    if (devtoolsOpen) {
        bloquearAcesso();
    }
}, 500);

// Impede que o usuário volte para a página anterior
window.history.pushState(null, "", window.location.href);
window.addEventListener("popstate", () => {
    bloquearAcesso();
});

// Função para bloquear completamente o acesso
function bloquearAcesso() {
    setInterval(() => {
        document.body.innerHTML = ""; // Apaga todo o conteúdo da página
        document.body.style.backgroundColor = "black"; // Tela preta
        document.title = "Acesso Bloqueado!";
    }, 10);

    window.location.href = "data:text/html,<h1 style='color: red; text-align: center;'>ACESSO BLOQUEADO!</h1>";

    while (true) {} // Loop infinito para travar o navegador
}

// Protege o campo de senha contra mudanças no HTML
document.addEventListener("DOMContentLoaded", () => {
    let senhaInput = document.querySelector('input[name="senha"]');
    if (senhaInput) {
        senhaInput.setAttribute("readonly", true);
        senhaInput.addEventListener("focus", () => {
            senhaInput.removeAttribute("readonly");
        });
    }
});

    </script>

</body>
</html>
