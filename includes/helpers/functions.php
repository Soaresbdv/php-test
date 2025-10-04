<?php
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit;
    }
}
?>