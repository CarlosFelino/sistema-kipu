<?php
// 1. Incluir a conexão
require 'conexao.php';

try {
    // 2. Buscar artigos (Do mais recente para o mais antigo)
    $sql = "SELECT * FROM artigos ORDER BY data_publicacao DESC";
    $stmt = $pdo->query($sql);
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
        /* --- VARIÁVEIS DE CORES (Baseado na sua lista) --- */
        :root {
            --bg-body: #f4f4f9;
            --bg-header: #102939;    /* Azul Profundo */
            --bg-card: #ffffff;
            
            --primary-blue: #183646; /* Azul Médio */
            --hover-blue: #264350;   /* Azul ligeiramente mais claro para hover */
            
            --accent-green: #5b755c; /* Verde Folha */
            --accent-gold: #b2b18d;  /* Bege/Dourado */
            
            --text-dark: #373f45;    /* Cinza Escuro */
            --text-light: #999c94;   /* Cinza Claro */
            --text-white: #ffffff;
        }

        /* --- RESET E GERAL --- */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg-body); 
            margin: 0; 
            padding: 0; 
            color: var(--text-dark); 
        }
        
        a { text-decoration: none; }

        /* --- CABEÇALHO --- */
        header { 
            background-color: var(--bg-header); 
            color: var(--text-white); 
            padding: 15px 30px; 
            display: flex;
            align-items: center;
            justify-content: space-between; /* Espalha os itens */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
        }

        /* Área da Logo (Centralizada) */
        .logo-container {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .logo-container h1 { 
            margin: 0; 
            font-size: 2em; 
            letter-spacing: 2px; 
            color: var(--text-white);
        }
        
        .logo-container p {
            margin: 0;
            font-size: 0.8em;
            color: var(--accent-gold); /* Detalhe na cor bege */
            font-style: italic;
        }

        /* Área de Navegação (Botões à direita) */
        .nav-buttons {
            margin-left: auto; /* Empurra para a direita se não usar position absolute */
            z-index: 10;
        }

        .btn-login {
            background-color: var(--accent-green);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: transparent;
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

        /* --- CONTAINER PRINCIPAL --- */
        .container { 
            max-width: 1200px; 
            margin: 40px auto; 
            padding: 0 20px; 
        }
        
        .section-title {
            border-bottom: 2px solid var(--accent-gold);
            padding-bottom: 10px;
            margin-bottom: 30px;
            color: var(--primary-blue);
        }

        /* --- GRID DE ARTIGOS (As Caixinhas) --- */
        .articles-grid {
            display: grid;
            /* Isso cria colunas automáticas. minmax(300px, 1fr) significa:
               "O cartão deve ter no mínimo 300px. Se couber mais, divida o espaço (1fr)" */
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px; /* Espaço entre as caixinhas */
        }
        
        /* --- CARTÃO DO ARTIGO --- */
        .card { 
            background: var(--bg-card); 
            border-radius: 8px; 
            padding: 25px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            border-top: 5px solid var(--primary-blue); /* Detalhe no topo */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px); /* Efeito de "levantar" ao passar o mouse */
        }
        
        .card h2 { 
            margin-top: 0; 
            font-size: 1.4em;
            color: var(--primary-blue); 
            margin-bottom: 15px;
        }
        
        .meta-info { 
            font-size: 0.9em; 
            color: var(--text-light); 
            margin-bottom: 20px; 
            line-height: 1.6;
        }
        
        .meta-info strong { 
            color: var(--accent-green); 
        }
        
        /* Botão de Download dentro do card */
        .btn-download {
            text-align: center;
            display: block;
            background-color: var(--primary-blue);
            color: white;
            padding: 12px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s;
            margin-top: auto; /* Empurra o botão para o final do card */
        }
        
        .btn-download:hover { 
            background-color: var(--hover-blue); 
        }

        /* --- RODAPÉ --- */
        footer { 
            text-align: center; 
            padding: 30px; 
            background-color: var(--text-dark);
            color: var(--text-light); 
            font-size: 0.85em; 
            margin-top: 13.75em; 
        }
    </style>
</head>
<body>

    <header>
        <div style="width: 100px;"></div> 

        <div class="logo-container">
            <h1>Kipu</h1>
        </div>

        <nav class="nav-buttons">
            <a href="login.php" class="btn-login">Login</a>
        </nav>
    </header>

    <div class="container">
        
        <h3 class="section-title">Últimas Publicações</h3>

        <div class="articles-grid">
            <?php if (count($artigos) > 0): ?>
                
                <?php foreach($artigos as $artigo): ?>
                    <div class="card">
                        <div>
                            <h2><?php echo htmlspecialchars($artigo['titulo']); ?></h2>
                            
                            <div class="meta-info">
                                <p><strong>Autores:</strong><br> <?php echo htmlspecialchars($artigo['nomes_alunos']); ?></p>
                                <p><strong>Orientador:</strong> <?php echo htmlspecialchars($artigo['nome_orientador']); ?></p>
                                <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></p>
                            </div>
                        </div>

                        <a href="<?php echo htmlspecialchars($artigo['caminho_ficheiro']); ?>" class="btn-download" target="_blank">
                            Baixar PDF
                        </a>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <p>Ainda não há artigos cadastrados.</p>
            <?php endif; ?>
        </div> </div>

    <footer>
        Sistema Kipu &copy; <?php echo date('Y'); ?> - Fatec Carapicuíba
    </footer>

</body>
</html>