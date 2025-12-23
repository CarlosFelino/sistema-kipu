<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_professor'])) { header("Location: login.php"); exit; }

// Verifica se veio o ID na URL (ex: excluir_tg.php?id=5)
if (isset($_GET['id'])) {
    $id_artigo = $_GET['id'];
    $id_professor = $_SESSION['id_professor'];

    try {
        // 1. Primeiro, buscamos o caminho do arquivo
        // E garantimos que o artigo pertence a quem está tentando excluir (WHERE id_professor_postou...)
        $stmt = $pdo->prepare("SELECT caminho_ficheiro FROM artigos WHERE id = :id AND id_professor_postou = :id_prof");
        $stmt->bindValue(':id', $id_artigo);
        $stmt->bindValue(':id_prof', $id_professor);
        $stmt->execute();
        $artigo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($artigo) {
            // 2. Apagar o arquivo físico (unlink)
            if (file_exists($artigo['caminho_ficheiro'])) {
                unlink($artigo['caminho_ficheiro']);
            }

            // 3. Apagar o registro do banco
            $del = $pdo->prepare("DELETE FROM artigos WHERE id = :id");
            $del->bindValue(':id', $id_artigo);
            $del->execute();
        }

    } catch (PDOException $e) {
        // Apenas silencia ou loga erro
    }
}

// Volta para o painel
header("Location: painel.php");
exit;
?>