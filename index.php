<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: app/dashboard/dashboard.php");
} else {
    header("Location: app/auth/login.php");
}
exit;
?>