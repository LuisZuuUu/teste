<?php
require 'includes/functions.php';
require_login();
require 'config.php';

// Conta quantos clientes, veículos e serviços existem.
$quantidades = [];
$quantidades['clientes'] = $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
$quantidades['veiculos'] = $pdo->query('SELECT COUNT(*) FROM veiculos')->fetchColumn();
$quantidades['servicos'] = $pdo->query('SELECT COUNT(*) FROM servicos')->fetchColumn();

// Busca os cinco serviços mais recentes.
$sql = "SELECT servicos.id, servicos.descricao, servicos.status,
               servicos.valor, servicos.data_servico,
               clientes.nome AS cliente, veiculos.modelo, veiculos.placa
        FROM servicos
        JOIN veiculos ON veiculos.id = servicos.veiculo_id
        JOIN clientes ON clientes.id = veiculos.cliente_id
        ORDER BY servicos.id DESC
        LIMIT 5";

$servicos = $pdo->query($sql)->fetchAll();

page_start('Visão geral', 'home');
?>

<section class="welcome">
    <div>
        <span class="eyebrow">PAINEL DE CONTROLE</span>
        <h1>Visão geral<span class="accent">.</span></h1>
        <p>Organize clientes, veículos e serviços em um só lugar.</p>
    </div>
    <a class="btn primary" href="servicos.php?novo=1">＋ Novo serviço</a>
</section>

<section class="stats">
    <article class="stat-card">
        <span class="stat-icon">♙</span>
        <div>
            <small>CLIENTES</small>
            <strong><?= $quantidades['clientes'] ?></strong>
            <a href="clientes.php">Gerenciar clientes →</a>
        </div>
    </article>

    <article class="stat-card">
        <span class="stat-icon">▰</span>
        <div>
            <small>VEÍCULOS</small>
            <strong><?= $quantidades['veiculos'] ?></strong>
            <a href="veiculos.php">Gerenciar veículos →</a>
        </div>
    </article>

    <article class="stat-card">
        <span class="stat-icon">⌁</span>
        <div>
            <small>SERVIÇOS</small>
            <strong><?= $quantidades['servicos'] ?></strong>
            <a href="servicos.php">Gerenciar serviços →</a>
        </div>
    </article>
</section>

<section class="panel">
    <div class="panel-heading">
        <div>
            <span class="eyebrow">ACOMPANHAMENTO</span>
            <h2>Serviços recentes</h2>
        </div>
        <a class="text-link" href="servicos.php">Ver todos →</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Cliente</th>
                    <th>Veículo</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicos as $servico) { ?>
                    <tr>
                        <td><?= e($servico['descricao']) ?></td>
                        <td><?= e($servico['cliente']) ?></td>
                        <td>
                            <?= e($servico['modelo']) ?>
                            <small><?= e($servico['placa']) ?></small>
                        </td>
                        <td><?= date('d/m/Y', strtotime($servico['data_servico'])) ?></td>
                        <td><span class="status <?= e(strtolower(str_replace(" ", "-", $servico['status']))) ?>"><?= e($servico['status']) ?></span></td>
                        <td>R$ <?= number_format($servico['valor'], 2, ',', '.') ?></td>
                    </tr>
                <?php } ?>

                <?php if (count($servicos) === 0) { ?>
                    <tr>
                        <td colspan="6" class="empty">Nenhum serviço cadastrado ainda.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>

<?php page_end(); ?>
