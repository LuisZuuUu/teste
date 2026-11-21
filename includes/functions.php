<?php
// Inicia a sessão para guardar os dados do usuário.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Evita que textos cadastrados sejam interpretados como código HTML.
function e($texto) {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Verifica se existe um usuário conectado.
function logged() {
    return isset($_SESSION['user_id']);
}

// Impede o acesso às páginas internas sem login.
function require_login() {
    if (!logged()) {
        header('Location: login.php');
        exit;
    }
}

// Guarda uma mensagem para mostrar na próxima página.
function flash($mensagem, $tipo = 'success') {
    $_SESSION['flash'] = $mensagem;
    $_SESSION['flash_type'] = $tipo;
}

// Mostra a mensagem guardada e depois a apaga.
function show_flash() {
    if (isset($_SESSION['flash'])) {
        $mensagem = e($_SESSION['flash']);
        $tipo = e($_SESSION['flash_type']);

        echo "<div class='alert $tipo'>$mensagem</div>";

        unset($_SESSION['flash']);
        unset($_SESSION['flash_type']);
    }
}

// Cria um código de segurança para os formulários.
function csrf_field() {
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    echo '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

// Confere se o formulário foi enviado com o código correto.
function check_csrf() {
    $codigo = $_POST['csrf'] ?? '';
    $codigo_salvo = $_SESSION['csrf'] ?? '';

    if (!hash_equals($codigo_salvo, $codigo)) {
        http_response_code(403);
        exit('Formulário inválido. Atualize a página e tente novamente.');
    }
}

// Cabeçalho usado nas páginas internas.
function page_start($titulo, $pagina_ativa = '') {
    $nome = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | VoltAuto</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/app.js" defer></script>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="index.php">
            <img src="assets/logo-autoeletrica.svg" alt="Logo Autoelétrica">
            <span>VOLT<span class="accent">AUTO</span><small>GESTÃO DE AUTOELÉTRICA</small></span>
        </a>

        <?php if (logged()) { ?>
            <button class="menu-toggle" aria-label="Abrir menu">☰</button>

            <nav class="nav">
                <a class="<?= $pagina_ativa === 'home' ? 'active' : '' ?>" href="index.php">Visão geral</a>
                <a class="<?= $pagina_ativa === 'clientes' ? 'active' : '' ?>" href="clientes.php">Clientes</a>
                <a class="<?= $pagina_ativa === 'veiculos' ? 'active' : '' ?>" href="veiculos.php">Veículos</a>
                <a class="<?= $pagina_ativa === 'servicos' ? 'active' : '' ?>" href="servicos.php">Serviços</a>
            </nav>

            <div class="user-area">
                <span>Olá, <?= e($nome) ?></span>
                <a class="logout" href="logout.php">Sair ↗</a>
            </div>
        <?php } ?>
    </header>

    <main class="container">
        <?php show_flash(); ?>
<?php
}

// Rodapé usado nas páginas internas.
function page_end() {
?>
    </main>

    <footer class="footer">
        <span>VOLT<span class="accent">AUTO</span> · Gestão de autoelétrica</span>
        <span>Projeto acadêmico · PHP + MySQL</span>
    </footer>
</body>
</html>
<?php
}
