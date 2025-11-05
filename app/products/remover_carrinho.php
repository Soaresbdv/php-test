<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDB();
    $input = json_decode(file_get_contents('php://input'), true);
    $id_item = $input['id_item'] ?? null;
    
    if ($id_item) {
        try {
            $stmt = $pdo->prepare("
                SELECT id_item FROM php_bd.carrinho_itens 
                WHERE id_item = ? AND id_usuario = ?
            ");
            $stmt->execute([$id_item, $_SESSION['user_id']]);
            $item = $stmt->fetch();
            
            if ($item) {
                $stmt = $pdo->prepare("DELETE FROM php_bd.carrinho_itens WHERE id_item = ?");
                $stmt->execute([$id_item]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Item removido do carrinho com sucesso!'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Item não encontrado no seu carrinho'
                ]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao remover item do carrinho: ' . $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID do item não especificado'
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