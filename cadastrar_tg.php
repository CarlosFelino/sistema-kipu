<?php
session_start();
require 'conexao.php';

// 1. SEGURANÇA
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit;
}

$mensagem = ''; 

// 2. PROCESSAMENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $titulo = $_POST['titulo'];
    $alunos = $_POST['alunos'];
    $orientador = $_POST['orientador'];
    $id_professor = $_SESSION['id_professor'];

    // Verifica/Cria a pasta uploads se ela não existir
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // 3. UPLOAD
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === UPLOAD_ERR_OK) {
        
        $arquivo = $_FILES['arquivo_pdf'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        
        if ($extensao !== 'pdf') {
            $mensagem = "<div class='alerta erro'>Erro: Apenas arquivos PDF são permitidos!</div>";
        } else {
            // Gera nome único: 20231223_hash_nome.pdf
            $novo_nome = date("Ymd_His") . "_" . uniqid() . "." . $extensao;
            $pasta_destino = 'uploads/' . $novo_nome;

            if (move_uploaded_file($arquivo['tmp_name'], $pasta_destino)) {
                
                // 4. BANCO DE DADOS
                try {
                    $sql = "INSERT INTO artigos (titulo, nomes_alunos, nome_orientador, caminho_ficheiro, id_professor_postou) 
                            VALUES (:titulo, :alunos, :orientador, :caminho, :id_prof)";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':titulo', $titulo);
                    $stmt->bindValue(':alunos', $alunos);
                    $stmt->bindValue(':orientador', $orientador);
                    $stmt->bindValue(':caminho', $pasta_destino);
                    $stmt->bindValue(':id_prof', $id_professor);
                    
                    $stmt->execute();
                    
                    $mensagem = "<div class='alerta sucesso'>Trabalho cadastrado com sucesso!</div>";

                } catch (PDOException $e) {
                    $mensagem = "<div class='alerta erro'>Erro no Banco: " . $e->getMessage() . "</div>";
                }

            } else {
                $mensagem = "<div class='alerta erro'>Erro ao salvar o arquivo na pasta. Verifique permissões.</div>";
            }
        }
    } else {
        $mensagem = "<div class='alerta erro'>Selecione um arquivo PDF válido.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar TG - Kipu</title>
    <style>
        :root {
            --bg-body: #f4f4f9;
            --primary-blue: #102939; /* Azul Profundo */
            --accent-green: #5b755c; /* Verde Folha */
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
            max-width: 600px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-top: 5px solid var(--primary-blue);
        }
        
        /* Cabeçalho do Form */
        h2 { 
            color: var(--primary-blue); 
            margin-top: 0; 
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        /* Inputs */
        label { 
            display: block; 
            margin-top: 20px; 
            font-weight: 600; 
            color: var(--primary-blue); 
            font-size: 0.95em;
        }
        
        input[type="text"], input[type="file"] { 
            width: 100%; 
            padding: 12px; 
            margin-top: 8px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            box-sizing: border-box;
            font-size: 1em;
            outline: none;
            transition: border 0.3s;
        }

        input[type="text"]:focus {
            border-color: var(--accent-green);
        }
        
        /* Botão Salvar */
        .btn-salvar {
            background-color: var(--accent-green); 
            color: white; 
            border: none; 
            padding: 15px; 
            width: 100%; 
            margin-top: 30px; 
            font-size: 16px; 
            font-weight: bold;
            cursor: pointer; 
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn-salvar:hover { 
            background-color: var(--hover-blue); 
        }
        
        /* Mensagens de Feedback */
        .alerta { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: 500;}
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;}
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
        
        /* Botão Voltar */
        .voltar { 
            display: inline-block; 
            margin-bottom: 20px; 
            color: #999; 
            text-decoration: none; 
            font-size: 0.9em;
            transition: color 0.2s;
        }
        .voltar:hover { color: var(--primary-blue); text-decoration: underline;}

        /* Pequeno texto de ajuda */
        small { display: block; margin-top: 5px; color: #777; font-style: italic;}

    </style>
</head>
<body>

    <div class="form-card">
        <a href="painel.php" class="voltar">← Voltar ao Painel</a>
        
        <h2>Publicar Novo Trabalho</h2>
        
        <?php echo $mensagem; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            
            <label>Título do Trabalho:</label>
            <input type="text" name="titulo" required placeholder="Ex: Desenvolvimento de Sistema Web..." autocomplete="off">
            
            <label>Integrantes (Alunos):</label>
            <input type="text" name="alunos" required placeholder="Ex: Ana Silva, Bruno Souza..." autocomplete="off">
            
            <label>Nome do Orientador:</label>
            <input type="text" name="orientador" required placeholder="Ex: Prof. Me. Carlos..." autocomplete="off">
            
            <label>Arquivo do TG (PDF):</label>
            <input type="file" name="arquivo_pdf" accept="application/pdf" required>
            <small>Tamanho máximo recomendado: 10MB.</small>

            <button type="submit" class="btn-salvar">💾 Publicar TG</button>
        </form>
    </div>

</body>
</html>