<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

$id_produto = $_GET['id_produto'] ?? null;
$id_vendedor = $_GET['id_vendedor'] ?? null;
$id_usuario_logado = $_SESSION['user_id'];

if (!$id_produto || !$id_vendedor) {
    header('Location: encontrar_produto.php');
    exit;
}

if ($id_vendedor == $id_usuario_logado) {
    $_SESSION['error'] = "Você não pode iniciar uma conversa sobre seu próprio produto!";
    header('Location: encontrar_produto.php');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT p.*, u.username as vendedor_nome FROM php_bd.produtos p INNER JOIN php_bd.usuarios u ON p.id_usuario = u.id WHERE p.id_camiseta = ?");
$stmt->execute([$id_produto]);
$produto = $stmt->fetch();

if (!$produto) {
    header('Location: encontrar_produto.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM php_bd.conversas WHERE id_produto = ? AND id_usuario_interessado = ? AND ativa = TRUE");
$stmt->execute([$id_produto, $id_usuario_logado]);
$conversa_existente = $stmt->fetch();

if ($conversa_existente) {
    header('Location: chat.php?id_conversa=' . $conversa_existente['id']);
    exit;
} else {
    $stmt = $pdo->prepare("INSERT INTO php_bd.conversas (id_produto, id_usuario_anunciante, id_usuario_interessado) VALUES (?, ?, ?)");
    $stmt->execute([$id_produto, $id_vendedor, $id_usuario_logado]);
    $stmt = $pdo->query("SELECT LASTVAL()");
    $id_conversa = $stmt->fetchColumn();
    
    header('Location: chat.php?id_conversa=' . $id_conversa);
    exit;
}
?>