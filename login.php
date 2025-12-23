<?php
// 1. Iniciar a Sessão
session_start();

require 'conexao.php';

$erro = ''; // Variável para guardar mensagens de erro

// 2. Verificar se o formulário foi enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Limpar os dados recebidos
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
            // COMPARAÇÃO SIMPLES (Para teste)
            if ($senha == $usuario['senha']) {
                
                // SUCESSO!
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
        /* Reset e Fundo */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f4f4f9; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            color: #373f45; /* Cinza Escuro */
        }
        
        /* Cartão de Login */
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 380px;
            text-align: center;
            border-top: 5px solid #183646; /* Azul Kipu */
        }

        /* Tipografia */
        h2 { 
            color: #183646; /* Azul Kipu */
            margin-bottom: 10px; 
            font-size: 1.8em;
        }

        .subtitle {
            color: #999c94; /* Cinza Claro da paleta */
            margin-top: 0;
            margin-bottom: 30px;
            font-size: 0.95em;
        }
        
        /* Campos do Formulário */
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
            outline: none;
            transition: border 0.3s;
        }

        input:focus {
            border-color: #5b755c; /* Foco Verde */
        }

        /* Botão de Ação */
        button {
            width: 100%;
            padding: 12px;
            background-color: #5b755c; /* Verde da Logo */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 10px;
        }

        button:hover { 
            background-color: #183646; /* Azul ao passar o mouse */
        }

        /* Mensagens e Links */
        .erro { 
            background-color: #ffe6e6;
            color: #d63031; 
            padding: 10px;
            border-radius: 4px;
            font-size: 0.9em; 
            margin-bottom: 15px;
            border-left: 4px solid #d63031;
        }
        
        .voltar { 
            display: block; 
            margin-top: 25px; 
            color: #999c94; /* Cinza Claro */
            text-decoration: none; 
            font-size: 0.9em; 
            transition: color 0.3s;
        }
        .voltar:hover { 
            color: #183646; 
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Acesso Kipu</h2>
        <p class="subtitle">Área restrita para docentes</p>

        <?php if(!empty($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="E-mail institucional" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>

        <a href="index.php" class="voltar">← Voltar para a Home</a>
    </div>

</body>
</html>