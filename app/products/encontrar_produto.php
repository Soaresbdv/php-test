<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

$pdo = getDB();
$filtros = [
    'tamanhos' => $pdo->query("SELECT DISTINCT tamanho FROM php_bd.produtos WHERE ativo = TRUE AND tamanho IS NOT NULL ORDER BY tamanho")->fetchAll(PDO::FETCH_COLUMN),
    'cores' => $pdo->query("SELECT DISTINCT cor FROM php_bd.produtos WHERE ativo = TRUE AND cor IS NOT NULL ORDER BY cor")->fetchAll(PDO::FETCH_COLUMN),
    'generos' => $pdo->query("SELECT DISTINCT genero FROM php_bd.produtos WHERE ativo = TRUE AND genero IS NOT NULL ORDER BY genero")->fetchAll(PDO::FETCH_COLUMN),
    'estampas' => $pdo->query("SELECT DISTINCT estampa FROM php_bd.produtos WHERE ativo = TRUE AND estampa IS NOT NULL AND estampa != '' ORDER BY estampa")->fetchAll(PDO::FETCH_COLUMN)
];

$sql = "
    SELECT p.*, u.username 
    FROM php_bd.produtos p 
    INNER JOIN php_bd.usuarios u ON p.id_usuario = u.id 
    WHERE p.ativo = TRUE 
";

$params = [];

if (isset($_GET['preco_min']) && $_GET['preco_min'] !== '') {
    $sql .= " AND p.preco >= ?";
    $params[] = $_GET['preco_min'];
}

if (isset($_GET['preco_max']) && $_GET['preco_max'] !== '') {
    $sql .= " AND p.preco <= ?";
    $params[] = $_GET['preco_max'];
}

if (isset($_GET['descricao']) && $_GET['descricao'] !== '') {
    $sql .= " AND p.descricao ILIKE ?";
    $params[] = '%' . $_GET['descricao'] . '%';
}

if (isset($_GET['tamanhos']) && is_array($_GET['tamanhos']) && !empty($_GET['tamanhos'])) {
    $placeholders = str_repeat('?,', count($_GET['tamanhos']) - 1) . '?';
    $sql .= " AND p.tamanho IN ($placeholders)";
    $params = array_merge($params, $_GET['tamanhos']);
}

if (isset($_GET['cores']) && is_array($_GET['cores']) && !empty($_GET['cores'])) {
    $placeholders = str_repeat('?,', count($_GET['cores']) - 1) . '?';
    $sql .= " AND p.cor IN ($placeholders)";
    $params = array_merge($params, $_GET['cores']);
}

if (isset($_GET['generos']) && is_array($_GET['generos']) && !empty($_GET['generos'])) {
    $placeholders = str_repeat('?,', count($_GET['generos']) - 1) . '?';
    $sql .= " AND p.genero IN ($placeholders)";
    $params = array_merge($params, $_GET['generos']);
}

if (isset($_GET['estampas']) && is_array($_GET['estampas']) && !empty($_GET['estampas'])) {
    $placeholders = str_repeat('?,', count($_GET['estampas']) - 1) . '?';
    $sql .= " AND p.estampa IN ($placeholders)";
    $params = array_merge($params, $_GET['estampas']);
}

if (isset($_GET['ultimos_dias']) && $_GET['ultimos_dias'] !== '') {
    $sql .= " AND p.data_publicacao >= CURRENT_DATE - INTERVAL '? days'";
    $params[] = $_GET['ultimos_dias'];
}

