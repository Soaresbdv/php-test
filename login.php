<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST['cpf'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE cpf = :cpf");
        $stmt->execute([':cpf' => $cpf]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            echo "<p style='color:green;'>Login realizado com sucesso!</p>";
        } else {
            echo "<p style='color:red;'>CPF ou senha incorretos!</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Tela de Login</h2>
    <form method="post">
        <label>CPF:</label>
        <input type="text" name="cpf" required maxlength="11"><br><br>

        <label>Senha:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Entrar</button>
    </form>
    
    <p><a href="register.php">Não tem conta? Registre-se</a></p>
</body>
</html>