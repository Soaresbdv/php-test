<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

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
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "CPF ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Login</h2>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="text-red-700"><?php echo $error; ?></div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registro']) && $_GET['registro'] == 'sucesso'): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="text-green-700">Cadastro realizado com sucesso! Faça login.</div>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CPF:</label>
                <input type="text" name="cpf" required maxlength="11" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="Digite seu CPF">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Senha:</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                       placeholder="Digite sua senha">
            </div>
            
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transform hover:-translate-y-1 transition duration-300">
                Entrar
            </button>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-gray-600">Não tem conta? 
                <a href="register.php" class="text-blue-500 hover:text-blue-700 font-semibold transition">Registre-se aqui</a>
            </p>
        </div>
    </div>
</body>
</html>