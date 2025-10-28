<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/services/ImageStorage.php';

redirectIfNotLogged();

$pdo = getDB();
$error = '';
$success = '';
$uploadDir = '../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $tamanho = $_POST['tamanho'] ?? '';
    $cor = $_POST['cor'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $estampa = $_POST['estampa'] ?? '';
    $foto = $_FILES['foto'] ?? null;
    
    try {
        if (empty($descricao) || empty($preco) || empty($tamanho) || empty($cor) || empty($genero)) {
            throw new Exception(t('all_fields_required'));
        }
        
        if (!is_numeric($preco) || $preco <= 0) {
            throw new Exception(t('positive_price_required'));
        }
        
        $fotoPath = null;
        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        
            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception(t('valid_image_formats'));
            }
        
            if ($foto['size'] > 5 * 1024 * 1024) {
                throw new Exception(t('image_size_limit_5mb'));
            }

            $filename = uniqid() . '_' . time() . '.' . $extension;
            $fotoPath = 'uploads/' . $filename;
            $fullPath = '../' . $fotoPath;
            
            if (!move_uploaded_file($foto['tmp_name'], $fullPath)) {
                throw new Exception(t('upload_error'));
            }
        } else if ($foto && $foto['error'] !== UPLOAD_ERR_NO_FILE) {
            throw new Exception(t('upload_error_code') . ": " . $foto['error']);
        }
        
        $stmt = $pdo->prepare("INSERT INTO php_bd.produtos (id_usuario, descricao, preco, tamanho, cor, genero, estampa, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $descricao, $preco, $tamanho, $cor, $genero, $estampa, $fotoPath]);
        $success = t('product_registered_success');
        $_POST = array();
        
    } catch (Exception $e) {
        if (isset($fullPath) && file_exists($fullPath)) {
            unlink($fullPath);
        }
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title><?php echo t('advertise_product'); ?></title>
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

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagePreview');
            const previewContainer = document.getElementById('imagePreviewContainer');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('hidden');
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
                    <h1 class="text-2xl font-bold text-light-text dark:text-dark-text"><?php echo t('advertise_product'); ?></h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-1"><?php echo t('advertise_product_desc'); ?></p>
                </div>
                <div class="space-x-3">
                    <a href="dashboard.php" class="bg-light-accent dark:bg-dark-accent text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        <?php echo t('back'); ?>
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
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        <?php echo t('product_photo'); ?> (<?php echo t('optional'); ?>)
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-light-border dark:border-dark-border rounded-xl cursor-pointer hover:border-light-accent dark:hover:border-dark-accent transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-light-text/50 dark:text-dark-text/50" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p class="mb-2 text-sm text-light-text/50 dark:text-dark-text/50">
                                    <span class="font-semibold"><?php echo t('click_to_upload'); ?></span>
                                </p>
                                <p class="text-xs text-light-text/50 dark:text-dark-text/50"><?php echo t('image_formats_large'); ?></p>
                            </div>
                            <input id="foto" name="foto" type="file" class="hidden" accept="image/*" onchange="previewImage(event)" />
                        </label>
                    </div>
                    
                    <div id="imagePreviewContainer" class="hidden mt-4">
                        <p class="text-sm font-medium text-light-text dark:text-dark-text mb-2"><?php echo t('preview'); ?>:</p>
                        <div class="flex justify-center">
                            <img id="imagePreview" class="max-w-xs max-h-48 rounded-lg shadow-md" src="" alt="<?php echo t('image_preview'); ?>">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        <?php echo t('product_description'); ?> *
                    </label>
                    <textarea name="descricao" required rows="3"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                        placeholder="<?php echo t('describe_product_placeholder'); ?>"><?php echo $_POST['descricao'] ?? ''; ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                            <?php echo t('price'); ?> (R$) *
                        </label>
                        <input type="number" name="preco" step="0.01" min="0" required
                            value="<?php echo $_POST['preco'] ?? ''; ?>"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="<?php echo t('price_placeholder'); ?>">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                            <?php echo t('size'); ?> *
                        </label>
                        <select name="tamanho" required
                            class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text">
                            <option value=""><?php echo t('select_size'); ?></option>
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
                        <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                            <?php echo t('color'); ?> *
                        </label>
                        <input type="text" name="cor" required
                            value="<?php echo $_POST['cor'] ?? ''; ?>"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="<?php echo t('color_placeholder'); ?>">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                            <?php echo t('gender'); ?> *
                        </label>
                        <select name="genero" required
                            class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text">
                            <option value=""><?php echo t('select_gender'); ?></option>
                            <option value="Masculino" <?php echo ($_POST['genero'] ?? '') == 'Masculino' ? 'selected' : ''; ?>><?php echo t('male'); ?></option>
                            <option value="Feminino" <?php echo ($_POST['genero'] ?? '') == 'Feminino' ? 'selected' : ''; ?>><?php echo t('female'); ?></option>
                            <option value="Unissex" <?php echo ($_POST['genero'] ?? '') == 'Unissex' ? 'selected' : ''; ?>><?php echo t('unisex'); ?></option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                        <?php echo t('print_type'); ?> (<?php echo t('optional'); ?>)
                    </label>
                    <input type="text" name="estampa"
                        value="<?php echo $_POST['estampa'] ?? ''; ?>"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                        placeholder="<?php echo t('print_placeholder'); ?>">
                </div>

                <button type="submit" 
                        class="w-full bg-light-accent dark:bg-dark-accent text-white py-4 rounded-xl font-semibold hover:opacity-90 transform hover:-translate-y-0.5 transition-all duration-300 shadow-lg">
                    <?php echo t('publish_product'); ?>
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