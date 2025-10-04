<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

$pdo = getDB();
$stmt = $pdo->query("
    SELECT p.*, u.username 
    FROM php_bd.produtos p 
    INNER JOIN php_bd.usuarios u ON p.id_usuario = u.id 
    WHERE p.ativo = TRUE 
    ORDER BY p.data_publicacao DESC
");
$produtos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Encontrar Produtos</title>
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
        <button id="themeToggle" class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <span class="dark:hidden">🌙</span>
            <span class="hidden dark:inline">☀️</span>
        </button>
    </div>

    <div class="max-w-6xl mx-auto py-6 px-4">
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 mb-6 border border-light-border dark:border-dark-border transition-all">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-light-text dark:text-dark-text">Encontrar Produtos</h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-1">Descubra camisetas incríveis na comunidade</p>
                </div>
                <div class="space-x-3">
                    <a href="../dashboard/dashboard.php" class="bg-light-accent dark:bg-dark-accent text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($produtos)): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">😔</div>
                    <h3 class="text-xl font-semibold text-light-text dark:text-dark-text mb-2">Nenhum produto encontrado</h3>
                    <p class="text-light-text/70 dark:text-dark-text/70">Seja o primeiro a divulgar uma camiseta!</p>
                </div>
            <?php else: ?>
                <?php foreach ($produtos as $produto): ?>
                <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-lg p-6 border border-light-border dark:border-dark-border hover:shadow-xl transition-all duration-300">
                    
                    <?php if (!empty($produto['foto'])): ?>
                        <div class="mb-4">
                            <img src="../<?php echo htmlspecialchars($produto['foto']); ?>" 
                                 alt="Foto do produto" 
                                 class="w-full h-48 object-cover rounded-xl mb-3">
                        </div>
                    <?php else: ?>
                        <div class="mb-4 bg-gray-200 dark:bg-gray-700 h-48 rounded-xl flex items-center justify-center">
                            <span class="text-gray-500 dark:text-gray-400 text-4xl">🖼️</span>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-semibold">
                                <?php echo $produto['tamanho']; ?>
                            </span>
                            <span class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 rounded-full text-xs font-semibold ml-1">
                                <?php echo $produto['genero']; ?>
                            </span>
                        </div>
                        <span class="text-2xl font-bold text-light-accent dark:text-dark-accent">
                            R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <h3 class="font-semibold text-light-text dark:text-dark-text mb-1">Descrição</h3>
                            <p class="text-light-text/80 dark:text-dark-text/80 text-sm"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-light-text/70 dark:text-dark-text/70">Cor:</span>
                                <span class="text-light-text dark:text-dark-text font-medium"><?php echo htmlspecialchars($produto['cor']); ?></span>
                            </div>
                            <?php if (!empty($produto['estampa'])): ?>
                            <div>
                                <span class="text-light-text/70 dark:text-dark-text/70">Estampa:</span>
                                <span class="text-light-text dark:text-dark-text font-medium"><?php echo htmlspecialchars($produto['estampa']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="border-t border-light-border dark:border-dark-border pt-3">
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span class="text-light-text/70 dark:text-dark-text/70">Vendedor:</span>
                                    <span class="text-light-text dark:text-dark-text font-medium"><?php echo htmlspecialchars($produto['username']); ?></span>
                                </div>
                                <span class="text-light-text/50 dark:text-dark-text/50 text-xs">
                                    <?php echo date('d/m/Y', strtotime($produto['data_publicacao'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="w-full bg-light-accent dark:bg-dark-accent text-white py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                            Entrar em Contato
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
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