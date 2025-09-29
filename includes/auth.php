<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function redirectIfNotLogged() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function redirectIfLogged() {
    if (isLoggedIn()) {
        header("Location: dashboard.php");
        exit;
    }
}
?>