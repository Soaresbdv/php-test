<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: pages/dashboard.php");
} else {
    header("Location: pages/login.php");
}
exit;
?>