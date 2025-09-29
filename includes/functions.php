<?php
require_once 'database.php';

function attemptLogin($cpf, $password) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;
        return true;
    }
    
    return false;
}

function attemptRegister($cpf, $username, $password) {
    if (strlen($password) < 6) {
        throw new Exception("Senha precisa ter 6+ caracteres");
    }
    
    $pdo = getDB();
    
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE cpf = ?");
    $stmt->execute([$cpf]);
    if ($stmt->fetch()) {
        throw new Exception("CPF já cadastrado");
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (cpf, username, password) VALUES (?, ?, ?)");
    return $stmt->execute([$cpf, $username, $hashedPassword]);
}
?>