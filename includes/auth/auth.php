<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    $logged = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    error_log("DEBUG isLoggedIn: " . ($logged ? 'TRUE' : 'FALSE'));
    return $logged;
}

function isAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

function redirectIfNotLogged() {
    error_log("DEBUG redirectIfNotLogged called");
    if (!isLoggedIn()) {
        error_log("DEBUG Redirecting to login.php");
        header("Location: login.php");
        exit;
    }
}

function redirectIfNotAdmin() {
    if (!isAdmin()) {
        header("Location: dashboard.php");
        exit;
    }
}

function redirectIfLogged() {
    if (isLoggedIn()) {
        header("Location: dashboard.php");
        exit;
    }
}

function attemptLogin($cpf, $password) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM php_bd.usuarios WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['admin'] = $user['admin'] == 1;
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
        throw new Exception("Senha deve ter pelo menos 6 caracteres!");
    }
    
    // Inserir novo usuário
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO php_bd.usuarios (cpf, username, password) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$cpf, $username, $hashedPassword])) {
        return true;
    }
    
    throw new Exception("Erro ao cadastrar usuário!");
}
?>