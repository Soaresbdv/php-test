<?php
require_once '../../includes/bootstrap.php';
$pdo = getDB();

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT foto_bytea, foto_mime_type FROM php_bd.produtos WHERE id_camiseta = ?");
    $stmt->execute([$_GET['id']]);
    $produto = $stmt->fetch();

    if ($produto && $produto['foto_bytea']) {
        header("Content-Type: " . $produto['foto_mime_type']);
        echo pg_unescape_bytea($produto['foto_bytea']);
        exit;
    }
}

header("Content-Type: image/png");
readfile('../assets/default-image.png');
?>