<?php
require_once '../../includes/bootstrap.php';

redirectIfNotLogged();

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM php_bd.usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'] ?? 'pt';
    
    try {
        $updateStmt = $pdo->prepare("UPDATE php_bd.usuarios SET language = ? WHERE id = ?");
        $updateStmt->execute([$language, $_SESSION['user_id']]);
        $_SESSION['user_language'] = $language;

        setcookie('user_language', $language, time() + (365 * 24 * 60 * 60), '/');
        header("Location: configuracoes.php?success=1");

        exit;
        
    } catch (Exception $e) {
        $message = t('settings_error') . ': ' . $e->getMessage();
        $message_type = 'error';
    }
}

?>

<!DOCTYPE html>
<html class="h-full" lang="<?php echo $_SESSION['user_language'] ?? 'pt'; ?>">
<head>
    <title><?php echo t('settings'); ?> - Dashboard</title>
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
    <div class="fixed top-4 left-4 z-50">
        <a href="dashboard.php" 
           class="w-10 h-10 flex items-center justify-center bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform rounded-full text-lg font-bold">
            ←
        </a>
    </div>

    <div class="fixed top-4 right-4 z-50">
        <button id="themeToggle" class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <span class="dark:hidden">🌙</span>
            <span class="hidden dark:inline">☀️</span>
        </button>
    </div>

    <?php if ($message || isset($_GET['success'])): ?>
        <div id="alertMessage" class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md p-4 rounded-lg <?php echo ($message_type === 'success' || isset($_GET['success'])) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-300 dark:border-green-700' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border border-red-300 dark:border-red-700'; ?> shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <?php if ($message_type === 'success' || isset($_GET['success'])): ?>
                        <span class="text-green-500 mr-2">✅</span>
                    <?php else: ?>
                        <span class="text-red-500 mr-2">❌</span>
                    <?php endif; ?>
                    <span><?php echo isset($_GET['success']) ? t('settings_saved') : htmlspecialchars($message); ?></span>
                </div>
                <button onclick="closeAlert()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 ml-4">
                    ✕
                </button>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mx-auto px-4 py-8 pt-20">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-light-text dark:text-dark-text mb-8 text-center">
                <?php echo t('settings'); ?>
            </h1>

            <div class="bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-lg border border-light-border dark:border-dark-border p-6">
                <form method="POST" class="space-y-6">
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-light-text dark:text-dark-text border-b border-light-border dark:border-dark-border pb-2">
                            <?php echo t('notifications'); ?>
                        </h3>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-light-text dark:text-dark-text font-medium"><?php echo t('email_notifications'); ?></p>
                                <p class="text-sm text-light-text/70 dark:text-dark-text/70"><?php echo t('email_notifications_desc'); ?></p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-light-accent dark:peer-checked:bg-dark-accent"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-light-text dark:text-dark-text font-medium"><?php echo t('message_notifications'); ?></p>
                                <p class="text-sm text-light-text/70 dark:text-dark-text/70"><?php echo t('message_notifications_desc'); ?></p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="message_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-light-accent dark:peer-checked:bg-dark-accent"></div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-light-text dark:text-dark-text border-b border-light-border dark:border-dark-border pb-2">
                            <?php echo t('privacy'); ?>
                        </h3>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-light-text dark:text-dark-text font-medium"><?php echo t('public_profile'); ?></p>
                                <p class="text-sm text-light-text/70 dark:text-dark-text/70"><?php echo t('public_profile_desc'); ?></p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="public_profile" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-light-accent dark:peer-checked:bg-dark-accent"></div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-light-text dark:text-dark-text border-b border-light-border dark:border-dark-border pb-2">
                            <?php echo t('preferences'); ?>
                        </h3>
                        
                        <div>
                            <label class="block text-light-text dark:text-dark-text font-medium mb-2">
                                <?php echo t('language'); ?>
                            </label>
                            <select name="language" class="w-full bg-light-primary dark:bg-dark-primary border border-light-border dark:border-dark-border rounded-lg px-4 py-2 text-light-text dark:text-dark-text focus:outline-none focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent">
                                <option value="pt" <?php echo ($_SESSION['user_language'] ?? 'pt') === 'pt' ? 'selected' : ''; ?>>
                                    <?php echo t('portuguese'); ?>
                                </option>
                                <option value="en" <?php echo ($_SESSION['user_language'] ?? 'pt') === 'en' ? 'selected' : ''; ?>>
                                    <?php echo t('english'); ?>
                                </option>
                                <option value="es" <?php echo ($_SESSION['user_language'] ?? 'pt') === 'es' ? 'selected' : ''; ?>>
                                    <?php echo t('spanish'); ?>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-light-accent dark:bg-dark-accent text-white py-3 px-4 rounded-lg font-semibold hover:opacity-90 transition-opacity">
                            💾 <?php echo t('save_settings'); ?>
                        </button>
                    </div>
                </form>
            </div>
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

        function closeAlert() {
            const alert = document.getElementById('alertMessage');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(-50%) translateY(-20px)';
                alert.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }
 
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('alertMessage');
            
            if (alert) {
                setTimeout(() => {
                    closeAlert();
                }, 2000);
                
                alert.addEventListener('click', function(e) {
                    if (!e.target.closest('button')) {
                        closeAlert();
                    }
                });
            }
        });
    </script>
</body>
</html>