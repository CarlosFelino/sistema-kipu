<?php
// Definições do computador/servidor
$host = 'localhost'; // O endereço da base de dados (geralmente localhost)
$dbname = 'simgetec_portal'; // O nome que demos à base de dados no SQL
$user = 'root'; // Utilizador padrão do XAMPP/WAMP (pode variar)
$pass = ''; // Senha padrão (geralmente vazia no XAMPP)

try {
    // A tentar criar a ligação (PDO - PHP Data Objects)
    // O PDO é a forma mais segura e moderna de ligar, evita hackers (SQL Injection)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Configurar o PDO para nos avisar se houver erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Se não aparecer erro nenhum, a ligação foi um sucesso!
    // (Não precisamos de escrever nada aqui para não sujar o ecrã do utilizador)

} catch (PDOException $e) {
    // Se algo correr mal (ex: base de dados desligada), entra aqui
    echo "Erro na ligação: " . $e->getMessage();
    exit; // Para o código imediatamente
}
?>