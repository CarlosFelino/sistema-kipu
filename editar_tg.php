<?php
session_start();
require 'conexao.php';

// 1. SEGURANÇA
if (!isset($_SESSION['id_professor'])) { 
    header("Location: login.php"); 
    exit; 
}

$id_artigo = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$id_professor = $_SESSION['id_professor'];
$msg = '';

// 2. BUSCAR DADOS ATUAIS (Para preencher o formulário)
$stmt = $pdo->prepare("SELECT * FROM artigos WHERE id = :id AND id_professor_postou = :id_prof");
$stmt->bindValue(':id', $id_artigo);
$stmt->bindValue(':id_prof', $id_professor);
$stmt->execute();
$artigo = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não achou (ou se o artigo não é desse professor)
if (!$artigo) { 
    die("Artigo não encontrado ou você não tem permissão para editá-lo."); 
}

// 3. ATUALIZAÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $alunos = $_POST['alunos'];
    $orientador = $_POST['orientador'];

    // Nota: Por enquanto não estamos atualizando o PDF aqui, apenas os textos.
    $sql = "UPDATE artigos SET titulo = :t, nomes_alunos = :a, nome_orientador = :o WHERE id = :id";
    $upd = $pdo->prepare($sql);
    $upd->bindValue(':t', $titulo);
    $upd->bindValue(':a', $alunos);
    $upd->bindValue(':o', $orientador);
    $upd->bindValue(':id', $id_artigo);
    
    if ($upd->execute()) {
        $msg = "<div class='alerta sucesso'>Informações atualizadas com sucesso!</div>";
        
        // Atualiza a variável $artigo para o formulário mostrar os dados novos
        $artigo['titulo'] = $titulo;
        $artigo['nomes_alunos'] = $alunos;
        $artigo['nome_orientador'] = $orientador;
    } else {
        $msg = "<div class='alerta erro'>Erro ao atualizar dados.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar TG - Kipu</title>
    <style>
        /* --- REUTILIZANDO O ESTILO PADRÃO KIPU --- */
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
            max-width: 600px;
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
            margin-top: 20px; 
            font-weight: 600; 
            color: var(--primary-blue); 
            font-size: 0.95em;
        }
        
        input[type="text"] { 
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

        input[type="text"]:focus { border-color: var(--accent-green); }
        
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
        .btn-salvar:hover { background-color: var(--hover-blue); }
        
        /* Mensagens */
        .alerta { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: 500;}
        .sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;}
        .erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
        
        /* Links */
        .voltar { 
            display: inline-block; 
            margin-bottom: 20px; 
            color: #999; 
            text-decoration: none; 
            font-size: 0.9em;
            transition: color 0.2s;
        }
        .voltar:hover { color: var(--primary-blue); text-decoration: underline;}

        .info-file {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #b2b18d; /* Bege da logo */
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <div class="form-card">
        <a href="painel.php" class="voltar">← Voltar ao Painel</a>
        
        <h2>Editar Informações do TG</h2>
        
        <?php echo $msg; ?>

        <form method="POST">
            
            <label>Título do Trabalho:</label>
            <input type="text" name="titulo" value="<?php echo htmlspecialchars($artigo['titulo']); ?>" required>
            
            <label>Integrantes (Alunos):</label>
            <input type="text" name="alunos" value="<?php echo htmlspecialchars($artigo['nomes_alunos']); ?>" required>
            
            <label>Nome do Orientador:</label>
            <input type="text" name="orientador" value="<?php echo htmlspecialchars($artigo['nome_orientador']); ?>" required>

            <div class="info-file">
                <strong>Nota:</strong> Para alterar o arquivo PDF, exclua este registro e cadastre-o novamente.
            </div>

            <button type="submit" class="btn-salvar">💾 Salvar Alterações</button>
        </form>
    </div>

</body>
</html>