<?php
session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/includes/config/env.php';
require_once BASE_PATH . '/includes/config/database.php';
require_once BASE_PATH . '/includes/auth/auth.php';
require_once BASE_PATH . '/includes/helpers/functions.php';
require_once BASE_PATH . '/includes/services/ImageStorage.php';

function loadLanguage($lang = 'pt') {
    $langFile = __DIR__ . "/languages/{$lang}.php";
    if (file_exists($langFile)) {
        return include($langFile);
    }
    return include(__DIR__ . '/languages/pt.php');
}

function t($key, $lang = null) {
    static $translations = null;
    
    if ($translations === null) {
        if (isset($_SESSION['user_language'])) {
            $userLang = $_SESSION['user_language'];
        } elseif (isset($_COOKIE['user_language'])) {
            $userLang = $_COOKIE['user_language'];
        } else {
            $userLang = 'pt';
        }
        
        $translations = loadLanguage($userLang);
    }
    
    return $translations[$key] ?? $key;
}
?>