<?php
require_once __DIR__ . '/../../includes/bootstrap.php';

if (!isset($_GET['file'])) {
    http_response_code(400);
    exit('Arquivo não especificado');
}

$file = $_GET['file'];
$basePath = __DIR__ . '/../../uploads/profiles/';
$filePath = $basePath . basename($file);

if (!file_exists($filePath) || !is_file($filePath)) {
    http_response_code(404);
    exit('Imagem não encontrada');
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$mimeType = mime_content_type($filePath);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(403);
    exit('Tipo de arquivo não permitido');
}

header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
readfile($filePath);