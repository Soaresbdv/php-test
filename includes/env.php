<?php
class Env {
    private static $loaded = false;
    private static $vars = [];
    
    public static function load($path = '../.env') {  // ← MUDEI AQUI: '../.env'
        if (self::$loaded) return;
        
        if (!file_exists($path)) {
            throw new Exception("Arquivo .env não encontrado: " . $path);
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);             
            $value = trim($value, '"\'');
            
            self::$vars[$name] = $value;
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
        
        self::$loaded = true;
    }
    
    public static function get($key, $default = null) {
        self::load();
        return self::$vars[$key] ?? $default;
    }
}

Env::load();
?>