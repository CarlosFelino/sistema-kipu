<?php
session_start();
require 'conexao.php';

// 1. SEGURANÇA
if (!isset($_SESSION['id_professor'])) { 
    header("Location: login.php"); 
    exit; 
}

$id = $_SESSION['id_professor'];
$msg = '';

// 2. BUSCAR DADOS ATUAIS
$stmt = $pdo->prepare("SELECT * FROM professores WHERE id = :id");
$stmt->bindValue(':id', $id);
$stmt->execute();
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. PROCESSAR ALTERAÇÃO
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
        
        // Atualiza a sessão imediatamente para o "Olá, Fulano" mudar no topo
        $_SESSION['nome_professor'] = $nome;
        
        $msg = "<div class='alerta sucesso'>Dados atualizados com sucesso!</div>";
        
        // Atualiza a variável visual
        $dados['nome'] = $nome;
        $dados['email'] = $email;

    } catch (PDOException $e) {
        $msg = "<div class='alerta erro'>Erro ao atualizar: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Kipu</title>
    <style>
        /* --- ESTILOS PADRÃO KIPU --- */
        :root {
            --bg-body: #f4f4f9;
            --primary-blue: #102939; 
            --accent-green: #5b755c; 
            --hover-blue: #183646;
            --text-dark: #373f45;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg-body); 
            display: flex; 
            justify-content: center; 
            padding-top: 50px; 
            margin: 0;
            color: var(--text-dark);
        }
        
        .form-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-top: 5px solid var(--primary-blue);
        }
        
        h2 { 
            color: var(--primary-blue); 
            margin-top: 0; 
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        label { 
            display: block; 
            margin-top: 15px; 
            font-weight: 600; 
            color: var(--primary-blue); 
            font-size: 0.9em;
        }

        input { 
            width: 100%; 
            padding: 12px; 
            margin-top: 5px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            box-sizing: border-box; 
            font-size: 1em;
            outline: none;
            transition: border 0.3s;
        }
        
        input:focus { border-color: var(--accent-green); }

        .btn-salvar {
            background-color: var(--accent-green); 
            color: white; 
            border: none; 
            padding: 12px; 
            width: 100%; 
            margin-top: 25px; 
            font-size: 16px; 
            font-weight: bold;
            cursor: pointer; 
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn-salvar:hover { background-color: var(--hover-blue); }

        /* Mensagens */
        .alerta { padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9em; }
        .erro { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; }
        .sucesso { color: #155724; background: #d4edda; border: 1px solid #c3e6cb; }

        .voltar { display: inline-block; margin-bottom: 20px; color: #999; text-decoration: none; font-size: 0.9em; transition: color 0.2s; }
        .voltar:hover { color: var(--primary-blue); text-decoration: underline; }
        
        small { color: #777; font-size: 0.85em; }
    </style>
</head>
<body>

    <div class="form-card">
        <a href="painel.php" class="voltar">← Voltar ao Painel</a>
        
        <h2>Editar Meus Dados</h2>
        
        <?php echo $msg; ?>
        
        <form method="POST">
            <label>Nome Completo:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($dados['nome']); ?>" required>
            
            <label>E-mail:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($dados['email']); ?>" required>
            
            <label>Nova Senha:</label>
            <input type="password" name="senha" placeholder="Deixe em branco para manter a atual">
            <small>Só preencha se quiser alterar sua senha.</small>
            
            <button type="submit" class="btn-salvar">Salvar Alterações</button>
        </form>
    </div>

</body>
</html>