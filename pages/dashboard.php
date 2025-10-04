<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/env.php';

redirectIfNotLogged();

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM php_bd.usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        light: {
                            primary: '#f8f4f0',
                            secondary: '#fffaf5', 
                            accent: '#667eea',
                            text: '#5a4d3a',
                            border: '#e8dfd5'
                        },
                        dark: {
                            primary: '#1a1a1a',
                            secondary: '#2d2d2d',
                            accent: '#8b5cf6',
                            text: '#e5e5e5',
                            border: '#404040'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-light-primary dark:bg-dark-primary transition-colors duration-300">
    <!-- Toggle Theme -->
    <div class="fixed top-4 right-4 z-50">
        <button id="themeToggle" class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <span class="dark:hidden">🌙</span>
            <span class="hidden dark:inline">☀️</span>
        </button>
    </div>

    <!-- Tela Dividida 50/50 -->
    <div class="flex h-screen">
        <!-- Metade Esquerda: Divulgar Produto -->
        <a href="divulgar_produto.php" 
           class="flex-1 flex flex-col items-center justify-center bg-light-secondary dark:bg-dark-secondary border-r border-light-border dark:border-dark-border hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 group transition-all duration-300 cursor-pointer">
            <div class="text-5xl mb-4 group-hover:scale-105 transition-transform">📢</div>
            <h2 class="text-xl font-semibold text-light-text dark:text-dark-text group-hover:text-light-accent dark:group-hover:text-dark-accent transition-colors">
                Divulgar um Produto
            </h2>
        </a>

        <!-- Metade Direita: Encontrar Produto -->
        <a href="encontrar_produto.php" 
           class="flex-1 flex flex-col items-center justify-center bg-light-secondary dark:bg-dark-secondary border-l border-light-border dark:border-dark-border hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 group transition-all duration-300 cursor-pointer">
            <div class="text-5xl mb-4 group-hover:scale-105 transition-transform">🔍</div>
            <h2 class="text-xl font-semibold text-light-text dark:text-dark-text group-hover:text-light-accent dark:group-hover:text-dark-accent transition-colors">
                Encontrar um Produto
            </h2>
        </a>
    </div>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
        });
    </script>
</body>
</html>