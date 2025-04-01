<?php
session_start();
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit();
}

require '../conn.php'; // Conexão com o banco de dados

$aluno_id = $_SESSION['aluno_id'];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $serie = $_POST['serie'];
    $descricao = $_POST['descricao'];

    // Handle file upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Check if file type is allowed
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Caminho relativo do diretório onde o arquivo será movido
            $uploadFileDir = __DIR__ . '/../../_uploads/fp_alunos/';  // Caminho absoluto dinâmico
            $dest_path = $uploadFileDir . $newFileName;

            // Garantir que o diretório de uploads existe
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true); // Criar o diretório se não existir
            }

            // Mover o arquivo para o diretório de uploads
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $foto_perfil = 'fp_alunos/' . $newFileName; // Caminho relativo ao diretório web
            } else {
                die('There was an error moving the uploaded file. Please try again.');
            }
        } else {
            die('Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions));
        }
    } else {
        // If no new file is uploaded, keep the existing one
        $foto_perfil = $_POST['existing_foto'] ?? null;
    }

    // Update the database
    $sql = "UPDATE alunos SET nome = ?, email = ?, serie = ?, descricao = ?, foto_perfil = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("sssssi", $nome, $email, $serie, $descricao, $foto_perfil, $aluno_id);
    if ($stmt->execute() === false) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }

    // Redirect back to the profile page
    header("Location: perfil.php");
    exit();
}
?>
