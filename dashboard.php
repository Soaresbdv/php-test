<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        
        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .welcome-card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .welcome-card h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .welcome-card p {
            color: #666;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-size: 1.1em;
            border-left: 4px solid #28a745;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: transform 0.2s;
            display: inline-block;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-logout {
            background: #dc3545;
            color: white;
        }
        
        .btn-login {
            background: #6c757d;
            color: white;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: left;
        }
        
        .user-info h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .user-info p {
            margin: 5px 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-card">
            <h1>Parabéns!!!</h1>
            <p>Você acessou o sistema com sucesso!</p>
            
            <div class="success-message">
                login realizado com sucesso! Bem-vindo ao sistema.
            </div>
            
            <div class="user-info">
                <h3>👤 Suas Informações</h3>
                <p><strong>ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p><strong>Status:</strong> Logado</p>
            </div>
            
            <div class="actions">
                <a href="logout.php" class="btn btn-logout">Sair do Sistema</a>
            </div>
        </div>
    </div>
</body>
</html>