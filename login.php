<?php
// 1. Iniciar a Sessão
// Isso é obrigatório sempre que formos lidar com login. 
// É como se o servidor desse uma "pulseirinha vip" para o navegador do usuário.
session_start();

require 'conexao.php';

$erro = ''; // Variável para guardar mensagens de erro, se houver

// 2. Verificar se o formulário foi enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Limpar os dados recebidos (segurança básica)
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    try {
        // 3. Buscar o usuário no banco pelo E-MAIL
        $sql = "SELECT * FROM professores WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 4. Verificar se achou o usuário e se a senha bate
        if ($usuario) {
            // COMPARAÇÃO SIMPLES (Para o nosso teste inicial funcionar)
            // Futuramente trocaremos por: if (password_verify($senha, $usuario['senha']))
            if ($senha == $usuario['senha']) {
                
                // SUCESSO!
                // Guardamos os dados na "pulseirinha" (Sessão)
                $_SESSION['id_professor'] = $usuario['id'];
                $_SESSION['nome_professor'] = $usuario['nome'];
                
                // Redireciona para o Painel Administrativo
                header('Location: painel.php');
                exit;
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "E-mail não encontrado.";
        }

    } catch (PDOException $e) {
        $erro = "Erro no sistema: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kipu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border-top: 5px solid #8B4513; /* Identidade Kipu */
        }

        h2 { color: #8B4513; margin-bottom: 20px; }
        
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Garante que o padding não estoure a largura */
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #8B4513;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        button:hover { background-color: #5e2f0d; }

        .erro { color: red; font-size: 0.9em; margin-bottom: 15px; }
        
        .voltar { display: block; margin-top: 15px; color: #666; text-decoration: none; font-size: 0.9em; }
        .voltar:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Acesso Kipu</h2>
        <p>Área restrita para docentes</p>

        <?php if(!empty($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Seu e-mail institucional" required>
            <input type="password" name="senha" placeholder="Sua senha" required>
            <button type="submit">Entrar</button>
        </form>

        <a href="index.php" class="voltar">← Voltar para a Home</a>
    </div>

</body>
</html>