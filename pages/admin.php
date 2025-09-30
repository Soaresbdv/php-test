<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/env.php';
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
        $stmt = $pdo->prepare("INSERT INTO usuarios (cpf, username, password, admin) VALUES (?, ?, ?, ?)");
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
        $stmt = $pdo->prepare("UPDATE usuarios SET username = ?, cpf = ?, admin = ? WHERE id = ?");
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
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
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
            $stmt = $pdo->prepare("UPDATE usuarios SET admin = NOT admin WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Status de admin alterado com sucesso!";
        } catch (Exception $e) {
            $error = "Erro ao alterar status: " . $e->getMessage();
        }
    }
}
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id");
$usuarios = $stmt->fetchAll();
$editar_usuario = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $editar_usuario = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gerenciar Usuários - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto py-8 px-4">
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800"> Gerenciar Usuários</h1>
                    <p class="text-gray-600 mt-2">Painel administrativo - CRUD completo</p>
                </div>
                <div class="space-x-4">
                    <a href="dashboard.php" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                        Voltar ao Dashboard
                    </a>
                    <a href="logout.php" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition">
                         Sair
                    </a>
                </div>
            </div>
        </div>
        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="text-red-700">❌ <?php echo $error; ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="text-green-700"> <?php echo $success; ?></div>
            </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <?php echo $editar_usuario ? '✏️ Editar Usuário' : '➕ Adicionar Usuário'; ?>
            </h2>
            
            <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if ($editar_usuario): ?>
                    <input type="hidden" name="id" value="<?php echo $editar_usuario['id']; ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CPF:</label>
                    <input type="text" name="cpf" required maxlength="11"
                           value="<?php echo $editar_usuario ? $editar_usuario['cpf'] : ''; ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="Digite o CPF">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome:</label>
                    <input type="text" name="username" required
                           value="<?php echo $editar_usuario ? $editar_usuario['username'] : ''; ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="Digite o nome">
                </div>

                <?php if (!$editar_usuario): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Senha:</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                           placeholder="Mínimo 6 caracteres">
                </div>
                <?php endif; ?>

                <div class="flex items-center">
                    <input type="checkbox" name="admin" id="admin" 
                           <?php echo ($editar_usuario && $editar_usuario['admin']) ? 'checked' : ''; ?>
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="admin" class="ml-2 text-sm font-medium text-gray-700">Usuário Administrador</label>
                </div>

                <div class="md:col-span-2 flex space-x-4">
                    <button type="submit" name="<?php echo $editar_usuario ? 'editar' : 'adicionar'; ?>"
                            class="bg-blue-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-600 transform hover:-translate-y-1 transition duration-300">
                        <?php echo $editar_usuario ? '💾 Atualizar' : '➕ Adicionar'; ?>
                    </button>
                    
                    <?php if ($editar_usuario): ?>
                        <a href="admin.php" class="bg-gray-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-600 transition">
                         Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Lista de Usuários</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPF</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Cadastro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $usuario['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $usuario['cpf']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($usuario['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($usuario['admin']): ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">✅ Admin</span>
                                <?php else: ?>
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold">❌ Usuário</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo date('d/m/Y H:i', strtotime($usuario['create_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="admin.php?editar=<?php echo $usuario['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded">✏️</a>
                                
                                <a href="admin.php?toggle_admin=<?php echo $usuario['id']; ?>" 
                                   class="text-purple-600 hover:text-purple-900 bg-purple-50 px-3 py-1 rounded">
                                    <?php echo $usuario['admin'] ? '👤' : '👑'; ?>
                                </a>
                                
                                <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                    <a href="admin.php?excluir=<?php echo $usuario['id']; ?>" 
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')"
                                       class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded">🗑️</a>
                                <?php else: ?>
                                    <span class="text-gray-400 bg-gray-100 px-3 py-1 rounded">👤 Você</span>
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
        function confirmAction(message) {
            return confirm(message);
        }
    </script>
</body>
</html>