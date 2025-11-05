<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

$pdo = getDB();
$stmt = $pdo->prepare("
    SELECT ci.*, p.descricao, p.preco, p.foto, p.tamanho as tamanho_produto, p.cor as cor_produto
    FROM php_bd.carrinho_itens ci
    JOIN php_bd.produtos p ON ci.id_produto = p.id_camiseta
    WHERE ci.id_usuario = ?
    ORDER BY ci.data_adicionado DESC
");
$stmt->execute([$_SESSION['user_id']]);
$itens_carrinho = $stmt->fetchAll();
$total = 0;
foreach ($itens_carrinho as $item) {
    $total += $item['preco'] * $item['quantidade'];
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Carrinho de Compras</title>
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

    <div class="max-w-4xl mx-auto py-6 px-4">
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 mb-6 border border-light-border dark:border-dark-border transition-all">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-light-text dark:text-dark-text">Meu Carrinho</h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-1">Gerencie seus produtos</p>
                </div>
                <div class="space-x-3">
                    <a href="encontrar_produto.php" class="bg-light-accent dark:bg-dark-accent text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        Continuar Comprando
                    </a>
                </div>
            </div>
        </div>

        <?php if (empty($itens_carrinho)): ?>
            <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-12 text-center border border-light-border dark:border-dark-border">
                <div class="text-6xl mb-4"></div>
                <h3 class="text-xl font-semibold text-light-text dark:text-dark-text mb-2">Seu carrinho está vazio</h3>
                <p class="text-light-text/70 dark:text-dark-text/70 mb-6">Adicione alguns produtos incríveis!</p>
                <a href="encontrar_produto.php" class="bg-light-accent dark:bg-dark-accent text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    Explorar Produtos
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($itens_carrinho as $item): ?>
                <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-lg p-6 border border-light-border dark:border-dark-border">
                    <div class="flex items-center space-x-4">
                        <?php if (!empty($item['foto'])): ?>
                            <img src="../<?php echo htmlspecialchars($item['foto']); ?>" 
                                 alt="Produto" 
                                 class="w-20 h-20 object-cover rounded-lg">
                        <?php else: ?>
                            <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <span class="text-gray-500 dark:text-gray-400 text-2xl">🖼️</span>
                            </div>
                        <?php endif; ?>

                        <div class="flex-1">
                            <h3 class="font-semibold text-light-text dark:text-dark-text">
                                <?php echo htmlspecialchars($item['descricao']); ?>
                            </h3>
                            <div class="flex items-center space-x-4 mt-2 text-sm text-light-text/70 dark:text-dark-text/70">
                                <span>Quantidade: <?php echo $item['quantidade']; ?></span>
                                <?php if ($item['tamanho_selecionado']): ?>
                                    <span>Tamanho: <?php echo htmlspecialchars($item['tamanho_selecionado']); ?></span>
                                <?php endif; ?>
                                <?php if ($item['cor_selecionada']): ?>
                                    <span>Cor: <?php echo htmlspecialchars($item['cor_selecionada']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-lg font-bold text-light-accent dark:text-dark-accent">
                                R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                            </p>
                            <p class="text-sm text-light-text/70 dark:text-dark-text/70">
                                R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?> cada
                            </p>
                            <button onclick="removerDoCarrinho(<?php echo $item['id_item']; ?>)" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-colors flex items-center space-x-1">
                                <span>🗑️</span>
                                <span>Remover</span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-lg p-6 border border-light-border dark:border-dark-border">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-light-text dark:text-dark-text">Total</h3>
                            <p class="text-2xl font-bold text-light-accent dark:text-dark-accent">
                                R$ <?php echo number_format($total, 2, ',', '.'); ?>
                            </p>
                        </div>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                            Finalizar Compra
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
<div id="toastContainer" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 space-y-2"></div>

    <script>
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? '✅' : '❌';
            
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-xl transform transition-all duration-300 max-w-md opacity-0 -translate-y-2`;
            toast.innerHTML = `
                <div class="flex items-center space-x-3">
                    <span class="text-lg flex-shrink-0">${icon}</span>
                    <span class="flex-1 text-sm font-medium">${message}</span>
                    <button onclick="closeToast('${toastId}')" class="text-white hover:text-gray-200 flex-shrink-0">
                        ✕
                    </button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('opacity-0', '-translate-y-2');
                toast.classList.add('opacity-100', 'translate-y-0');
            }, 10);
            
            setTimeout(() => {
                closeToast(toastId);
            }, 3000);
        }
        
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }

        async function removerDoCarrinho(idItem) {
            if (!confirm('Tem certeza que deseja remover este item do carrinho?')) {
                return;
            }

            try {
                const response = await fetch('remover_carrinho.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id_item: idItem })
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    showToast('Item removido do carrinho!', 'success');
                    
                    setTimeout(() => {
                        location.reload(); 
                    }, 1000);
                } else {
                    showToast('❌ ' + (result.message || 'Erro ao remover item'), 'error');
                }
                
            } catch (error) {
                console.error('Erro:', error);
                showToast('❌ Erro de conexão', 'error');
            }
        }
    </script>
</body>
</html>