$sql .= " ORDER BY p.data_publicacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title><?php echo t('find_products'); ?></title>
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
    <div class="fixed top-4 right-4 z-50 flex space-x-2">
        <button id="filterToggle" class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <span class="text-sm">🔍</span>
        </button>
        <button id="themeToggle" class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <span class="dark:hidden">🌙</span>
            <span class="hidden dark:inline">☀️</span>
        </button>
    </div>

    <div id="filterSidebar" class="fixed left-0 top-0 h-full w-80 bg-light-secondary dark:bg-dark-secondary border-r border-light-border dark:border-dark-border shadow-2xl transform -translate-x-full transition-transform duration-300 z-40 overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-light-text dark:text-dark-text">Filtros</h2>
                <button id="closeFilters" class="p-2 rounded-lg bg-light-primary dark:bg-dark-primary hover:bg-opacity-50 transition">
                    <span class="text-light-text dark:text-dark-text">✕</span>
                </button>
            </div>

            <form id="filterForm" method="GET" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        Pesquisar na descrição
                    </label>
                    <input type="text" name="descricao" value="<?php echo htmlspecialchars($_GET['descricao'] ?? ''); ?>" 
                           placeholder="Digite palavras-chave..."
                           class="w-full px-3 py-2 border border-light-border dark:border-dark-border rounded-lg bg-light-primary dark:bg-dark-primary text-light-text dark:text-dark-text placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        Preço
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input type="number" name="preco_min" value="<?php echo htmlspecialchars($_GET['preco_min'] ?? ''); ?>" 
                                   placeholder="Mínimo"
                                   class="w-full px-3 py-2 border border-light-border dark:border-dark-border rounded-lg bg-light-primary dark:bg-dark-primary text-light-text dark:text-dark-text placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent">
                        </div>
                        <div>
                            <input type="number" name="preco_max" value="<?php echo htmlspecialchars($_GET['preco_max'] ?? ''); ?>" 
                                   placeholder="Máximo"
                                   class="w-full px-3 py-2 border border-light-border dark:border-dark-border rounded-lg bg-light-primary dark:bg-dark-primary text-light-text dark:text-dark-text placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        Publicados nos últimos
                    </label>
                    <select name="ultimos_dias" class="w-full px-3 py-2 border border-light-border dark:border-dark-border rounded-lg bg-light-primary dark:bg-dark-primary text-light-text dark:text-dark-text focus:outline-none focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent">
                        <option value="">Qualquer data</option>
                        <option value="1" <?php echo (isset($_GET['ultimos_dias']) && $_GET['ultimos_dias'] == '1') ? 'selected' : ''; ?>>1 dia</option>
                        <option value="3" <?php echo (isset($_GET['ultimos_dias']) && $_GET['ultimos_dias'] == '3') ? 'selected' : ''; ?>>3 dias</option>
                        <option value="7" <?php echo (isset($_GET['ultimos_dias']) && $_GET['ultimos_dias'] == '7') ? 'selected' : ''; ?>>7 dias</option>
                        <option value="30" <?php echo (isset($_GET['ultimos_dias']) && $_GET['ultimos_dias'] == '30') ? 'selected' : ''; ?>>30 dias</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        Tamanhos
                    </label>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        <?php foreach ($filtros['tamanhos'] as $tamanho): ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="tamanhos[]" value="<?php echo htmlspecialchars($tamanho); ?>" 
                                   <?php echo (isset($_GET['tamanhos']) && in_array($tamanho, $_GET['tamanhos'])) ? 'checked' : ''; ?>
                                   class="rounded border-light-border dark:border-dark-border text-light-accent dark:text-dark-accent focus:ring-light-accent dark:focus:ring-dark-accent">
                            <span class="ml-2 text-sm text-light-text dark:text-dark-text"><?php echo htmlspecialchars($tamanho); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        Cores
                    </label>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        <?php foreach ($filtros['cores'] as $cor): ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="cores[]" value="<?php echo htmlspecialchars($cor); ?>" 
                                   <?php echo (isset($_GET['cores']) && in_array($cor, $_GET['cores'])) ? 'checked' : ''; ?>
                                   class="rounded border-light-border dark:border-dark-border text-light-accent dark:text-dark-accent focus:ring-light-accent dark:focus:ring-dark-accent">
                            <span class="ml-2 text-sm text-light-text dark:text-dark-text"><?php echo htmlspecialchars($cor); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        Gêneros
                    </label>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        <?php foreach ($filtros['generos'] as $genero): ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="generos[]" value="<?php echo htmlspecialchars($genero); ?>" 
                                   <?php echo (isset($_GET['generos']) && in_array($genero, $_GET['generos'])) ? 'checked' : ''; ?>
                                   class="rounded border-light-border dark:border-dark-border text-light-accent dark:text-dark-accent focus:ring-light-accent dark:focus:ring-dark-accent">
                            <span class="ml-2 text-sm text-light-text dark:text-dark-text"><?php echo htmlspecialchars($genero); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        Estampas
                    </label>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        <?php foreach ($filtros['estampas'] as $estampa): ?>
                        <label class="flex items-center">
                            <input type="checkbox" name="estampas[]" value="<?php echo htmlspecialchars($estampa); ?>" 
                                   <?php echo (isset($_GET['estampas']) && in_array($estampa, $_GET['estampas'])) ? 'checked' : ''; ?>
                                   class="rounded border-light-border dark:border-dark-border text-light-accent dark:text-dark-accent focus:ring-light-accent dark:focus:ring-dark-accent">
                            <span class="ml-2 text-sm text-light-text dark:text-dark-text"><?php echo htmlspecialchars($estampa); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-light-accent dark:bg-dark-accent text-white py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        Aplicar
                    </button>
                    <button type="button" id="clearFilters" class="flex-1 bg-gray-500 text-white py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        Limpar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>

    <div class="max-w-6xl mx-auto py-6 px-4">
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 mb-6 border border-light-border dark:border-dark-border transition-all">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-light-text dark:text-dark-text"><?php echo t('find_products'); ?></h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-1"><?php echo t('discover_amazing_t_shirts'); ?></p>
                </div>
                <div class="space-x-3">
                    <a href="../dashboard/dashboard.php" class="bg-light-accent dark:bg-dark-accent text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        <?php echo t('back'); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($produtos)): ?>
                <div class="col-span-full text-center py-12">
                    <h3 class="text-xl font-semibold text-light-text dark:text-dark-text mb-2"><?php echo t('no_products_found'); ?></h3>
                    <p class="text-light-text/70 dark:text-dark-text/70"><?php echo t('be_first_to_advertise'); ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($produtos as $produto): ?>
                <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-lg p-6 border border-light-border dark:border-dark-border hover:shadow-xl transition-all duration-300">
                    
                    <?php if (!empty($produto['foto'])): ?>
                        <div class="mb-4">
                            <img src="../<?php echo htmlspecialchars($produto['foto']); ?>" 
                                 alt="<?php echo t('product_photo'); ?>" 
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
                            <h3 class="font-semibold text-light-text dark:text-dark-text mb-1"><?php echo t('description'); ?></h3>
                            <p class="text-light-text/80 dark:text-dark-text/80 text-sm"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-light-text/70 dark:text-dark-text/70"><?php echo t('color'); ?>:</span>
                                <span class="text-light-text dark:text-dark-text font-medium"><?php echo htmlspecialchars($produto['cor']); ?></span>
                            </div>
                            <?php if (!empty($produto['estampa'])): ?>
                            <div>
                                <span class="text-light-text/70 dark:text-dark-text/70"><?php echo t('print'); ?>:</span>
                                <span class="text-light-text dark:text-dark-text font-medium"><?php echo htmlspecialchars($produto['estampa']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="border-t border-light-border dark:border-dark-border pt-3">
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span class="text-light-text/70 dark:text-dark-text/70"><?php echo t('seller'); ?>:</span>
                                    <span class="text-light-text dark:text-dark-text font-medium"><?php echo htmlspecialchars($produto['username']); ?></span>
                                </div>
                                <span class="text-light-text/50 dark:text-dark-text/50 text-xs">
                                    <?php echo date('d/m/Y', strtotime($produto['data_publicacao'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="iniciar_chat.php?id_produto=<?php echo $produto['id_camiseta']; ?>&id_vendedor=<?php echo $produto['id_usuario']; ?>" 
                           class="block w-full bg-light-accent dark:bg-dark-accent text-white py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm text-center">
                            <?php echo t('contact_seller'); ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const themeToggle = document.getElementById('themeToggle');
        const filterToggle = document.getElementById('filterToggle');
        const closeFilters = document.getElementById('closeFilters');
        const filterSidebar = document.getElementById('filterSidebar');
        const overlay = document.getElementById('overlay');
        const clearFilters = document.getElementById('clearFilters');
        const filterForm = document.getElementById('filterForm');
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

        filterToggle.addEventListener('click', () => {
            filterSidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });

        closeFilters.addEventListener('click', () => {
            filterSidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        overlay.addEventListener('click', () => {
            filterSidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        clearFilters.addEventListener('click', () => {
            window.location.href = window.location.pathname;
        });
    </script>
</body>
</html>