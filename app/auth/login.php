<?php
require_once '../../includes/bootstrap.php';

if (isLoggedIn() && !isset($_POST['cpf'])) {
    session_destroy();
    $_SESSION = array();
}

redirectIfLogged();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST['cpf'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (attemptLogin($cpf, $password)) {
        header("Location: ../dashboard/dashboard.php");
        exit;
    } else {
        $error = "CPF ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Login</title>
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

    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-light-secondary dark:bg-dark-secondary rounded-3xl shadow-2xl p-8 border border-light-border dark:border-dark-border transition-all duration-300">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-light-text dark:text-dark-text mb-2">Login</h2>
                    <p class="text-light-text/70 dark:text-dark-text/70">Acesse sua conta</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="mt-6 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded">
                        <div class="text-red-700 dark:text-red-300"><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registro']) && $_GET['registro'] == 'sucesso'): ?>
                    <div class="mt-6 bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 p-4 rounded">
                        <div class="text-green-700 dark:text-green-300">Cadastro realizado com sucesso! Faça login.</div>
                    </div>
                <?php endif; ?>

                <form method="post" class="mt-8 space-y-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">CPF</label>
                            <input type="text" name="cpf" required maxlength="11" 
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                                   placeholder="Digite seu CPF">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Senha</label>
                            <input type="password" name="password" required 
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-xl focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                                   placeholder="Digite sua senha">
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-light-accent dark:bg-dark-accent text-white py-4 rounded-xl font-semibold hover:opacity-90 transform hover:-translate-y-0.5 transition-all duration-300 shadow-lg">
                        Entrar
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-light-text/70 dark:text-dark-text/70">
                        Não tem conta? 
                        <a href="register.php" class="text-light-accent dark:text-dark-accent font-semibold hover:underline transition">
                            Registre-se aqui
                        </a>
                    </p>
                </div>
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
    </script>
</body>
</html>