<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_professor'])) { header("Location: login.php"); exit; }

$id = $_SESSION['id_professor'];
$msg = '';

// 1. Buscar dados atuais para preencher o formulário
$stmt = $pdo->prepare("SELECT * FROM professores WHERE id = :id");
$stmt->bindValue(':id', $id);
$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Processar alteração
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $nova_senha = $_POST['senha'];

    // Lógica da senha: Se digitou algo, atualiza. Se não, mantém a velha.
    if (!empty($nova_senha)) {
        $sql = "UPDATE professores SET nome = :n, email = :e, senha = :s WHERE id = :id";
    } else {
        $sql = "UPDATE professores SET nome = :n, email = :e WHERE id = :id";
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':n', $nome);
        $stmt->bindValue(':e', $email);
        $stmt->bindValue(':id', $id);
        if (!empty($nova_senha)) {
            $stmt->bindValue(':s', $nova_senha);
        }
        $stmt->execute();
        
        // Atualiza a sessão com o novo nome
        $_SESSION['nome_professor'] = $nome;
        $msg = "<div class='sucesso'>Dados atualizados!</div>";
        
        // Atualiza os dados na variável para mostrar no form
        $dados['nome'] = $nome;
        $dados['email'] = $email;

    } catch (PDOException $e) {
        $msg = "<div class='erro'>Erro ao atualizar: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; padding: 20px; }
        .card { background: white; max-width: 500px; margin: 0 auto; padding: 30px; border-radius: 8px; }
        input { width: 100%; padding: 10px; margin: 10px 0; }
        .btn { width: 100%; padding: 10px; background: #2c3e50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Editar Meus Dados</h3>
        <?php echo $msg; ?>
        <form method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo $dados['nome']; ?>" required>
            
            <label>E-mail:</label>
            <input type="email" name="email" value="<?php echo $dados['email']; ?>" required>
            
            <label>Nova Senha (deixe em branco para não mudar):</label>
            <input type="password" name="senha">
            
            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
        <br>
        <a href="painel.php">Voltar</a>
    </div>
</body>
</html>