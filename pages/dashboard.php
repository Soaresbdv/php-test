<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/env.php';

redirectIfNotLogged();

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$appName = Env::get('APP_NAME', 'Sistema');
$appUrl = Env::get('APP_URL', 'http://localhost:8000');
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Dashboard - <?php echo $appName; ?></title>
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
    <div class="fixed top-4 right-4 z-50">
        <button id="themeToggle" class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform text-sm">
            <span class="dark:hidden">🌙</span>
            <span class="hidden dark:inline">☀️</span>
        </button>
    </div>

    <div class="max-w-6xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-8 mb-8 border border-light-border dark:border-dark-border transition-all">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-light-text dark:text-dark-text"><?php echo $appName; ?></h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-2">Sistema seguro de autenticação</p>
                </div>
                <div class="space-x-4">
                    <a href="logout.php" class="bg-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition shadow-lg">
                        🚪 Sair
                    </a>
                </div>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-8 border border-light-border dark:border-dark-border transition-all">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold text-light-text dark:text-dark-text mb-4">Logado com Sucesso!!!</h2>
                
                <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 p-6 rounded-xl mb-6">
                    <div class="text-green-700 dark:text-green-300 text-lg font-semibold">
                        Você acessou o sistema com sucesso!!!
                        <?php if ($_SESSION['admin']): ?>
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm ml-2 font-semibold">ADMINISTRADOR</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>       
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl border border-light-border dark:border-dark-border mb-8 transition-all">
                <h3 class="text-2xl font-semibold text-light-text dark:text-dark-text mb-6">Seus Dados</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <p class="text-sm text-light-text/70 dark:text-dark-text/70">ID</p>
                        <p class="font-medium text-light-text dark:text-dark-text text-lg"><?php echo $user['id']; ?></p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm text-light-text/70 dark:text-dark-text/70">Nome</p>
                        <p class="font-medium text-light-text dark:text-dark-text text-lg"><?php echo htmlspecialchars($user['username']); ?></p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm text-light-text/70 dark:text-dark-text/70">CPF</p>
                        <p class="font-medium text-light-text dark:text-dark-text text-lg"><?php echo htmlspecialchars($user['cpf']); ?></p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm text-light-text/70 dark:text-dark-text/70">Tipo de Usuário</p>
                        <p class="font-medium text-light-text dark:text-dark-text text-lg">
                            <?php echo $user['admin'] ? '👑 Administrador' : '👤 Usuário Comum'; ?>
                        </p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-sm text-light-text/70 dark:text-dark-text/70">Data de Cadastro</p>
                        <p class="font-medium text-light-text dark:text-dark-text text-lg"><?php echo date('d/m/Y H:i', strtotime($user['create_at'])); ?></p>
                    </div>
                </div>
            </div>
            <?php if ($_SESSION['admin']): ?>
            <div class="bg-blue-50 dark:bg-blue-900/20 p-8 rounded-xl border border-blue-200 dark:border-blue-800 mb-8 transition-all">
                <h3 class="text-2xl font-semibold text-light-text dark:text-dark-text mb-6">Área Administrativa</h3>
                <p class="text-light-text/70 dark:text-dark-text/70 mb-6 text-lg">Você tem acesso às funcionalidades administrativas do sistema.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-light-border dark:border-dark-border hover:shadow-lg transition-all">
                        <h4 class="font-semibold text-light-text dark:text-dark-text text-lg mb-3"> Estatísticas</h4>
                        <p class="text-light-text/70 dark:text-dark-text/70 text-sm">Visualizar relatórios do sistema</p>
                    </div>
                    
                    <a href="admin.php" class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-light-border dark:border-dark-border hover:shadow-lg transition-all hover:border-light-accent dark:hover:border-dark-accent group cursor-pointer block">
                        <h4 class="font-semibold text-light-text dark:text-dark-text text-lg mb-3 group-hover:text-light-accent dark:group-hover:text-dark-accent transition-colors">👥 Gerenciar Usuários</h4>
                        <p class="text-light-text/70 dark:text-dark-text/70 text-sm">CRUD completo de usuários</p>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
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