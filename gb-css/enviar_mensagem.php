<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php';

$professor_id = $_SESSION['professor_id'];

$erro = $sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = $_POST['aluno_id'];
    $mensagem = $_POST['mensagem'];
    $data_envio = date('Y-m-d H:i:s');
    
    $imagem_url = "";
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $diretorio = "_uploads/";
        $arquivo_nome = $_FILES['imagem']['name'];
        $arquivo_tmp = $_FILES['imagem']['tmp_name'];
        $extensao = pathinfo($arquivo_nome, PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $extensao;
        
        if (in_array(strtolower($extensao), ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($arquivo_tmp, $diretorio . $novo_nome)) {
                $imagem_url = $diretorio . $novo_nome;
            } else {
                $erro = "Erro ao enviar a imagem!";
            }
        } else {
            $erro = "Formato de imagem inválido!";
        }
    }

    if (empty($mensagem)) {
        $erro = "A mensagem não pode estar vazia!";
    } else {
        $mensagem = preg_replace("/(http:\/\/|https:\/\/)([a-zA-Z0-9\-\._~\/?#[\]@!$&'()*+,;=]*[a-zA-Z0-9\-\._~\/?#[\]@!$&'()*+,;=])/", "<a href='$0' target='_blank'>$0</a>", $mensagem);

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

$sql_alunos = "SELECT id, nome FROM alunos";
$result_alunos = $conn->query($sql_alunos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensagem - Professor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #00796b;
            --primary-dark: #004d40;
            --background-gradient: linear-gradient(135deg, #f0f4f8, #e0e7ff);
            --card-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background: var(--background-gradient);
            font-family: 'Arial', sans-serif;
            color: #212121;
        }

        .container {
            margin-top: 50px;
        }

        .alert {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
        }

        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background: #1a1a1a;
                color: #ffffff;
            }
            .alert {
                background-color: #333;
                color: #fff;
            }
            .btn-primary {
                background-color: #005f4f;
            }
            .btn-primary:hover {
                background-color: #004d40;
            }
        }
    </style>
</head>
<body>

    <div class="container">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
