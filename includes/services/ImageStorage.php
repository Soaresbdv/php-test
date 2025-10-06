<?php
class ImageStorage {
    private $basePath;

    public function __construct() {
        $this->basePath = __DIR__ . '/../../uploads/';
        
        // Criar diretórios se não existirem
        $this->createDirectories();
    }

    private function createDirectories() {
        $directories = ['profiles', 'products'];
        
        foreach ($directories as $dir) {
            $fullPath = $this->basePath . $dir;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
        }
    }

    public function uploadProfileImage($file, $userId) {
       $uploadDir = $this->basePath . 'profiles/';
       
       // Debug
       error_log("Tentando fazer upload para: " . $uploadDir);
       error_log("Arquivo recebido: " . $file['name'] . " - Tamanho: " . $file['size']);
       
       // Validar tipo de arquivo
       $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
       if (!in_array($file['type'], $allowedTypes)) {
           error_log("Tipo de arquivo não permitido: " . $file['type']);
           return ['success' => false, 'error' => 'Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP.'];
       }
    
       // Validar tamanho (máximo 2MB)
       if ($file['size'] > 2 * 1024 * 1024) {
           error_log("Arquivo muito grande: " . $file['size']);
           return ['success' => false, 'error' => 'Arquivo muito grande. Máximo 2MB.'];
       }
    
       // Gerar nome único para o arquivo
       $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
       $fileName = 'profile_' . $userId . '_' . time() . '.' . $extension;
       $filePath = $uploadDir . $fileName;
    
       error_log("Caminho completo do arquivo: " . $filePath);
    
       // Mover arquivo
       if (move_uploaded_file($file['tmp_name'], $filePath)) {
           $relativePath = 'uploads/profiles/' . $fileName;
           error_log("Upload bem-sucedido. Caminho relativo: " . $relativePath);
           
           // Verificar se o arquivo realmente existe
           if (file_exists($filePath)) {
               error_log("Arquivo confirmado no sistema de arquivos");
           } else {
               error_log("AVISO: Arquivo não encontrado após upload");
           }
           
           return [
               'success' => true, 
               'file_path' => $relativePath
           ];
       } else {
           error_log("Falha no move_uploaded_file. Erro: " . $file['error']);
           return ['success' => false, 'error' => 'Erro ao fazer upload do arquivo.'];
       }
    }

    public function uploadProductImage($file, $productId) {
        $uploadDir = $this->basePath . 'products/';
        
        // Validar tipo de arquivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Tipo de arquivo não permitido.'];
        }

        // Validar tamanho (máximo 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Arquivo muito grande. Máximo 2MB.'];
        }

        // Gerar nome único para o arquivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'product_' . $productId . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        // Mover arquivo
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true, 
                'file_path' => 'uploads/products/' . $fileName
            ];
        }

        return ['success' => false, 'error' => 'Erro ao fazer upload do arquivo.'];
    }
}
?>