<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/env.php';

redirectIfNotLogged();

// Buscar dados do usuário
$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Informações do sistema do .env
$appName = Env::get('APP_NAME', 'Sistema');
$appUrl = Env::get('APP_URL', 'http://localhost:8000');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - <?php echo $appName; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-12 px-6">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800"><?php echo $appName; ?></h1>
                <p class="text-gray-600 mt-2">Sistema seguro de autenticação</p>
            </div>
            
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-6">Logado com Sucesso!!!!!</h2>
            
            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg mb-8">
                <div class="text-green-700 text-lg font-semibold">Você acessou o sistema com sucesso!</div>
            </div>
            
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">👤 Seus Dados</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">ID</p>
                        <p class="font-medium"><?php echo $user['id']; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Nome</p>
                        <p class="font-medium"><?php echo htmlspecialchars($user['username']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">CPF</p>
                        <p class="font-medium"><?php echo htmlspecialchars($user['cpf']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Data de Cadastro</p>
                        <p class="font-medium"><?php echo date('d/m/Y H:i', strtotime($user['create_at'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Informações do Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nome do Sistema</p>
                        <p class="font-medium"><?php echo $appName; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">URL</p>
                        <p class="font-medium"><?php echo $appUrl; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Ambiente</p>
                        <p class="font-medium"><?php echo Env::get('APP_ENV', 'Desenvolvimento'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Banco de Dados</p>
                        <p class="font-medium"><?php echo Env::get('DB_NAME', 'dev_db'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="text-center space-x-4">
                <a href="logout.php" 
                   class="inline-flex items-center px-6 py-3 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transform hover:-translate-y-1 transition duration-300">
                     Sair do Sistema
                </a>           
            </div>
        </div>
    </div>
</body>
</html>