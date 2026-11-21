<?php
require 'includes/functions.php';
require 'config.php';

// Não precisa fazer login novamente se já estiver conectado.
if (logged()) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['password'] ?? '';

    // Procura o usuário pelo e-mail informado.
    $consulta = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $consulta->execute([$email]);
    $usuario = $consulta->fetch();

    // Confere a senha digitada com a senha protegida no banco.
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_name'] = $usuario['nome'];
        $_SESSION['csrf'] = bin2hex(random_bytes(32));

        header('Location: index.php');
        exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | VoltAuto</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
    <div class="auth-shell">
        <section class="auth-aside">
            <img src="assets/logo-autoeletrica.svg" alt="Logo Autoelétrica">
            <span class="eyebrow">CENTRAL DE OPERAÇÕES</span>
            <h1>Seu negócio,<br>em movimento<span class="accent">.</span></h1>
            <p>Gestão simples e organizada para quem mantém tudo funcionando.</p>
            <div class="auth-lines"></div>
        </section>

        <section class="auth-main">
            <div class="auth-form">
                <a class="brand auth-brand" href="login.php">
                    <span>VOLT<span class="accent">AUTO</span><small>GESTÃO DE AUTOELÉTRICA</small></span>
                </a>

                <span class="eyebrow">BEM-VINDO DE VOLTA</span>
                <h2>Acesse sua conta</h2>
                <p class="muted">Entre com seus dados para continuar.</p>

                <?php if ($erro !== '') { ?>
                    <div class="alert error"><?= e($erro) ?></div>
                <?php } ?>

                <form method="post" class="form-stack">
                    <?php csrf_field(); ?>

                    <label>E-mail
                        <input type="email" name="email" required maxlength="150"
                            autocomplete="username" placeholder="voce@exemplo.com">
                    </label>

                    <label>Senha
                        <input type="password" name="password" required
                            autocomplete="current-password" placeholder="Sua senha">
                    </label>

                    <button class="btn primary full" type="submit">Entrar na plataforma</button>
                </form>

                <p class="auth-foot">Ainda não tem acesso? <a href="cadastro.php">Criar uma conta</a></p>
            </div>
        </section>
    </div>
</body>
</html>
