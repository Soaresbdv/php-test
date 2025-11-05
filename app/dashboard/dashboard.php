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
    <title><?php echo t('dashboard'); ?></title>
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
                class="p-3 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
        <div id="userMenu" class="hidden absolute left-0 top-12 mt-2 w-48 bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-2xl border border-light-border dark:border-dark-border transition-all duration-300">
            <div class="p-4 border-b border-light-border dark:border-dark-border">
                <p class="text-sm font-semibold text-light-text dark:text-dark-text"><?php echo t('hello'); ?>, <?php echo htmlspecialchars($user['username']); ?>!</p>
                <p class="text-xs text-light-text/70 dark:text-dark-text/70 mt-1"><?php echo htmlspecialchars($user['cpf']); ?></p>
            </div>
            
            <div class="p-2">
                <a href="meus_produtos.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    <?php echo t('my_products'); ?>
                </a>

                <a href="../products/minhas_conversas.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    <?php echo t('my_conversations'); ?>
                </a>

                <a href="editar_perfil.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    <?php echo t('edit_profile'); ?>
                </a>

                <a href="configuracoes.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    <?php echo t('settings'); ?>
                </a>
            </div>
            
            <div class="p-2 border-t border-light-border dark:border-dark-border">
                <a href="../auth/logout.php" 
                   class="flex items-center px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    <?php echo t('logout'); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="fixed top-4 right-4 z-50">
        <button id="themeToggle" class="p-3 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <svg class="w-6 h-6 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg class="w-6 h-6 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>
    </div>

    <div class="flex h-screen">
        <a href="divulgar_produto.php" 
           class="flex-1 flex flex-col items-center justify-center bg-light-secondary dark:bg-dark-secondary border-r border-light-border dark:border-dark-border hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 group transition-all duration-300 cursor-pointer">
            <div class="text-6xl mb-6 group-hover:scale-110 transition-transform">
              <svg
                viewBox="0 0 24 24"
                role="img"
                xmlns="http://www.w3.org/2000/svg"
                aria-labelledby="hornIconTitle"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
                class="w-20 h-20 mx-auto"
              >
                <title id="hornIconTitle">Bullhorn</title>
                <path
                  stroke-linejoin="round"
                  d="M6.5,9 C8.33333333,9 10.1666667,9 12,9 C14,9 16.3333333,7.33333333 19,4 L19,19 C16.3333333,15.6666667 14,14 12,14 C10.1666667,14 8.33333333,14 6.5,14 L6.5,14 C5.11928813,14 4,12.8807119 4,11.5 L4,11.5 C4,10.1192881 5.11928813,9 6.5,9 Z"
                ></path>
                <polygon points="7 14 9 20 13 20 11 14"></polygon>
                <path d="M11,9 L11,14"></path>
              </svg>
            </div>
            <h2 class="text-2xl font-semibold text-light-text dark:text-dark-text group-hover:text-light-accent dark:group-hover:text-dark-accent transition-colors text-center px-4">
                <?php echo t('advertise_product'); ?>
            </h2>
            <p class="text-light-text/70 dark:text-dark-text/70 mt-2 text-center px-4">
                <?php echo t('advertise_product_desc'); ?>
            </p>
        </a>

        <a href="../products/encontrar_produto.php" 
           class="flex-1 flex flex-col items-center justify-center bg-light-secondary dark:bg-dark-secondary border-l border-light-border dark:border-dark-border hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 group transition-all duration-300 cursor-pointer">
            <div class="text-6xl mb-6 group-hover:scale-110 transition-transform">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 512 512"
                fill="currentColor"
                class="w-16 h-16 mx-auto"
                role="img"
                aria-label="Search Icon"
              >
                <path
                  d="M172.625,102.4c-42.674,0-77.392,34.739-77.392,77.438c0,5.932,4.806,10.74,10.733,10.74
                  c5.928,0,10.733-4.808,10.733-10.74c0-30.856,25.088-55.959,55.926-55.959c5.928,0,10.733-4.808,10.733-10.74
                  C183.358,107.208,178.553,102.4,172.625,102.4z"
                ></path>
                <path
                  d="M361.657,301.511c19.402-30.436,30.645-66.546,30.645-105.244C392.302,88.036,304.318,0,196.151,0
                  c-38.676,0-74.765,11.25-105.182,30.663C66.734,46.123,46.11,66.759,30.659,91.008C11.257,121.444,0,157.568,0,196.267
                  c0,108.217,87.998,196.266,196.151,196.266c38.676,0,74.779-11.264,105.197-30.677
                  C325.582,346.396,346.206,325.76,361.657,301.511z M259.758,320.242c-19.075,9.842-40.708,15.403-63.607,15.403
                  c-76.797,0-139.296-62.535-139.296-139.378c0-22.912,5.558-44.558,15.394-63.644c13.318-25.856,34.483-47.019,60.323-60.331
                  c19.075-9.842,40.694-15.403,63.578-15.403c76.812,0,139.296,62.521,139.296,139.378c0,22.898-5.558,44.53-15.394,63.616
                  C306.749,285.739,285.598,306.916,259.758,320.242z"
                ></path>
                <path
                  d="M499.516,439.154L386.275,326.13c-16.119,23.552-36.771,44.202-60.309,60.345l113.241,113.024
                  c8.329,8.334,19.246,12.501,30.148,12.501c10.916,0,21.833-4.167,30.162-12.501C516.161,482.83,516.161,455.822,499.516,439.154z"
                ></path>
              </svg>
            </div>
           <h2 class="text-2xl font-semibold text-light-text dark:text-dark-text group-hover:text-light-accent dark:group-hover:text-dark-accent transition-colors text-center px-4">
                <?php echo t('find_product'); ?>
            </h2>
            <p class="text-light-text/70 dark:text-dark-text/70 mt-2 text-center px-4">
                <?php echo t('find_product_desc'); ?>
            </p>
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