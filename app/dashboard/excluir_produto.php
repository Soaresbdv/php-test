<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

if (!isset($_GET['id'])) {
    header("Location: meus_produtos.php");
    exit;
}

$pdo = getDB();
$produto_id = $_GET['id'];

// Verificar se o produto pertence ao usuário logado
$stmt = $pdo->prepare("SELECT id_usuario FROM php_bd.produtos WHERE id_camiseta = ?");
$stmt->execute([$produto_id]);
$produto = $stmt->fetch();

if (!$produto) {
    $_SESSION['error'] = "Produto não encontrado!";
    header("Location: meus_produtos.php");
    exit;
}

if ($produto['id_usuario'] != $_SESSION['user_id']) {
    $_SESSION['error'] = "Você não tem permissão para excluir este produto!";
    header("Location: meus_produtos.php");
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE php_bd.produtos SET ativo = FALSE WHERE id_camiseta = ?");
    $stmt->execute([$produto_id]); 
    $_SESSION['success'] = "Produto excluído com sucesso!";
} catch (Exception $e) {
    $_SESSION['error'] = "Erro ao excluir produto: " . $e->getMessage();
}

header("Location: meus_produtos.php");
exit;
?>