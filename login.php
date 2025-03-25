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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Professores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e0f7fa; /* Fundo suave */
            font-family: Arial, sans-serif;
            color: #212121;
        }
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #006064; /* Azul mais escuro */
            font-size: 24px;
        }
        .form-label {
            font-size: 1.1rem;
            font-weight: bold;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #004d40;
            box-shadow: 0 0 8px rgba(0, 100, 64, 0.4);
        }
        .btn-custom {
            background-color: #004d40;
            color: white;
            font-size: 1.1rem;
            padding: 12px 20px;
            border-radius: 8px;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #00332d;
            transition: background-color 0.3s ease;
        }
        .alert-custom {
            margin-top: 20px;
            background-color: #c8e6c9;
            color: #388e3c;
            padding: 15px;
            border-radius: 8px;
        }
        .hover-effect:hover {
            background-color: #004d40;
            color: white;
            transition: all 0.3s;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Login de Professores</h2>
            <?php if (isset($erro)) { echo "<div class='alert alert-danger'>$erro</div>"; } ?>
            <form method="POST">
                <div class="mb-4">
                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" class="form-control" name="email" id="email" required aria-label="Email" placeholder="Digite seu email">
                </div>
                <div class="mb-4">
                    <label for="senha" class="form-label"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" class="form-control" name="senha" id="senha" required aria-label="Senha" placeholder="Digite sua senha">
                </div>
                <button type="submit" class="btn btn-custom">Entrar</button>
            </form>
            <p class="text-center mt-4">Ainda não tem uma conta? <a href="cp.php" class="hover-effect">Cadastre-se!</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



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
