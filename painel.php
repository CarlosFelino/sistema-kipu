<?php
session_start();
require 'conexao.php';

// --- 1. SEGURANÇA ---
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit;
}

$id_professor = $_SESSION['id_professor'];
$nome_professor = $_SESSION['nome_professor'];

// --- 2. BUSCAR ARTIGOS ---
try {
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
        /* --- ESTILOS GERAIS (Igual ao anterior) --- */
        :root {
            --bg-body: #f4f4f9;
            --bg-sidebar: #102939;
            --bg-table-head: #183646;
            --accent-green: #5b755c;
            --accent-gold: #b2b18d;
            --text-white: #ffffff;
            --text-dark: #373f45;
            --danger: #d63031;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; background-color: var(--bg-body); display: flex; height: 100vh; overflow: hidden; 
        }
        
        /* Sidebar e Conteúdo (Mantidos iguais) */
        .sidebar { width: 260px; background-color: var(--bg-sidebar); color: var(--text-white); display: flex; flex-direction: column; padding: 25px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); flex-shrink: 0; }
        .sidebar h2 { margin-top: 0; margin-bottom: 10px; font-size: 1.8em; letter-spacing: 1px; }
        .sidebar .user-info { font-size: 0.9em; color: var(--accent-gold); margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a { color: #bdc3c7; text-decoration: none; padding: 12px 15px; margin-bottom: 5px; border-radius: 6px; transition: all 0.3s; display: block; font-size: 0.95em; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--accent-green); color: white; padding-left: 20px; }
        .sidebar .sair { margin-top: auto; background-color: rgba(255,255,255,0.1); color: #fff; text-align: center; }
        .sidebar .sair:hover { background-color: var(--danger); }
        
        .content { flex-grow: 1; padding: 40px; overflow-y: auto; }
        .header-content { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e0e0e0; padding-bottom: 15px; }
        .header-content h1 { color: var(--text-dark); font-size: 1.8em; margin: 0; }
        .btn-novo { background-color: var(--accent-green); color: white; padding: 12px 25px; text-decoration: none; border-radius: 30px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s, background 0.3s; }
        .btn-novo:hover { background-color: #4a614b; transform: translateY(-2px); }
        
        /* Tabela */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        th, td { padding: 18px 20px; text-align: left; border-bottom: 1px solid #eee; font-size: 0.95em; }
        th { background-color: var(--bg-table-head); color: white; font-weight: 600; text-transform: uppercase; font-size: 0.85em; letter-spacing: 0.5px; }
        tr:hover { background-color: #f8f9fa; }
        .action-btn { display: inline-flex; justify-content: center; align-items: center; width: 35px; height: 35px; border-radius: 50%; text-decoration: none; font-size: 1.1em; transition: background 0.2s; margin-right: 5px; cursor: pointer; border:none;}
        .btn-view { background-color: #e3f2fd; color: #1e88e5; }
        .btn-edit { background-color: #fff3e0; color: #f57c00; }
        .btn-delete { background-color: #ffebee; color: #c62828; }

        /* --- ESTILOS DO MODAL (NOVO) --- */
        .modal-overlay {
            display: none; /* Começa invisível */
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fundo escuro transparente */
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(3px); /* Efeito de desfoque no fundo */
        }

        .modal-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            border-top: 5px solid var(--danger);
            animation: descida 0.3s ease-out;
        }

        @keyframes descida {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-box h3 { margin-top: 0; color: var(--text-dark); font-size: 1.5em; }
        .modal-box p { color: #666; margin-bottom: 25px; line-height: 1.5; }

        .modal-actions { display: flex; justify-content: center; gap: 15px; }

        .btn-modal { padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 1em; text-decoration: none;}
        .btn-cancelar { background-color: #e0e0e0; color: #333; }
        .btn-cancelar:hover { background-color: #d6d6d6; }
        
        .btn-confirmar { background-color: var(--danger); color: white; }
        .btn-confirmar:hover { background-color: #b02324; }

    </style>
</head>
<body>

<div class="sidebar">
    <h2>Kipu Admin</h2>
    <div class="user-info">Olá, <?php echo htmlspecialchars($nome_professor); ?></div>
    
    <a href="painel.php">📄 Meus TGs</a>
    <a href="cadastrar_tg.php">➕ Novo TG</a>
    
    <a href="meu_perfil.php">👤 Meu Perfil</a>
    
    <div style="margin-top: 20px; margin-bottom: 10px; font-size: 0.8em; color: #666; padding-left: 15px; text-transform: uppercase;">Administração</div>
    
    <a href="novo_professor.php">🎓 Novo Professor</a>
    <a href="logout.php" class="sair">🚪 Sair do Sistema</a>
</div>

    <div class="content">
        <div class="header-content">
            <h1>Gestão de Artigos</h1>
            <a href="cadastrar_tg.php" class="btn-novo">+ Publicar Novo TG</a>
        </div>

        <?php if (count($meus_artigos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Título do Trabalho</th>
                        <th>Orientador</th>
                        <th>Data Publicação</th>
                        <th width="150" style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($meus_artigos as $artigo): ?>
                        <tr>
                            <td>#<?php echo $artigo['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($artigo['titulo']); ?></strong></td>
                            <td><?php echo htmlspecialchars($artigo['nome_orientador']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($artigo['data_publicacao'])); ?></td>
                            <td style="text-align: center;">
                                <a href="<?php echo htmlspecialchars($artigo['caminho_ficheiro']); ?>" target="_blank" class="action-btn btn-view" title="Ver PDF">👁️</a>
                                <a href="editar_tg.php?id=<?php echo $artigo['id']; ?>" class="action-btn btn-edit" title="Editar">✏️</a>
                                
                                <button onclick="abrirModal(<?php echo $artigo['id']; ?>)" class="action-btn btn-delete" title="Excluir">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; color: #999;">
                <h3>Nenhum Trabalho Encontrado</h3>
                <p>Você ainda não publicou nenhum artigo no sistema Kipu.</p>
                <a href="cadastrar_tg.php" class="btn-novo">Começar Agora</a>
            </div>
        <?php endif; ?>
    </div>

    <div id="modalExclusao" class="modal-overlay">
        <div class="modal-box">
            <h3>Tem certeza?</h3>
            <p>Você está prestes a excluir este Trabalho de Graduação e o arquivo PDF associado. <br><strong>Esta ação não pode ser desfeita.</strong></p>
            
            <div class="modal-actions">
                <button onclick="fecharModal()" class="btn-modal btn-cancelar">Cancelar</button>
                <a id="linkExclusao" href="#" class="btn-modal btn-confirmar">Sim, Excluir</a>
            </div>
        </div>
    </div>

    <script>
        function abrirModal(idArtigo) {
            // 1. Pega o elemento do modal
            const modal = document.getElementById('modalExclusao');
            
            // 2. Pega o botão "Sim, Excluir" do modal
            const link = document.getElementById('linkExclusao');
            
            // 3. Atualiza o link para apontar para o ID correto
            // Isso cria o link dinâmico: excluir_tg.php?id=10, excluir_tg.php?id=11, etc.
            link.href = 'excluir_tg.php?id=' + idArtigo;
            
            // 4. Mostra o modal (muda display de none para flex)
            modal.style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('modalExclusao').style.display = 'none';
        }

        // Fecha o modal se clicar fora da caixinha (opcional)
        window.onclick = function(event) {
            const modal = document.getElementById('modalExclusao');
            if (event.target == modal) {
                fecharModal();
            }
        }
    </script>

</body>
</html>