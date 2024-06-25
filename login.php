<?php
session_start();
require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['perfil_id'] = $user['perfil_id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Login ou senha incorretos.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $perfil_nome = $_POST['perfil'];

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $existe_email = $stmt->fetch();

    if ($existe_email) {
        $error = "Este email já está sendo utilizado.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM perfils WHERE nome = :nome");
        $stmt->execute(['nome' => $perfil_nome]);
        $perfil = $stmt->fetch();

        if ($perfil) {
            $perfil_id = $perfil['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO perfils (nome) VALUES (:nome)");
            $stmt->execute(['nome' => $perfil_nome]);
            $perfil_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil_id) VALUES (:nome, :email, :senha, :perfil_id)");
        if ($stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senha, 'perfil_id' => $perfil_id])) {
            $success = "Cadastro realizado com sucesso! Você já pode fazer login.";
        } else {
            $error = "Erro ao realizar cadastro. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login e Cadastro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 80%;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-around;
        }

        .form-container {
            width: 45%;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        select {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }
        
        select:focus {
            outline: none;
            border-color: #3498db; 
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: #e74c3c;
            margin-top: 10px;
            text-align: center;
        }

        .success-message {
            color: #27ae60;
            margin-top: 10px;
            text-align: center;
        }

        .form-container form {
            margin-bottom: 20px;
        }

        
    </style>
</head>
<body>
    <h1>Login e Cadastro</h1>
    <div class="container">
        <div class="form-container">
            <h2>Login</h2>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" name="login">Login</button>
            </form>
            <?php if ($error && isset($_POST['login'])): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>

        <div class="form-container">
            <h2>Cadastro</h2>
            <form method="POST">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <select name="perfil" required>
                    <option value="Colaborador">Colaborador</option>
                    <option value="Administrador">Administrador</option>
                </select>
                <button type="submit" name="register">Cadastrar</button>
            </form>
            <?php if ($error): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php elseif ($success): ?>
                <p class="success-message"><?php echo $success; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
