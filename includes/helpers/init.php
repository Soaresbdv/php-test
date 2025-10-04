<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'env.php';
require_once 'database.php';
require_once 'auth.php';

function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM php_bd.usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

function requireLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
        header("Location: dashboard.php");
        exit;
    }
}
?>