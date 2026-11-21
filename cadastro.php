<?php
require 'includes/functions.php';
require 'config.php';

// Se já estiver conectado, vai direto para o painel.
if (logged()) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Preencha um nome e um e-mail válido.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($senha !== $confirmar) {
        $erro = 'As senhas não são iguais.';
    } else {
        // Confere se o e-mail já está cadastrado.
        $consulta = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);

        if ($consulta->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            // A senha é salva em formato protegido, não como texto puro.
            $senha_protegida = password_hash($senha, PASSWORD_DEFAULT);

            $consulta = $pdo->prepare(
                'INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)'
            );
            $consulta->execute([$nome, $email, $senha_protegida]);

            flash('Conta criada. Agora faça seu login.');
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta | VoltAuto</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
    <div class="auth-shell">
        <section class="auth-aside">
            <img src="assets/logo-autoeletrica.svg" alt="Logo Autoelétrica">
            <span class="eyebrow">GESTÃO NA PRÁTICA</span>
            <h1>Comece a organizar<br>sua oficina<span class="accent">.</span></h1>
            <p>Crie seu acesso para gerenciar os dados da autoelétrica.</p>
            <div class="auth-lines"></div>
        </section>

        <section class="auth-main">
            <div class="auth-form">
                <a class="brand auth-brand" href="login.php">
                    <span>VOLT<span class="accent">AUTO</span><small>GESTÃO DE AUTOELÉTRICA</small></span>
                </a>

                <span class="eyebrow">NOVO ACESSO</span>
                <h2>Criar uma conta</h2>
                <p class="muted">Preencha os dados abaixo.</p>

                <?php if ($erro !== '') { ?>
                    <div class="alert error"><?= e($erro) ?></div>
                <?php } ?>

                <form method="post" class="form-stack">
                    <?php csrf_field(); ?>

                    <label>Nome completo
                        <input type="text" name="nome" required maxlength="100"
                            value="<?= e($_POST['nome'] ?? '') ?>">
                    </label>

                    <label>E-mail
                        <input type="email" name="email" required maxlength="150"
                            value="<?= e($_POST['email'] ?? '') ?>">
                    </label>

                    <label>Senha
                        <input type="password" name="senha" required minlength="8">
                    </label>

                    <label>Confirmar senha
                        <input type="password" name="confirmar" required minlength="8">
                    </label>

                    <button class="btn primary full" type="submit">Criar conta</button>
                </form>

                <p class="auth-foot">Já possui acesso? <a href="login.php">Voltar ao login</a></p>
            </div>
        </section>
    </div>
</body>
</html>
