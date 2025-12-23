<?php
session_start();
require 'conexao.php';

// 1. SEGURANÇA: Só professor logado entra aqui
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit;
}

$mensagem = ''; // Para avisar se deu certo ou errado

// 2. PROCESSAMENTO (Quando clica em "Salvar")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Pegar os textos
    $titulo = $_POST['titulo'];
    $alunos = $_POST['alunos'];
    $orientador = $_POST['orientador'];
    $id_professor = $_SESSION['id_professor']; // Quem está cadastrando

    // 3. O UPLOAD DO ARQUIVO
    // Verificamos se foi enviado um arquivo e se não deu erro no envio básico
    if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === UPLOAD_ERR_OK) {
        
        $arquivo = $_FILES['arquivo_pdf'];
        
        // A. Validar a extensão (Só aceitamos PDF)
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        
        if ($extensao !== 'pdf') {
            $mensagem = "<div class='erro'>Erro: Apenas arquivos PDF são permitidos!</div>";
        } else {
            // B. Gerar nome único
            // Usamos uniqid() com a data atual. Isso gera algo como: "20231025_6538ac_tcc.pdf"
            // Isso evita que dois alunos enviem "tcc.pdf" e um apague o outro.
            $novo_nome = date("Ymd_His") . "_" . uniqid() . "." . $extensao;
            $pasta_destino = 'uploads/' . $novo_nome;

            // C. Mover da pasta temporária para a pasta 'uploads'
            if (move_uploaded_file($arquivo['tmp_name'], $pasta_destino)) {
                
                // D. SUCESSO NO UPLOAD -> AGORA SALVA NO BANCO
                try {
                    $sql = "INSERT INTO artigos (titulo, nomes_alunos, nome_orientador, caminho_ficheiro, id_professor_postou) 
                            VALUES (:titulo, :alunos, :orientador, :caminho, :id_prof)";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':titulo', $titulo);
                    $stmt->bindValue(':alunos', $alunos);
                    $stmt->bindValue(':orientador', $orientador);
                    $stmt->bindValue(':caminho', $pasta_destino); // Salvamos o caminho "uploads/nome.pdf"
                    $stmt->bindValue(':id_prof', $id_professor);
                    
                    $stmt->execute();
                    
                    $mensagem = "<div class='sucesso'>TG cadastrado com sucesso!</div>";

                } catch (PDOException $e) {
                    $mensagem = "<div class='erro'>Erro no Banco: " . $e->getMessage() . "</div>";
                }

            } else {
                $mensagem = "<div class='erro'>Erro ao salvar o arquivo na pasta. Verifique as permissões.</div>";
            }
        }
    } else {
        $mensagem = "<div class='erro'>Selecione um arquivo PDF válido.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar TG - Kipu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; padding-top: 50px; }
        
        .form-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        h2 { color: #8B4513; margin-top: 0; }
        
        label { display: block; margin-top: 15px; font-weight: bold; color: #555; }
        input[type="text"], input[type="file"] { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        
        .btn-salvar {
            background-color: #27ae60; color: white; border: none; padding: 15px; width: 100%; margin-top: 25px; font-size: 16px; cursor: pointer; border-radius: 5px;
        }
        .btn-salvar:hover { background-color: #219150; }
        
        .sucesso { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb;}
        .erro { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c6cb;}
        
        .voltar { display: inline-block; margin-bottom: 20px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

    <div class="form-card">
        <a href="painel.php" class="voltar">← Voltar ao Painel</a>
        
        <h2>Novo Trabalho de Graduação</h2>
        
        <?php echo $mensagem; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            
            <label>Título do Trabalho:</label>
            <input type="text" name="titulo" required placeholder="Ex: Sistema de Gestão Kipu">
            
            <label>Integrantes (Alunos):</label>
            <input type="text" name="alunos" required placeholder="Ex: Ana Silva, Bruno Souza">
            
            <label>Nome do Orientador:</label>
            <input type="text" name="orientador" required placeholder="Ex: Prof. Dr. Fulano">
            
            <label>Arquivo do TG (PDF):</label>
            <input type="file" name="arquivo_pdf" accept="application/pdf" required>
            <small style="color: #888;">Apenas formato .PDF</small>

            <button type="submit" class="btn-salvar">💾 Publicar TG</button>
        </form>
    </div>

</body>
</html>