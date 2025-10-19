<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
requireLogin();

$pdo = getDB();
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM php_bd.usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "Usuário não encontrado.";
    header('Location: dashboard.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $complemento = $_POST['complemento'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';
    $foto_perfil = $user['foto_perfil'];
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $imageStorage = new ImageStorage();
        $uploadResult = $imageStorage->uploadProfileImage($_FILES['foto_perfil'], $user_id);
        
        if ($uploadResult['success']) {
            $foto_perfil = $uploadResult['file_path'];
        } else {
            $_SESSION['error'] = $uploadResult['error'];
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE php_bd.usuarios SET 
                email = ?, 
                telefone = ?, 
                foto_perfil = ?, 
                cep = ?, 
                endereco = ?, 
                numero = ?, 
                complemento = ?, 
                bairro = ?, 
                cidade = ?, 
                estado = ?,
                perfil_completo = true,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");

        $stmt->execute([
            $email, $telefone, $foto_perfil, $cep, $endereco, 
            $numero, $complemento, $bairro, $cidade, $estado, $user_id
        ]);

        $_SESSION['success'] = "Perfil atualizado com sucesso!";
        header('Location: editar_perfil.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Erro ao atualizar perfil: " . $e->getMessage();
        header('Location: editar_perfil.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Editar Perfil</title>
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

        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userMenu');
            const gear = document.getElementById('userGear');
            
            if (menu && gear && !menu.contains(event.target) && !gear.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</head>
<body class="h-full bg-light-primary dark:bg-dark-primary transition-colors duration-300">
    <div class="fixed top-4 left-4 z-50">
        <button id="userGear" onclick="toggleUserMenu()" 
                class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            ⚙️
        </button>
        
        <div id="userMenu" class="hidden absolute left-0 top-12 mt-2 w-48 bg-light-secondary dark:bg-dark-secondary rounded-xl shadow-2xl border border-light-border dark:border-dark-border transition-all duration-300">
            <div class="p-4 border-b border-light-border dark:border-dark-border">
                <p class="text-sm font-semibold text-light-text dark:text-dark-text">Olá, <?php echo htmlspecialchars($user['username']); ?>!</p>
                <p class="text-xs text-light-text/70 dark:text-dark-text/70 mt-1"><?php echo htmlspecialchars($user['cpf']); ?></p>
            </div>
            
            <div class="p-2">
                <a href="meus_produtos.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    Meus Produtos
                </a>
                
                <a href="editar_perfil.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-accent dark:text-dark-accent bg-light-accent/10 dark:bg-dark-accent/10 rounded-lg transition-colors">
                    Editar Perfil
                </a>
                
                <a href="configuracoes.php" 
                   class="flex items-center px-3 py-2 text-sm text-light-text dark:text-dark-text hover:bg-light-accent/10 dark:hover:bg-dark-accent/10 rounded-lg transition-colors">
                    Configurações
                </a>
            </div>
            
            <div class="p-2 border-t border-light-border dark:border-dark-border">
                <a href="../auth/logout.php" 
                   class="flex items-center px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                    Sair
                </a>
            </div>
        </div>
    </div>

    <div class="fixed top-4 right-4 z-50">
        <button id="themeToggle" class="p-2 rounded-full bg-light-accent dark:bg-dark-accent text-white shadow-lg hover:scale-110 transition-transform">
            <span class="dark:hidden">🌙</span>
            <span class="hidden dark:inline">☀️</span>
        </button>
    </div>

    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-8 mb-8 border border-light-border dark:border-dark-border transition-all">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-light-text dark:text-dark-text">Editar Perfil</h1>
                    <p class="text-light-text/70 dark:text-dark-text/70 mt-2">Atualize suas informações pessoais</p>
                </div>
                <div>
                    <a href="dashboard.php" class="bg-gray-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-gray-600 transition">
                        Voltar
                    </a>
                </div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="text-red-700 dark:text-red-300">❌ <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?></div>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="text-green-700 dark:text-green-300">✅ <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?></div>
            </div>
        <?php endif; ?>

        <div class="bg-light-secondary dark:bg-dark-secondary rounded-2xl shadow-xl p-8 border border-light-border dark:border-dark-border transition-all">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="text-center">
                    <label class="block text-sm font-medium text-light-text dark:text-dark-text mb-4">Foto de Perfil</label>
                        
                    <div id="dropZone" 
                         class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-dashed border-light-border dark:border-dark-border hover:border-light-accent dark:hover:border-dark-accent transition-colors duration-300 cursor-pointer flex items-center justify-center overflow-hidden relative group">
                        
                        <?php if (!empty($user['foto_perfil'])): ?>
                            <img src="/<?= htmlspecialchars($user['foto_perfil']) ?>" 
                                 alt="Foto de Perfil" 
                                 id="profileImage"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <span class="text-white text-sm font-semibold">Alterar</span>
                            </div>
                        <?php else: ?>
                            <div id="placeholder" class="text-gray-500 dark:text-gray-400 text-4xl group-hover:text-light-accent dark:group-hover:text-dark-accent transition-colors">
                                👤
                            </div>
                            <img id="profileImage" src="" alt="Foto de Perfil" class="w-full h-full object-cover hidden">
                        <?php endif; ?>
                        
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" class="hidden">
                    </div>
                        
                    <p class="text-xs text-light-text/50 dark:text-dark-text/50 mt-1">PNG, JPG até 2MB</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                Nome de Usuário
                            </label>
                            <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border 
                                          rounded-xl text-light-text dark:text-dark-text opacity-70 cursor-not-allowed">
                            <p class="text-xs text-light-text/70 dark:text-dark-text/70 mt-1">
                                Nome de usuário não pode ser alterado
                            </p>
                        </div>

                        <div>
                            <label for="cpf" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                CPF
                            </label>
                            <input type="text" id="cpf" value="<?= htmlspecialchars($user['cpf']) ?>" readonly
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border 
                                          rounded-xl text-light-text dark:text-dark-text opacity-70 cursor-not-allowed">
                            <p class="text-xs text-light-text/70 dark:text-dark-text/70 mt-1">
                                CPF não pode ser alterado
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                E-mail
                            </label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border 
                                          rounded-xl text-light-text dark:text-dark-text focus:outline-none 
                                          focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent">
                        </div>

                        <div>
                            <label for="telefone" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                Telefone
                            </label>
                            <input type="tel" id="telefone" name="telefone" value="<?= htmlspecialchars($user['telefone'] ?? '') ?>"
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border 
                                          rounded-xl text-light-text dark:text-dark-text focus:outline-none 
                                          focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent">
                        </div>
                    </div>
                    <div class="border-t border-light-border dark:border-dark-border mt-8 pt-6">
                        <h3 class="text-xl font-semibold text-light-text dark:text-dark-text mb-6">Endereço</h3>
                                            
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                            <div class="md:col-span-1">
                                <label for="cep" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                    CEP *
                                </label>
                                <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($user['cep'] ?? '') ?>" maxlength="9"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border 
                                              rounded-xl text-light-text dark:text-dark-text focus:outline-none 
                                              focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent transition-colors"
                                       placeholder="00000-000">
                                <p class="text-xs text-light-text/60 dark:text-dark-text/60 mt-1">
                                    Digite o CEP para buscar automaticamente
                                </p>
                            </div>
                                            
                            <div class="md:col-span-2">
                                <label for="endereco" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                    Endereço *
                                </label>
                                <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($user['endereco'] ?? '') ?>" readonly
                                       class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-700 border border-light-border dark:border-dark-border 
                                              rounded-xl text-light-text dark:text-dark-text opacity-80 cursor-not-allowed">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                            <div>
                                <label for="numero" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                    Número *
                                </label>
                                <input type="text" id="numero" name="numero" value="<?= htmlspecialchars($user['numero'] ?? '') ?>"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border 
                                              rounded-xl text-light-text dark:text-dark-text focus:outline-none 
                                              focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent transition-colors"
                                       placeholder="Ex: 123">
                            </div>
                                            
                            <div>
                                <label for="complemento" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                    Complemento
                                </label>
                                <input type="text" id="complemento" name="complemento" value="<?= htmlspecialchars($user['complemento'] ?? '') ?>"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-light-border dark:border-dark-border 
                                              rounded-xl text-light-text dark:text-dark-text focus:outline-none 
                                              focus:ring-2 focus:ring-light-accent dark:focus:ring-dark-accent transition-colors"
                                       placeholder="Ex: Apt 101, Bloco A">
                            </div>
                                            
                            <div>
                                <label for="bairro" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                    Bairro *
                                </label>
                                <input type="text" id="bairro" name="bairro" value="<?= htmlspecialchars($user['bairro'] ?? '') ?>" readonly
                                       class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-700 border border-light-border dark:border-dark-border 
                                              rounded-xl text-light-text dark:text-dark-text opacity-80 cursor-not-allowed">
                            </div>
                        </div>                       
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="cidade" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                    Cidade *
                                </label>
                                <input type="text" id="cidade" name="cidade" value="<?= htmlspecialchars($user['cidade'] ?? '') ?>" readonly
                                       class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-700 border border-light-border dark:border-dark-border 
                                              rounded-xl text-light-text dark:text-dark-text opacity-80 cursor-not-allowed">
                            </div>
                                            
                            <div>
                                <label for="estado" class="block text-sm font-medium text-light-text dark:text-dark-text mb-2">
                                    Estado *
                                </label>
                                <input type="text" id="estado" name="estado" value="<?= htmlspecialchars($user['estado'] ?? '') ?>" readonly
                                       class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-700 border border-light-border dark:border-dark-border 
                                              rounded-xl text-light-text dark:text-dark-text opacity-80 cursor-not-allowed">
                            </div>
                        </div>                                    
                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-xs text-blue-700 dark:text-blue-300 flex items-center">
                                <span class="mr-2"></span>
                                Campos marcados com * são obrigatórios. Endereço, Bairro, Cidade e Estado são preenchidos automaticamente pelo CEP.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 pt-6">
                    <button type="submit" class="bg-light-accent dark:bg-dark-accent text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition block mx-auto">
                      Salvar Alterações
                    </button>
                </div>
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

        let buscandoCEP = false;

        document.querySelector('form').addEventListener('submit', function(e) {
            if (buscandoCEP) {
                e.preventDefault();
                alert('Aguarde a busca do CEP terminar antes de salvar.');
                return;
            }

            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            if (cep && cep.length !== 8) {
                e.preventDefault();
                alert('Por favor, digite um CEP válido com 8 dígitos.');
                document.getElementById('cep').focus();
            }
        });

        document.getElementById('cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');

            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;

            const cepDigits = value.replace(/\D/g, '');
            if (cepDigits.length === 8 && !buscandoCEP) {
                setTimeout(() => {
                    buscarEnderecoPorCEP();
                }, 500);
            }
        });

        document.getElementById('cep').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8 && !buscandoCEP) {
                    buscarEnderecoPorCEP();
                }
            }
        });

        document.getElementById('cep').addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8 && !buscandoCEP) {
                buscarEnderecoPorCEP();
            }
        });

        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
            }
            if (value.length > 10) {
                value = value.substring(0, 10) + '-' + value.substring(10, 14);
            }
            e.target.value = value;
        });

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('foto_perfil');
        const profileImage = document.getElementById('profileImage');
        const placeholder = document.getElementById('placeholder');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.classList.add('border-light-accent', 'dark:border-dark-accent', 'bg-light-accent/10', 'dark:bg-dark-accent/10', 'dragging');
            dropZone.style.borderStyle = 'solid';
        }

        function unhighlight() {
            dropZone.classList.remove('border-light-accent', 'dark:border-dark-accent', 'bg-light-accent/10', 'dark:bg-dark-accent/10', 'dragging');
            dropZone.style.borderStyle = 'dashed';
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                handleFiles(files[0]);
            }
        }

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleFiles(this.files[0]);
            }
        });

        function handleFiles(file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Por favor, selecione uma imagem válida (JPEG, PNG, GIF ou WebP).');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('A imagem deve ter no máximo 2MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                if (profileImage) {
                    profileImage.src = e.target.result;
                    profileImage.classList.remove('hidden');

                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }

                    const existingOverlay = dropZone.querySelector('.drop-overlay');
                    if (existingOverlay) {
                        existingOverlay.remove();
                    }

                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100';
                    overlay.innerHTML = '<span class="text-white text-sm font-semibold">Alterar</span>';
                    dropZone.appendChild(overlay);
                }
            };
            reader.readAsDataURL(file);
        }

        async function buscarEnderecoPorCEP() {
            const cepInput = document.getElementById('cep');
            const cep = cepInput.value.replace(/\D/g, '');

            if (cep.length !== 8) {
                alert('Por favor, digite um CEP válido com 8 dígitos.');
                cepInput.focus();
                return;
            }

            if (buscandoCEP) return;

            buscandoCEP = true;
            mostrarLoadingEndereco(true);

            try {
                const response = await fetch(`https://brasilapi.com.br/api/cep/v2/${cep}`);

                if (!response.ok) {
                    throw new Error('CEP não encontrado');
                }

                const data = await response.json();

                if (data && !data.errors) {
                    document.getElementById('endereco').value = data.street || '';
                    document.getElementById('bairro').value = data.neighborhood || '';
                    document.getElementById('cidade').value = data.city || '';
                    document.getElementById('estado').value = data.state || '';
                    document.getElementById('numero').focus();

                    cepInput.classList.remove('border-red-500');
                    cepInput.classList.add('border-green-500');

                    setTimeout(() => {
                        cepInput.classList.remove('border-green-500');
                    }, 2000);

                } else {
                    throw new Error('CEP não encontrado');
                }

            } catch (error) {
                console.error('Erro ao buscar CEP:', error);
                alert('CEP não encontrado. Verifique o número digitado.');
                cepInput.classList.add('border-red-500');
                limparCamposEndereco();
            } finally {
                buscandoCEP = false;
                mostrarLoadingEndereco(false);
            }
        }

        function mostrarLoadingEndereco(mostrar) {
            const campos = ['endereco', 'bairro', 'cidade', 'estado'];

            if (mostrar) {
                const cepInput = document.getElementById('cep');
                cepInput.placeholder = 'Buscando...';
                cepInput.classList.add('opacity-50');
                cepInput.readOnly = true;

                campos.forEach(campo => {
                    const element = document.getElementById(campo);
                    if (element) {
                        element.value = 'Buscando...';
                        element.classList.add('opacity-50');
                    }
                });
            } else {
                const cepInput = document.getElementById('cep');
                cepInput.placeholder = '00000-000';
                cepInput.classList.remove('opacity-50');
                cepInput.readOnly = false;

                campos.forEach(campo => {
                    const element = document.getElementById(campo);
                    if (element) {
                        element.classList.remove('opacity-50');
                        if (element.value === 'Buscando...') {
                            element.value = '';
                        }
                    }
                });
            }
        }

        function limparCamposEndereco() {
            document.getElementById('endereco').value = '';
            document.getElementById('bairro').value = '';
            document.getElementById('cidade').value = '';
            document.getElementById('estado').value = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.bg-green-100');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.transition = 'all 0.5s ease';
                    successMessage.style.opacity = '0';
                    successMessage.style.transform = 'translateY(-10px)';
                    successMessage.style.marginBottom = '0';

                    setTimeout(() => {
                        if (successMessage.parentNode) {
                            successMessage.remove();
                        }
                    }, 500);
                }, 3000);
            }

            const errorMessage = document.querySelector('.bg-red-100');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.transition = 'all 0.5s ease';
                    errorMessage.style.opacity = '0';
                    errorMessage.style.transform = 'translateY(-10px)';
                    errorMessage.style.marginBottom = '0';

                    setTimeout(() => {
                        if (errorMessage.parentNode) {
                            errorMessage.remove();
                        }
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>