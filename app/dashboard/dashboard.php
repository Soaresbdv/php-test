<?php
require_once '../../includes/bootstrap.php';

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

        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userMenu');
            const gear = document.getElementById('userGear');
            
            if (!menu.contains(event.target) && !gear.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</head>
<body class="h-full bg-light-primary dark:bg-dark-primary transition-colors duration-300">
    <div class="fixed top-4 left-4 z-50">
        <button id="userGear" onclick="toggleUserMenu()" 
                class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            ⚙️
        </button>
        <div id="userMenu" class="hidden absolute left-0 top-12 mt-2 w-48 bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-2xl border border-light-border dark:border-dark-border transition-all duration-300">
            <div class="p-4 border-b border-light-border dark:border-dark-border">
                <p class="text-sm font-semibold text-light-text dark:text-dark-text">Olá, <?php echo htmlspecialchars($user['username']); ?>!</p>
                <p class="text-xs text-light-text/70 dark:text-dark-text/70 mt-1"><?php echo htmlspecialchars($user['cpf']); ?></p>
            </div>
            
            <div class="p-2">
                <a href="meus_produtos.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    Meus Produtos
                </a>
                
                <a href="editar_perfil.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    Editar Perfil
                </a>
                
                <a href="configuracoes.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    Configurações
                </a>
            </div>
            
            <div class="p-2 border-t border-light-border dark:border-dark-border">
                <a href="../auth/logout.php" 
                   class="flex items-center px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    Sair
                </a>
            </div>
        </div>
    </div>

    <div class="fixed top-4 right-4 z-50">
        <button id="themeToggle" class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <span class="dark:hidden">🌙</span>
            <span class="hidden dark:inline">☀️</span>
        </button>
    </div>

    <div class="flex h-screen">
        <a href="divulgar_produto.php" 
           class="flex-1 flex flex-col items-center justify-center bg-light-secondary dark:bg-dark-secondary border-r border-light-border dark:border-dark-border hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 group transition-all duration-300 cursor-pointer">
            <div class="text-3xl mb-4 group-hover:scale-105 transition-transform">📢</div>
            <h2 class="text-xl font-semibold text-light-text dark:text-dark-text group-hover:text-light-accent dark:group-hover:text-dark-accent transition-colors">
                Divulgar um Produto
            </h2>
        </a>

        <a href="../products/encontrar_produto.php" 
           class="flex-1 flex flex-col items-center justify-center bg-light-secondary dark:bg-dark-secondary border-l border-light-border dark:border-dark-border hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 group transition-all duration-300 cursor-pointer">
            <div class="text-3xl mb-4 group-hover:scale-105 transition-transform">🔍</div>
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