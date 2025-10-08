<?php
require_once '../../includes/bootstrap.php';
redirectIfNotLogged();

$id_conversa = $_GET['id_conversa'] ?? null;
$id_usuario_logado = $_SESSION['user_id'];

if (!$id_conversa) {
    header('Location: encontrar_produto.php');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("
    SELECT c.*, p.descricao as produto_titulo, p.preco, p.foto, 
           ua.username as anunciante_nome, ui.username as interessado_nome,
           ua.id as id_anunciante, ui.id as id_interessado
    FROM php_bd.conversas c
    INNER JOIN php_bd.produtos p ON c.id_produto = p.id_camiseta
    INNER JOIN php_bd.usuarios ua ON c.id_usuario_anunciante = ua.id
    INNER JOIN php_bd.usuarios ui ON c.id_usuario_interessado = ui.id
    WHERE c.id = ? AND c.ativa = TRUE
");
$stmt->execute([$id_conversa]);
$conversa = $stmt->fetch();

if (!$conversa) {
    header('Location: encontrar_produto.php');
    exit;
}

if ($conversa['id_anunciante'] != $id_usuario_logado && $conversa['id_interessado'] != $id_usuario_logado) {
    header('Location: encontrar_produto.php');
    exit;
}

$outro_usuario_id = ($conversa['id_anunciante'] == $id_usuario_logado) ? $conversa['id_interessado'] : $conversa['id_anunciante'];
$outro_usuario_nome = ($conversa['id_anunciante'] == $id_usuario_logado) ? $conversa['interessado_nome'] : $conversa['anunciante_nome'];
$stmt = $pdo->prepare("
    SELECT m.*, u.username 
    FROM php_bd.mensagens m 
    INNER JOIN php_bd.usuarios u ON m.id_usuario_remetente = u.id 
    WHERE m.id_conversa = ? 
    ORDER BY m.data_envio ASC
");
$stmt->execute([$id_conversa]);
$mensagens = $stmt->fetchAll();

// Marcar mensagens como lidas
$stmt = $pdo->prepare("UPDATE php_bd.mensagens SET lida = TRUE WHERE id_conversa = ? AND id_usuario_remetente != ? AND lida = FALSE");
$stmt->execute([$id_conversa, $id_usuario_logado]);

// Processar envio de nova mensagem
if (isset($_POST['mensagem']) && !empty(trim($_POST['mensagem']))) {
    $mensagem = trim($_POST['mensagem']);
    $stmt = $pdo->prepare("INSERT INTO php_bd.mensagens (id_conversa, id_usuario_remetente, mensagem) VALUES (?, ?, ?)");
    $stmt->execute([$id_conversa, $id_usuario_logado, $mensagem]);
    
    header("Location: chat.php?id_conversa=$id_conversa");
    exit;
}

$preco_formatado = isset($conversa['preco']) ? number_format(floatval($conversa['preco']), 2, ',', '.') : '0,00';
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Chat - <?php echo htmlspecialchars($conversa['produto_titulo'] ?? 'Produto'); ?></title>
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

    <div class="max-w-4xl mx-auto h-full flex flex-col">
        <div class="bg-light-secondary dark:bg-dark-secondary border-b border-light-border dark:border-dark-border p-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="minhas_conversas.php" class="text-light-accent dark:text-dark-accent hover:opacity-80">
                        Voltar
                    </a>
                    <div>
                        <h1 class="font-semibold text-light-text dark:text-dark-text"><?php echo htmlspecialchars($outro_usuario_nome); ?></h1>
                        <p class="text-sm text-light-text/70 dark:text-dark-text/70">Conversando sobre: <?php echo htmlspecialchars($conversa['produto_titulo'] ?? 'Produto'); ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-light-accent dark:text-dark-accent">
                        R$ <?php echo $preco_formatado; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="mensagensContainer">
            <?php if (empty($mensagens)): ?>
                <div class="text-center py-8">
                    <div class="text-4xl mb-2">💬</div>
                    <p class="text-light-text/70 dark:text-dark-text/70">Inicie a conversa sobre este produto!</p>
                </div>
            <?php else: ?>
                <?php foreach ($mensagens as $msg): ?>
                <div class="flex <?php echo ($msg['id_usuario_remetente'] == $id_usuario_logado) ? 'justify-end' : 'justify-start'; ?>">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-2xl <?php echo ($msg['id_usuario_remetente'] == $id_usuario_logado) ? 'bg-light-accent text-white rounded-br-none' : 'bg-light-border dark:bg-dark-border text-light-text dark:text-dark-text rounded-bl-none'; ?>">
                        <p class="text-sm"><?php echo htmlspecialchars($msg['mensagem']); ?></p>
                        <p class="text-xs mt-1 opacity-70 text-right">
                            <?php echo date('H:i', strtotime($msg['data_envio'])); ?>
                            <?php if ($msg['id_usuario_remetente'] == $id_usuario_logado && $msg['lida']): ?>
                                ✓✓
                            <?php elseif ($msg['id_usuario_remetente'] == $id_usuario_logado): ?>
                                ✓
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="bg-light-secondary dark:bg-dark-secondary border-t border-light-border dark:border-dark-border p-4">
            <form method="POST" class="flex space-x-2">
                <input type="text" 
                       name="mensagem" 
                       placeholder="Digite sua mensagem..." 
                       class="flex-1 px-4 py-2 rounded-full border border-light-border dark:border-dark-border bg-white dark:bg-dark-primary text-light-text dark:text-dark-text focus:outline-none focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent"
                       required>
                <button type="submit" 
                        class="px-6 py-2 bg-light-accent dark:bg-dark-accent text-white rounded-full font-semibold hover:opacity-90 transition">
                    Enviar
                </button>
            </form>
        </div>
    </div>

    <script>
        const mensagensContainer = document.getElementById('mensagensContainer');
        if (mensagensContainer) {
            mensagensContainer.scrollTop = mensagensContainer.scrollHeight;
        }

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