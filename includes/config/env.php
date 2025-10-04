<?php
class Env {
    public static function load() {
        $envPath = BASE_PATH . '/.env';
        
        if (!file_exists($envPath)) {
            throw new Exception("Arquivo .env não encontrado: " . $envPath);
        }
        
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }
        }
    }
    
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}

Env::load();
?>