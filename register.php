<?php
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST['cpf'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (strlen($password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (cpf, username, password) VALUES (:cpf, :username, :password)");
            $stmt->execute([
                ':cpf' => $cpf,
                ':username' => $username,
                ':password' => $hashedPassword
            ]);

            header("Location: login.php");
            exit;
            
        } catch (PDOException $e) {
            $error = "Erro ao registrar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar</title>
</head>
<body>
    <h2>Registrar</h2>
    <?php if (!empty($error)): ?>
        <p style='color:red;'><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>CPF:</label>
        <input type="text" name="cpf" required maxlength="11"><br><br>

        <label>Nome:</label>
        <input type="text" name="username" required><br><br>

        <label>Senha:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Registrar</button>
    </form>
    
    <p><a href="login.php">Já tem conta? Faça login</a></p>
</body>
</html>