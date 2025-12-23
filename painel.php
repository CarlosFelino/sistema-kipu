<?php
session_start();
require 'conexao.php';

// --- 1. SEGURANÇA (A Sentinela) ---
// Se não existir a variável 'id_professor' na sessão, significa que não logou.
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit;
}

// Pega os dados do professor logado para usar na tela
$id_professor = $_SESSION['id_professor'];
$nome_professor = $_SESSION['nome_professor'];

// --- 2. BUSCAR OS ARTIGOS DESTE PROFESSOR ---
try {
    // Note o WHERE: Só pegamos artigos onde o id_professor_postou é igual ao id de quem está logado
    $sql = "SELECT * FROM artigos WHERE id_professor_postou = :id ORDER BY data_publicacao DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id_professor);
    $stmt->execute();
    
    $meus_artigos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Kipu</title>
    <style>
        /* Layout Flexbox: Menu na esquerda, Conteúdo na direita */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #f4f4f9; display: flex; height: 100vh; }
        
        /* Barra Lateral (Sidebar) */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .sidebar h2 { margin-bottom: 40px; color: #d2691e; /* Cor Cobre/Kipu */ }
        .sidebar a {
            color: #bdc3c7;
            text-decoration: none;
            padding: 15px 0;
            border-bottom: 1px solid #34495e;
            transition: 0.3s;
        }
        .sidebar a:hover { color: white; padding-left: 10px; }
        .sidebar .sair { margin-top: auto; color: #e74c3c; border: none; }
        
        /* Conteúdo Principal */
        .content { flex-grow: 1; padding: 40px; overflow-y: auto; }
        
        .header-content { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-novo {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        
        /* Tabela de Artigos */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #8B4513; color: white; }
        tr:hover { background-color: #f1f1f1; }
        
        .status-vazio { text-align: center; padding: 40px; color: #777; font-style: italic; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Kipu Admin</h2>
    <p>Olá, <?php echo htmlspecialchars($nome_professor); ?></p>
    
    <a href="painel.php">📄 Meus TGs</a>
    <a href="cadastrar-tg.php">➕ Novo TG</a>
    <a href="novo_professor.php">🎓 Cadastrar Professor</a>
    <a href="meu_perfil.php">👤 Meu Perfil</a>
    
    <a href="logout.php" class="sair">🚪 Sair</a>
</div>

    <div class="content">
        <div class="header-content">
            <h1>Gestão de Artigos</h1>
            <a href="cadastrar.php" class="btn-novo">+ Publicar Novo Artigo</a>
        </div>

        <?php if (count($meus_artigos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Orientador</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($meus_artigos as $artigo): ?>
                        <tr>
                            <td>#<?php echo $artigo['id']; ?></td>
                            <td><?php echo htmlspecialchars($artigo['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($artigo['nome_orientador']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></td>
                            <td>
                                <a href="<?php echo $artigo['caminho_ficheiro']; ?>" target="_blank" title="Ver PDF">👁️</a>
                                &nbsp;
                                
                                <a href="editar_tg.php?id=<?php echo $artigo['id']; ?>" title="Editar">✏️</a>
                                &nbsp;
                                
                                <a href="excluir_tg.php?id=<?php echo $artigo['id']; ?>" onclick="return confirm('Tem certeza que deseja apagar este TG e o arquivo PDF?');" style="color:red;" title="Excluir">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="status-vazio">
                Você ainda não publicou nenhum artigo no Kipu. <br>
                Clique no botão acima para começar.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>