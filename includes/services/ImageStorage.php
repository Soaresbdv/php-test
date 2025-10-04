<?php
class ImageStorage {
    private $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function saveToDatabase($file, $userId) {
        if (!$this->isValidImage($file)) {
            throw new Exception('Imagem inválida');
        }

        $imageData = file_get_contents($file['tmp_name']);
        $mimeType = $this->getMimeType($file);

        return [
            'data' => $imageData,
            'mime_type' => $mimeType
        ];
    }

    public function getImageUrl($produtoId) {
        return "get_image.php?id=" . $produtoId;
    }

    private function isValidImage($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        return in_array($mimeType, $allowedTypes);
    }

    private function getMimeType($file) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        return $mimeType;
    }
}
?>