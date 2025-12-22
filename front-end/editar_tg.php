<?php
session_start();
require 'conexao.php';
if (!isset($_SESSION['id_professor'])) { header("Location: login.php"); exit; }

$id_artigo = $_GET['id'];
$id_professor = $_SESSION['id_professor'];
$msg = '';

// Buscar dados atuais
$stmt = $pdo->prepare("SELECT * FROM artigos WHERE id = :id AND id_professor_postou = :id_prof");
$stmt->bindValue(':id', $id_artigo);
$stmt->bindValue(':id_prof', $id_professor);
$stmt->execute();
$artigo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$artigo) { die("Artigo não encontrado ou sem permissão."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $alunos = $_POST['alunos'];
    $orientador = $_POST['orientador'];

    $sql = "UPDATE artigos SET titulo = :t, nomes_alunos = :a, nome_orientador = :o WHERE id = :id";
    $upd = $pdo->prepare($sql);
    $upd->bindValue(':t', $titulo);
    $upd->bindValue(':a', $alunos);
    $upd->bindValue(':o', $orientador);
    $upd->bindValue(':id', $id_artigo);
    
    if ($upd->execute()) {
        $msg = "<div style='color:green'>Atualizado com sucesso! <a href='painel.php'>Voltar</a></div>";
        // Atualiza a visualização
        $artigo['titulo'] = $titulo;
        $artigo['nomes_alunos'] = $alunos;
        $artigo['nome_orientador'] = $orientador;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head><title>Editar TG</title></head>
<body style="font-family: sans-serif; padding: 20px;">
    <h3>Editar Informações do TG</h3>
    <?php echo $msg; ?>
    <form method="POST">
        <p>Título: <br><input type="text" name="titulo" value="<?php echo htmlspecialchars($artigo['titulo']); ?>" style="width:300px"></p>
        <p>Alunos: <br><input type="text" name="alunos" value="<?php echo htmlspecialchars($artigo['nomes_alunos']); ?>" style="width:300px"></p>
        <p>Orientador: <br><input type="text" name="orientador" value="<?php echo htmlspecialchars($artigo['nome_orientador']); ?>" style="width:300px"></p>
        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>