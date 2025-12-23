<?php
session_start(); // Precisa iniciar para poder destruir
session_destroy(); // Destrói todas as variáveis (id, nome, etc)
header("Location: login.php"); // Redireciona para o login
exit;
?>
