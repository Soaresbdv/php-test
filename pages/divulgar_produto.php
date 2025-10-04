<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/env.php';

redirectIfNotLogged();

$pdo = getDB();
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $tamanho = $_POST['tamanho'] ?? '';
    $cor = $_POST['cor'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $estampa = $_POST['estampa'] ?? '';
    
    try {
        if (empty($descricao) || empty($preco) || empty($tamanho) || empty($cor) || empty($genero)) {
            throw new Exception("Todos os campos obrigatórios devem ser preenchidos!");
        }
        
        if (!is_numeric($preco) || $preco <= 0) {
            throw new Exception("Preço deve ser um valor numérico positivo!");
        }
        
        $stmt = $pdo->prepare("INSERT INTO php_bd.produtos (id_usuario, descricao, preco, tamanho, cor, genero, estampa) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $descricao, $preco, $tamanho, $cor, $genero, $estampa]);     
        $success = "Produto cadastrado com sucesso!";  
        $_POST = array();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Divulgar Produto</title>
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
                    <h1 class="text-2xl font-bold text-light-text dark:text-dark-text">📢 Divulgar Produto</h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-1">Anuncie sua camiseta para a comunidade</p>
                </div>
                <div class="space-x-3">
                    <a href="dashboard.php" class="bg-light-accent dark:bg-dark-accent text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        ← Voltar
                    </a>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="text-red-700 dark:text-red-300">❌ <?php echo $error; ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="text-green-700 dark:text-green-300">✅ <?php echo $success; ?></div>
            </div>
        <?php endif; ?>

        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-8 border border-light-border dark:border-dark-border transition-all">
            <form method="post" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Descrição do Produto *</label>
                    <textarea name="descricao" required rows="3"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                        placeholder="Descreva sua camiseta (cor, estilo, condição, etc.)"><?php echo $_POST['descricao'] ?? ''; ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Preço (R$) *</label>
                        <input type="number" name="preco" step="0.01" min="0" required
                            value="<?php echo $_POST['preco'] ?? ''; ?>"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="Ex: 29.90">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Tamanho *</label>
                        <select name="tamanho" required
                            class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text">
                            <option value="">Selecione o tamanho</option>
                            <option value="PP" <?php echo ($_POST['tamanho'] ?? '') == 'PP' ? 'selected' : ''; ?>>PP</option>
                            <option value="P" <?php echo ($_POST['tamanho'] ?? '') == 'P' ? 'selected' : ''; ?>>P</option>
                            <option value="M" <?php echo ($_POST['tamanho'] ?? '') == 'M' ? 'selected' : ''; ?>>M</option>
                            <option value="G" <?php echo ($_POST['tamanho'] ?? '') == 'G' ? 'selected' : ''; ?>>G</option>
                            <option value="GG" <?php echo ($_POST['tamanho'] ?? '') == 'GG' ? 'selected' : ''; ?>>GG</option>
                            <option value="XG" <?php echo ($_POST['tamanho'] ?? '') == 'XG' ? 'selected' : ''; ?>>XG</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Cor *</label>
                        <input type="text" name="cor" required
                            value="<?php echo $_POST['cor'] ?? ''; ?>"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="Ex: Azul, Vermelha, Preta">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Gênero *</label>
                        <select name="genero" required
                            class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text">
                            <option value="">Selecione o gênero</option>
                            <option value="Masculino" <?php echo ($_POST['genero'] ?? '') == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Feminino" <?php echo ($_POST['genero'] ?? '') == 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                            <option value="Unissex" <?php echo ($_POST['genero'] ?? '') == 'Unissex' ? 'selected' : ''; ?>>Unissex</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Tipo de Estampa (Opcional)</label>
                    <input type="text" name="estampa"
                        value="<?php echo $_POST['estampa'] ?? ''; ?>"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                        placeholder="Ex: Lisa, Estampada, Personalizada, etc.">
                </div>

                <button type="submit" 
                        class="w-full bg-light-accent dark:bg-dark-accent text-white py-4 rounded-xl font-semibold hover:opacity-90 transform hover:-translate-y-0.5 transition-all duration-300 shadow-lg">
                    Publicar Produto
                </button>
            </form>
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