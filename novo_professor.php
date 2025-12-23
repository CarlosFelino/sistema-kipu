<?php
session_start();
require 'conexao.php';

// 1. SEGURANÇA: Apenas logados entram
if (!isset($_SESSION['id_professor'])) { 
    header("Location: login.php"); 
    exit; 
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpeza básica
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $matricula = $_POST['matricula'];
    $senha = $_POST['senha']; 

    // --- VALIDAÇÕES FATEC ---
    
    // 1. Validação do domínio (PHP 8+)
    if (!str_ends_with($email, '@fatec.sp.gov.br')) {
        $msg = "<div class='alerta erro'>O e-mail deve ser institucional (@fatec.sp.gov.br).</div>";
    } 
    // 2. Validação da Matrícula
    elseif (strlen($matricula) < 4 || strlen($matricula) > 5) {
        $msg = "<div class='alerta erro'>A matrícula deve ter entre 4 e 5 dígitos.</div>";
    } 
    else {
        try {
            // 3. Verifica se a matrícula é permitida
            // Se você quiser liberar o cadastro para qualquer matrícula, apague este bloco IF/ELSE
            $check = $pdo->prepare("SELECT id FROM matriculas_permitidas WHERE numero_matricula = :m");
            $check->bindValue(':m', $matricula);
            $check->execute();
            
            if ($check->rowCount() > 0) {
                
                // SUCESSO: Inserir Professor
                // Nota: Mantendo senha em texto puro para compatibilidade com o login atual.
                // Futuramente, use: password_hash($senha, PASSWORD_DEFAULT)
                $sql = "INSERT INTO professores (nome, email, matricula, senha) VALUES (:n, :e, :m, :s)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':n', $nome);
                $stmt->bindValue(':e', $email);
                $stmt->bindValue(':m', $matricula);
                $stmt->bindValue(':s', $senha);
                $stmt->execute();
                
                $msg = "<div class='alerta sucesso'>Professor cadastrado com sucesso!</div>";

            } else {
                $msg = "<div class='alerta erro'>Esta matrícula não consta na lista de autorizados pela coordenação.</div>";
            }

        } catch (PDOException $e) {
            // Código 1062 = Duplicate entry (Chave única violada)
            if ($e->errorInfo[1] == 1062) {
                $msg = "<div class='alerta erro'>E-mail ou Matrícula já cadastrados no sistema.</div>";
            } else {
                $msg = "<div class='alerta erro'>Erro técnico: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Professor - Kipu</title>
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
    </style>
</head>
<body>

    <div class="form-card">
        <a href="painel.php" class="voltar">← Voltar ao Painel</a>
        
        <h2>Cadastrar Novo Docente</h2>
        
        <?php echo $msg; ?>
        
        <form method="POST">
            <label>Nome Completo:</label>
            <input type="text" name="nome" required autocomplete="off">

            <label>E-mail Institucional (@fatec.sp.gov.br):</label>
            <input type="email" name="email" placeholder="nome@fatec.sp.gov.br" required autocomplete="off">

            <label>Matrícula (4 a 5 dígitos):</label>
            <input type="number" name="matricula" placeholder="Ex: 1234" required>

            <label>Senha Inicial:</label>
            <input type="password" name="senha" required>

            <button type="submit" class="btn-salvar">Cadastrar</button>
        </form>
    </div>

</body>
</html>