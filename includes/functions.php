<?php
require_once 'database.php';

function attemptLogin($cpf, $password) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("SELECT * FROM php_bd.usuarios WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['logged_in'] = true;
        $_SESSION['admin'] = $user['admin'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function attemptRegister($cpf, $username, $password) {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("SELECT id FROM php_bd.usuarios WHERE cpf = ?");
    $stmt->execute([$cpf]);
    
    if ($stmt->fetch()) {
        throw new Exception("CPF já cadastrado!");
    }
    
    if (strlen($password) < 6) {
        throw new Exception("Senha deve ter pelo menos 6 caracteres");
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO php_bd.usuarios (cpf, username, password) VALUES (?, ?, ?)");
    $stmt->execute([$cpf, $username, $hashedPassword]);
    
    return true;
}