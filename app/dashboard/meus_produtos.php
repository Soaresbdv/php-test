<?php
require_once '../../includes/bootstrap.php';
requireLogin();

$pdo = getDB();

$stmt = $pdo->prepare("
    SELECT p.*, u.username 
    FROM php_bd.produtos p 
    INNER JOIN php_bd.usuarios u ON p.id_usuario = u.id 
    WHERE p.id_usuario = ? AND p.ativo = TRUE 
    ORDER BY p.data_publicacao DESC
");
$stmt->execute([$_SESSION['user_id']]);
$produtos = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(preco) as valor_total FROM php_bd.produtos WHERE id_usuario = ? AND ativo = TRUE");
$stmt->execute([$_SESSION['user_id']]);
$estatisticas = $stmt->fetch();


if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Meus Produtos</title>
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
            
            if (menu && gear && !menu.contains(event.target) && !gear.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        function confirmarExclusao(id, descricao) {
            if (confirm(`Tem certeza que deseja excluir o produto "${descricao.substring(0, 50)}..."?`)) {
                window.location.href = `excluir_produto.php?id=${id}`;
            }
        }
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
                <p class="text-sm font-semibold text-light-text dark:text-dark-text">Minha Conta</p>
            </div>
            
            <div class="p-2">
                <a href="dashboard.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    Dashboard
                </a>
                
                <a href="meus_produtos.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-accent dark:text-dark-accent bg-light-accent/10 dark:bg-dark-accent/10 rounded-lg transition-colors">
                    Meus Produtos
                </a>
                
                <a href="divulgar_produto.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    Novo Produto
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

    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-8 mb-8 border border-light-border dark:border-dark-border transition-all">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-light-text dark:text-dark-text">Meus Produtos</h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-2">Gerencie todos os seus produtos anunciados</p>
                </div>
                <div class="space-x-4">
                    <a href="divulgar_produto.php" class="bg-light-accent dark:bg-dark-accent text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition shadow-lg">
                        + Novo Produto
                    </a>
                    <a href="dashboard.php" class="bg-gray-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-gray-600 transition">
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="text-red-700 dark:text-red-300">❌ <?php echo $error_message; ?></div>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="text-green-700 dark:text-green-300">✅ <?php echo $success_message; ?></div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 border border-light-border dark:border-dark-border transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl mr-4">
                        <span class="text-2xl">📦</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-light-text dark:text-dark-text"><?php echo $estatisticas['total'] ?? 0; ?></p>
                        <p class="text-light-text/70 dark:text-dark-text/70">Produtos Ativos</p>
                    </div>
                </div>
            </div>

            <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 border border-light-border dark:border-dark-border transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl mr-4">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-light-text dark:text-dark-text">R$ <?php echo number_format($estatisticas['valor_total'] ?? 0, 2, ',', '.'); ?></p>
                        <p class="text-light-text/70 dark:text-dark-text/70">Valor Total</p>
                    </div>
                </div>
            </div>

            <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 border border-light-border dark:border-dark-border transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl mr-4">
                        <span class="text-2xl">📊</span>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-light-text dark:text-dark-text"><?php echo count($produtos); ?></p>
                        <p class="text-light-text/70 dark:text-dark-text/70">Visíveis</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-8 border border-light-border dark:border-dark-border transition-all">
            <h2 class="text-2xl font-bold text-light-text dark:text-dark-text mb-6">Seus Produtos Anunciados</h2>
            
            <?php if (empty($produtos)): ?>
                <div class="text-center py-12">
                    <div class="text-6xl mb-4"></div>
                    <h3 class="text-xl font-semibold text-light-text dark:text-dark-text mb-2">Nenhum produto encontrado</h3>
                    <p class="text-light-text/70 dark:text-dark-text/70 mb-6">Você ainda não divulgou nenhum produto.</p>
                    <a href="divulgar_produto.php" class="bg-light-accent dark:bg-dark-accent text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition inline-block">
                        Divulgar Primeiro Produto
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($produtos as $produto): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-light-border dark:border-dark-border hover:shadow-xl transition-all duration-300">                       
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

                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-semibold">
                                        <?php echo $produto['tamanho']; ?>
                                    </span>
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 rounded-full text-xs font-semibold ml-1">
                                        <?php echo $produto['genero']; ?>
                                    </span>
                                </div>
                                <span class="text-xl font-bold text-light-accent dark:text-dark-accent">
                                    R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                                </span>
                            </div>

                            <div>
                                <p class="text-light-text/80 dark:text-dark-text/80 text-sm line-clamp-2"><?php echo htmlspecialchars($produto['descricao']); ?></p>
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
                                    <span class="text-light-text/50 dark:text-dark-text/50">
                                        <?php echo date('d/m/Y H:i', strtotime($produto['data_publicacao'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex space-x-2">
                            <a href="editar_produto.php?id=<?php echo $produto['id_camiseta']; ?>" 
                               class="flex-1 bg-blue-500 text-white py-2 rounded-lg font-semibold hover:bg-blue-600 transition text-sm text-center">
                                Editar
                            </a>
                            <button onclick="confirmarExclusao(<?php echo $produto['id_camiseta']; ?>, '<?php echo addslashes($produto['descricao']); ?>')" 
                                    class="flex-1 bg-red-500 text-white py-2 rounded-lg font-semibold hover:bg-red-600 transition text-sm">
                                Excluir
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
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