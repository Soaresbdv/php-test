<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

$id_usuario_logado = $_SESSION['user_id'];
$pdo = getDB();

$stmt = $pdo->prepare("
    SELECT c.*, p.descricao as produto_titulo, p.preco, p.foto,
           ua.username as anunciante_nome, ui.username as interessado_nome,
           ua.id as id_anunciante, ui.id as id_interessado,
           (SELECT COUNT(*) FROM php_bd.mensagens m WHERE m.id_conversa = c.id AND m.id_usuario_remetente != ? AND m.lida = FALSE) as mensagens_nao_lidas,
           (SELECT mensagem FROM php_bd.mensagens WHERE id_conversa = c.id ORDER BY data_envio DESC LIMIT 1) as ultima_mensagem
    FROM php_bd.conversas c
    INNER JOIN php_bd.produtos p ON c.id_produto = p.id_camiseta
    INNER JOIN php_bd.usuarios ua ON c.id_usuario_anunciante = ua.id
    INNER JOIN php_bd.usuarios ui ON c.id_usuario_interessado = ui.id
    WHERE (c.id_usuario_anunciante = ? OR c.id_usuario_interessado = ?) AND c.ativa = TRUE
    ORDER BY (SELECT MAX(data_envio) FROM php_bd.mensagens WHERE id_conversa = c.id) DESC
");
$stmt->execute([$id_usuario_logado, $id_usuario_logado, $id_usuario_logado]);
$conversas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title><?php echo t('my_conversations'); ?></title>
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
        <button id="themeToggle" class="p-3 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <svg class="w-6 h-6 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg class="w-6 h-6 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>
    </div>

    <div class="max-w-4xl mx-auto py-6 px-4">
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 mb-6 border border-light-border dark:border-dark-border">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-light-text dark:text-dark-text"><?php echo t('my_conversations'); ?></h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-1"><?php echo t('your_product_conversations'); ?></p>
                </div>
                <div class="space-x-3">
                    <a href="encontrar_produto.php" class="bg-light-accent dark:bg-dark-accent text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        <?php echo t('find_products'); ?>
                    </a>
                    <a href="../dashboard/dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        <?php echo t('back'); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <?php if (empty($conversas)): ?>
                <div class="text-center py-12 bg-light-secondary dark:bg-dark-secondary rounded-2xl">
                    <div class="text-6xl mb-4">💬</div>
                    <h3 class="text-xl font-semibold text-light-text dark:text-dark-text mb-2"><?php echo t('no_conversations'); ?></h3>
                    <p class="text-light-text/70 dark:text-dark-text/70"><?php echo t('find_product_start_conversation'); ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($conversas as $conversa): ?>
                <?php 
                    $outro_usuario_id = ($conversa['id_anunciante'] == $id_usuario_logado) ? $conversa['id_interessado'] : $conversa['id_anunciante'];
                    $outro_usuario_nome = ($conversa['id_anunciante'] == $id_usuario_logado) ? $conversa['interessado_nome'] : $conversa['anunciante_nome'];
                ?>
                <a href="chat.php?id_conversa=<?php echo $conversa['id']; ?>" class="block bg-light-secondary dark:bg-dark-secondary rounded-2xl p-4 border border-light-border dark:border-dark-border hover:shadow-lg transition-all">
                    <div class="flex items-center space-x-4">
                        <?php if (!empty($conversa['foto'])): ?>
                            <img src="../../<?php echo htmlspecialchars($conversa['foto']); ?>" 
                                 alt="<?php echo t('product'); ?>" 
                                 class="w-16 h-16 object-cover rounded-xl">
                        <?php else: ?>
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                                <span class="text-gray-500 dark:text-gray-400 text-2xl">🖼️</span>
                            </div>
                        <?php endif; ?>   

                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-light-text dark:text-dark-text"><?php echo htmlspecialchars($outro_usuario_nome); ?></h3>
                                    <p class="text-sm text-light-text/70 dark:text-dark-text/70"><?php echo htmlspecialchars($conversa['produto_titulo']); ?></p>
                                    <?php if (!empty($conversa['ultima_mensagem'])): ?>
                                        <p class="text-sm text-light-text/80 dark:text-dark-text/80 mt-1 truncate max-w-md">
                                            <?php echo htmlspecialchars($conversa['ultima_mensagem']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-light-text/70 dark:text-dark-text/70">
                                        R$ <?php echo isset($conversa['preco']) ? number_format(floatval($conversa['preco']), 2, ',', '.') : '0,00'; ?>
                                    </p>
                                    <?php if ($conversa['mensagens_nao_lidas'] > 0): ?>
                                        <span class="inline-block mt-1 bg-red-500 text-white text-xs rounded-full px-2 py-1">
                                            <?php echo $conversa['mensagens_nao_lidas']; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
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