<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDB();
    
    $id_produto = $_POST['id_produto'] ?? null;
    $tamanho = $_POST['tamanho'] ?? null;
    $cor = $_POST['cor'] ?? null;
    
    if ($id_produto) {
        try {
            $stmt = $pdo->prepare("
                SELECT id_item, quantidade 
                FROM php_bd.carrinho_itens 
                WHERE id_usuario = ? AND id_produto = ? AND tamanho_selecionado = ? AND cor_selecionada = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $id_produto, $tamanho, $cor]);
            $item_existente = $stmt->fetch();
            
            if ($item_existente) {
                $stmt = $pdo->prepare("
                    UPDATE php_bd.carrinho_itens 
                    SET quantidade = quantidade + 1 
                    WHERE id_item = ?
                ");
                $stmt->execute([$item_existente['id_item']]);
                $acao = 'atualizado';
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO php_bd.carrinho_itens (id_usuario, id_produto, quantidade, tamanho_selecionado, cor_selecionada) 
                    VALUES (?, ?, 1, ?, ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $id_produto, $tamanho, $cor]);
                $acao = 'adicionado';
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Produto adicionado ao carrinho!',
                'acao' => $acao
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao adicionar produto ao carrinho: ' . $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do produto não especificado'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
}
?>