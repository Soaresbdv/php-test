<?php
session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/includes/config/env.php';
require_once BASE_PATH . '/includes/config/database.php';
require_once BASE_PATH . '/includes/auth/auth.php';
require_once BASE_PATH . '/includes/helpers/functions.php';
?>