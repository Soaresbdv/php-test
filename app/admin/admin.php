<?php
require_once '../../includes/bootstrap.php';
if (!isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$pdo = getDB();
$error = '';
$success = '';

if (isset($_POST['adicionar'])) {
    $cpf = $_POST['cpf'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $admin = isset($_POST['admin']) ? 1 : 0;

    try {
        if (strlen($password) < 6) {
            throw new Exception("Senha deve ter 6+ caracteres");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO php_bd.usuarios (cpf, username, password, admin) VALUES (?, ?, ?, ?)");
        $stmt->execute([$cpf, $username, $hashedPassword, $admin]);

        $success = "Usuário adicionado com sucesso!";
    } catch (Exception $e) {
        $error = "Erro ao adicionar: " . $e->getMessage();
    }
}

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $cpf = $_POST['cpf'];
    $admin = isset($_POST['admin']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE php_bd.usuarios SET username = ?, cpf = ?, admin = ? WHERE id = ?");
        $stmt->execute([$username, $cpf, $admin, $id]);
        $success = "Usuário atualizado com sucesso!";
    } catch (Exception $e) {
        $error = "Erro ao atualizar: " . $e->getMessage();
    }
}

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    
    if ($id == $_SESSION['user_id']) {
        $error = "Você não pode excluir sua própria conta!";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM php_bd.usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Usuário excluído com sucesso!";
        } catch (Exception $e) {
            $error = "Erro ao excluir: " . $e->getMessage();
        }
    }
}

if (isset($_GET['toggle_admin'])) {
    $id = $_GET['toggle_admin'];
    if ($id == $_SESSION['user_id']) {
        $error = "Você não pode remover seus próprios privilégios de admin!";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE php_bd.usuarios SET admin = NOT admin WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Status de admin alterado com sucesso!";
        } catch (Exception $e) {
            $error = "Erro ao alterar status: " . $e->getMessage();
        }
    }
}

$stmt = $pdo->query("SELECT * FROM php_bd.usuarios ORDER BY id");
$usuarios = $stmt->fetchAll();
$editar_usuario = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM php_bd.usuarios WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $editar_usuario = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Gerenciar Usuários - Admin</title>
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

    <div class="max-w-7xl mx-auto py-6 px-4">
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 mb-6 border border-light-border dark:border-dark-border transition-all">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-light-text dark:text-dark-text">Gerenciar Usuários</h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-1">Painel administrativo - CRUD completo</p>
                </div>
                <div class="space-x-3">
                    <a href="../dashboard/dashboard.php" class="bg-light-accent dark:bg-dark-accent text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition text-sm">
                        Voltar
                    </a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600 transition text-sm">
                        Sair
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
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 mb-6 border border-light-border dark:border-dark-border transition-all">
            <h2 class="text-xl font-bold text-light-text dark:text-dark-text mb-4">
                <?php echo $editar_usuario ? '✏️ Editar Usuário' : '➕ Adicionar Usuário'; ?>
            </h2>
            
            <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if ($editar_usuario): ?>
                    <input type="hidden" name="id" value="<?php echo $editar_usuario['id']; ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">CPF:</label>
                    <input type="text" name="cpf" required maxlength="11"
                           value="<?php echo $editar_usuario ? $editar_usuario['cpf'] : ''; ?>"
                           class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-lg focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                           placeholder="Digite o CPF">
                </div>

                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Nome:</label>
                    <input type="text" name="username" required
                           value="<?php echo $editar_usuario ? $editar_usuario['username'] : ''; ?>"
                           class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-lg focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                           placeholder="Digite o nome">
                </div>

                <?php if (!$editar_usuario): ?>
                <div>
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">Senha:</label>
                    <input type="password" name="password" required
                           class="w-full px-3 py-2 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border rounded-lg focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent focus:border-transparent transition text-light-text dark:text-dark-text placeholder-gray-500 dark:placeholder-gray-400"
                           placeholder="Mínimo 6 caracteres">
                </div>
                <?php endif; ?>

                <div class="flex items-center">
                    <input type="checkbox" name="admin" id="admin" 
                           <?php echo ($editar_usuario && $editar_usuario['admin']) ? 'checked' : ''; ?>
                           class="w-4 h-4 text-light-accent dark:text-dark-accent border-light-border dark:border-dark-border rounded focus:ring-light-accent dark:focus:ring-dark-accent bg-white dark:bg-gray-800">
                    <label for="admin" class="ml-2 text-sm font-medium text-light-text dark:text-dark-text">Usuário Administrador</label>
                </div>

                <div class="md:col-span-2 flex space-x-3">
                    <button type="submit" name="<?php echo $editar_usuario ? 'editar' : 'adicionar'; ?>"
                            class="bg-light-accent dark:bg-dark-accent text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transform hover:-translate-y-0.5 transition-all duration-300 shadow-lg text-sm">
                        <?php echo $editar_usuario ? '💾 Atualizar' : '➕ Adicionar'; ?>
                    </button>
                    
                    <?php if ($editar_usuario): ?>
                        <a href="admin.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-600 transition text-sm">
                            ❌ Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-6 border border-light-border dark:border-dark-border transition-all">
            <h2 class="text-xl font-bold text-light-text dark:text-dark-text mb-4">Lista de Usuários</h2>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-white dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-light-text dark:text-dark-text uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-light-text dark:text-dark-text uppercase tracking-wider">CPF</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-light-text dark:text-dark-text uppercase tracking-wider">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-light-text dark:text-dark-text uppercase tracking-wider">Admin</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-light-text dark:text-dark-text uppercase tracking-wider">Data Cadastro</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-light-text dark:text-dark-text uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-light-border dark:divide-dark-border">
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr class="hover:bg-white dark:hover:bg-gray-800 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-light-text dark:text-dark-text"><?php echo $usuario['id']; ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-light-text dark:text-dark-text"><?php echo $usuario['cpf']; ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-light-text dark:text-dark-text"><?php echo htmlspecialchars($usuario['username']); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <?php if ($usuario['admin']): ?>
                                    <span class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 rounded-full text-xs font-semibold">✅ Admin</span>
                                <?php else: ?>
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded-full text-xs font-semibold">❌ Usuário</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-light-text dark:text-dark-text">
                                <?php echo date('d/m/Y H:i', strtotime($usuario['create_at'])); ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium space-x-1">
                                <a href="admin.php?editar=<?php echo $usuario['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded text-xs transition-colors">✏️</a>
                                
                                <a href="admin.php?toggle_admin=<?php echo $usuario['id']; ?>" 
                                   class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 bg-purple-50 dark:bg-purple-900/30 px-2 py-1 rounded text-xs transition-colors">
                                    <?php echo $usuario['admin'] ? '👤' : '👑'; ?>
                                </a>
                                
                                <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                    <a href="admin.php?excluir=<?php echo $usuario['id']; ?>" 
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')"
                                       class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded text-xs transition-colors">🗑️</a>
                                <?php else: ?>
                                    <span class="text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">👤 Você</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

        function confirmAction(message) {
            return confirm(message);
        }
    </script>
</body>
</html>