<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco de dados

$professor_id = $_SESSION['professor_id'];

// Variáveis de erro e sucesso
$erro = $sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = $_POST['aluno_id'];
    $mensagem = $_POST['mensagem'];
    $data_envio = date('Y-m-d H:i:s'); // Data e hora atual
    
    // Validando e processando o upload de imagem, se houver
    $imagem_url = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        // Caminho onde a imagem será salva
        $diretorio = "_uploads/";
        $arquivo_nome = $_FILES['imagem']['name'];
        $arquivo_tmp = $_FILES['imagem']['tmp_name'];
        $extensao = pathinfo($arquivo_nome, PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $extensao;
        
        // Verifica se a imagem é do tipo permitido
        if (in_array(strtolower($extensao), ['jpg', 'jpeg', 'png', 'gif'])) {
            // Move o arquivo para o diretório de uploads
            if (move_uploaded_file($arquivo_tmp, $diretorio . $novo_nome)) {
                $imagem_url = $diretorio . $novo_nome;
            } else {
                $erro = "Erro ao enviar a imagem!";
            }
        } else {
            $erro = "Formato de imagem inválido!";
        }
    }

    // Valida o conteúdo da mensagem
    if (empty($mensagem)) {
        $erro = "A mensagem não pode estar vazia!";
    } else {
        // Adiciona links detectados na mensagem (substitui URLs por links HTML)
        $mensagem = preg_replace("/(http:\/\/|https:\/\/)([a-zA-Z0-9\-\._~\/?#[\]@!$&'()*+,;=]*[a-zA-Z0-9\-\._~\/?#[\]@!$&'()*+,;=])/", "<a href='$0' target='_blank'>$0</a>", $mensagem);

        // Insere a mensagem no banco de dados
        $sql = "INSERT INTO mensagens (aluno_id, professor_id, mensagem, data_envio, imagem_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $aluno_id, $professor_id, $mensagem, $data_envio, $imagem_url);

        if ($stmt->execute()) {
            $sucesso = "Mensagem enviada com sucesso!";
        } else {
            $erro = "Erro ao enviar mensagem!";
        }

        $stmt->close();
    }
}

// Busca todos os alunos para exibir no formulário
$sql_alunos = "SELECT id, nome FROM alunos";
$result_alunos = $conn->query($sql_alunos);

// Fecha a conexão depois de terminar todas as operações
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensagem - Professor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h2>Enviar Mensagem</h2>

        <?php if (!empty($erro)) { ?>
            <div class="alert alert-danger" role="alert"><?php echo $erro; ?></div>
        <?php } ?>

        <?php if (!empty($sucesso)) { ?>
            <div class="alert alert-success" role="alert"><?php echo $sucesso; ?></div>
        <?php } ?>

        <form action="enviar_mensagem.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="aluno_id" class="form-label">Selecionar Aluno</label>
                <select class="form-select" id="aluno_id" name="aluno_id" required>
                    <option value="" disabled selected>Escolha um aluno</option>
                    <?php while ($aluno = $result_alunos->fetch_assoc()) { ?>
                        <option value="<?php echo $aluno['id']; ?>"><?php echo $aluno['nome']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="mensagem" class="form-label">Mensagem</label>
                <textarea class="form-control" id="mensagem" name="mensagem" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Enviar Imagem (opcional)</label>
                <input class="form-control" type="file" id="imagem" name="imagem">
            </div>
            <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
