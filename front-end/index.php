<?php
// 1. Incluir a conexão (A "ponte" que criamos antes)
require 'conexao.php';

try {
    // 2. Preparar a consulta SQL
    // Queremos todos os artigos, ordenados do mais recente para o mais antigo
    $sql = "SELECT * FROM artigos ORDER BY data_publicacao DESC";
    
    // 3. Executar a consulta
    $stmt = $pdo->query($sql);
    
    // 4. Buscar os resultados e guardar numa variável (array)
    // FETCH_ASSOC significa que os dados vêm associados pelo nome da coluna (ex: $artigo['titulo'])
    $artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro ao buscar artigos: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kipu - Repositório Acadêmico</title>
    <style>
        /* ESTILOS (CSS) - A "Pele" do site */
        
        /* Reset básico e fontes */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; color: #333; }
        
        /* Cabeçalho com a identidade Kipu */
        header { background-color: #8B4513; /* Cor "Terra/Corda" */ color: white; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 2.5em; letter-spacing: 2px; }
        header p { margin: 5px 0 0; font-style: italic; opacity: 0.9; }
        
        /* Área principal */
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        
        /* Cartão do Artigo */
        .card { 
            background: white; 
            border-radius: 8px; 
            padding: 20px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            border-left: 5px solid #d2691e; /* Detalhe visual lembrando um fio/corda */
        }
        
        .card h2 { margin-top: 0; color: #2c3e50; }
        
        .meta-info { color: #666; font-size: 0.9em; margin-bottom: 15px; }
        .meta-info strong { color: #8B4513; }
        
        /* Botão de Download */
        .btn-download {
            display: inline-block;
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn-download:hover { background-color: #1a252f; }

        /* Rodapé */
        footer { text-align: center; padding: 20px; color: #777; font-size: 0.8em; margin-top: 40px; }
    </style>
</head>
<body>

    <header>
        <h1>Kipu</h1>
        <p>Organizando o conhecimento complexo de forma simples.</p>
    </header>

    <div class="container">
        
        <h3>Artigos Publicados Recentemente</h3>

        <?php if (count($artigos) > 0): ?>
            
            <?php foreach($artigos as $artigo): ?>
                <div class="card">
                    <h2><?php echo htmlspecialchars($artigo['titulo']); ?></h2>
                    
                    <div class="meta-info">
                        <p><strong>Autores:</strong> <?php echo htmlspecialchars($artigo['nomes_alunos']); ?></p>
                        <p><strong>Orientador:</strong> <?php echo htmlspecialchars($artigo['nome_orientador']); ?></p>
                        <p><strong>Publicado em:</strong> <?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></p>
                    </div>

                    <a href="<?php echo htmlspecialchars($artigo['caminho_ficheiro']); ?>" class="btn-download" target="_blank">
                        📄 Baixar PDF Completo
                    </a>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p>Nenhum artigo encontrado no sistema Kipu.</p>
        <?php endif; ?>

    </div>

    <footer>
        Sistema Kipu &copy; <?php echo date('Y'); ?> - Fatec Carapicuíba
    </footer>

</body>
</html>