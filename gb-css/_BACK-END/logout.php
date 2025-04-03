<?php
session_start();
session_destroy();  // Destrói a sessão
header("Location: ../FRONT-END/login.php");  // Redireciona para a página de login
exit();
?>
