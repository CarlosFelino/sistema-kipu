<?php
session_start();
require 'conexao.php';

// Apenas logados entram
if (!isset($_SESSION['id_professor'])) { header("Location: login.php"); exit; }

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $matricula = $_POST['matricula'];
    $senha = $_POST['senha']; // Lembre-se: idealmente usaríamos password_hash()

    // --- VALIDAÇÕES FATEC ---
    
    // 1. Validação do domínio de e-mail (PHP 8+)
    // Se usar PHP antigo, use: substr($email, -17) !== '@fatec.sp.gov.br'
    if (!str_ends_with($email, '@fatec.sp.gov.br')) {
        $msg = "<div class='erro'>O e-mail deve ser institucional (@fatec.sp.gov.br).</div>";
    } 
    // 2. Validação da Matrícula (4 a 5 dígitos)
    elseif (strlen($matricula) < 4 || strlen($matricula) > 5) {
        $msg = "<div class='erro'>A matrícula deve ter entre 4 e 5 dígitos.</div>";
    } 
    else {
        // Tenta cadastrar
        try {
            // Verificar se a matrícula está na lista de permitidas (regra inicial do sistema)
            // Se você quiser pular essa checagem e confiar no admin criando, pode remover este bloco.
            $check = $pdo->prepare("SELECT id FROM matriculas_permitidas WHERE numero_matricula = :m");
            $check->bindValue(':m', $matricula);
            $check->execute();
            
            if ($check->rowCount() > 0) {
                // Inserir Professor
                $sql = "INSERT INTO professores (nome, email, matricula, senha) VALUES (:n, :e, :m, :s)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':n', $nome);
                $stmt->bindValue(':e', $email);
                $stmt->bindValue(':m', $matricula);
                $stmt->bindValue(':s', $senha);
                $stmt->execute();
                
                $msg = "<div class='sucesso'>Professor cadastrado com sucesso!</div>";
            } else {
                $msg = "<div class='erro'>Esta matrícula não consta na lista de autorizados pela coordenação.</div>";
            }

        } catch (PDOException $e) {
            // Pega erro de duplicidade (e-mail ou matrícula já cadastrados)
            if ($e->errorInfo[1] == 1062) {
                $msg = "<div class='erro'>E-mail ou Matrícula já cadastrados no sistema.</div>";
            } else {
                $msg = "<div class='erro'>Erro: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Professor - Kipu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; padding: 20px; }
        .card { background: white; max-width: 500px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; padding: 10px; background: #8B4513; color: white; border: none; cursor: pointer; border-radius: 4px; }
        .erro { color: #721c24; background: #f8d7da; padding: 10px; margin-bottom: 10px; border-radius: 4px; }
        .sucesso { color: #155724; background: #d4edda; padding: 10px; margin-bottom: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Cadastrar Novo Docente</h3>
        <?php echo $msg; ?>
        <form method="POST">
            <label>Nome Completo:</label>
            <input type="text" name="nome" required>

            <label>E-mail Fatec:</label>
            <input type="email" name="email" placeholder="nome@fatec.sp.gov.br" required>

            <label>Matrícula (4-5 dígitos):</label>
            <input type="number" name="matricula" placeholder="Ex: 1234" required>

            <label>Senha Inicial:</label>
            <input type="password" name="senha" required>

            <button type="submit" class="btn">Cadastrar</button>
        </form>
        <br>
        <a href="painel.php">Voltar ao Painel</a>
    </div>
</body>
</html>