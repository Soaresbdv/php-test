<?php
require_once 'env.php';

function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = Env::get('DB_HOST');
        $dbname = Env::get('DB_NAME');
        $username = Env::get('DB_USER');
        $password = Env::get('DB_PASS');
        $port = Env::get('DB_PORT');

        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro no banco: " . $e->getMessage());
        }
    }
    
    return $pdo;
}
?>