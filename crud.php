<?php
require 'includes/functions.php';
require_login();
require 'config.php';

// Cada página informa qual cadastro será exibido.
// Os campos abaixo são usados para montar os formulários e as tabelas.
$configuracoes = [
    'clientes' => [
        'titulo' => 'Clientes',
        'singular' => 'cliente',
        'tabela' => 'clientes',
        'campos' => [
            ['nome', 'Nome completo', 'text', true],
            ['telefone', 'Telefone', 'tel', true],
            ['email', 'E-mail', 'email', false],
            ['observacoes', 'Observações', 'textarea', false]
        ],
        'colunas' => ['Nome' => 'nome', 'Telefone' => 'telefone', 'E-mail' => 'email']
    ],
    'veiculos' => [
        'titulo' => 'Veículos',
        'singular' => 'veículo',
        'tabela' => 'veiculos',
        'campos' => [
            ['cliente_id', 'Cliente', 'cliente', true],
            ['marca', 'Marca', 'text', true],
            ['modelo', 'Modelo', 'text', true],
            ['ano', 'Ano', 'number', false],
            ['placa', 'Placa', 'placa', true]
        ],
        'colunas' => ['Cliente' => 'cliente', 'Veículo' => 'modelo', 'Ano' => 'ano', 'Placa' => 'placa']
    ],
    'servicos' => [
        'titulo' => 'Serviços',
        'singular' => 'serviço',
        'tabela' => 'servicos',
        'campos' => [
            ['veiculo_id', 'Veículo', 'veiculo', true],
            ['descricao', 'Descrição do serviço', 'text', true],
            ['data_servico', 'Data', 'date', true],
            ['valor', 'Valor (R$)', 'number', true],
            ['status', 'Status', 'status', true],
            ['detalhes', 'Detalhes', 'textarea', false]
        ],
        'colunas' => [
            'Serviço' => 'descricao',
            'Veículo' => 'veiculo',
            'Cliente' => 'cliente',
            'Data' => 'data_servico',
            'Status' => 'status',
            'Valor' => 'valor'
        ]
    ]
];

$config = $configuracoes[$entity];
$tabela = $config['tabela'];
$id = (int) ($_GET['editar'] ?? 0);
$editando = $id > 0;
$erro = '';
$valores = [];

// Busca os clientes e veículos para usar nas caixas de seleção.
$clientes = $pdo->query('SELECT id, nome FROM clientes ORDER BY nome')->fetchAll();
$veiculos = $pdo->query(
    'SELECT veiculos.id, veiculos.modelo, veiculos.placa, clientes.nome AS cliente
     FROM veiculos
     JOIN clientes ON clientes.id = veiculos.cliente_id
     ORDER BY veiculos.modelo'
)->fetchAll();

// Trata os formulários enviados.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $acao = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    // Exclui um registro.
    if ($acao === 'delete') {
        try {
            $consulta = $pdo->prepare("DELETE FROM $tabela WHERE id = ?");
            $consulta->execute([$id]);
            flash('Registro excluído com sucesso.');
        } catch (PDOException $erro_banco) {
            flash('Não foi possível excluir. Este registro pode estar vinculado a outro.', 'error');
        }

        header("Location: $entity.php");
        exit;
    }

    // Salva um registro novo ou atualiza um que já existe.
    if ($acao === 'save') {
        $valores = [];

        foreach ($config['campos'] as $campo) {
            $nome_campo = $campo[0];
            $obrigatorio = $campo[3];
            $valores[$nome_campo] = trim($_POST[$nome_campo] ?? '');

            if ($obrigatorio && $valores[$nome_campo] === '') {
                $erro = 'Preencha todos os campos obrigatórios.';
            }
        }

        // Validações simples de alguns campos.
        if ($erro === '' && isset($valores['email']) && $valores['email'] !== '') {
            if (!filter_var($valores['email'], FILTER_VALIDATE_EMAIL)) {
                $erro = 'Digite um e-mail válido.';
            }
        }

        if ($erro === '' && isset($valores['placa'])) {
            if (!preg_match('/^[A-Za-z]{3}[0-9][A-Za-z0-9][0-9]{2}$/', $valores['placa'])) {
                $erro = 'Digite uma placa válida.';
            }
        }

        if ($erro === '' && isset($valores['ano']) && $valores['ano'] !== '') {
            if ($valores['ano'] < 1950 || $valores['ano'] > date('Y') + 1) {
                $erro = 'Digite um ano válido para o veículo.';
            }
        }

        if ($erro === '' && isset($valores['valor'])) {
            if (!is_numeric($valores['valor']) || $valores['valor'] < 0) {
                $erro = 'Digite um valor válido.';
            }
        }

        if ($erro === '') {
            try {
                $campos = array_keys($valores);
                $dados = array_values($valores);

                if ($id > 0) {
                    // Monta os campos que serão atualizados.
                    $partes = [];
                    foreach ($campos as $campo) {
                        $partes[] = "$campo = ?";
                    }

                    $sql = "UPDATE $tabela SET " . implode(', ', $partes) . ' WHERE id = ?';
                    $dados[] = $id;
                    $consulta = $pdo->prepare($sql);
                    $consulta->execute($dados);
                    flash('Registro atualizado com sucesso.');
                } else {
                    // Monta o comando para inserir um novo registro.
                    $marcacoes = array_fill(0, count($campos), '?');
                    $sql = "INSERT INTO $tabela (" . implode(', ', $campos) . ') VALUES (' . implode(', ', $marcacoes) . ')';
                    $consulta = $pdo->prepare($sql);
                    $consulta->execute($dados);
                    flash('Registro cadastrado com sucesso.');
                }

                header("Location: $entity.php");
                exit;
            } catch (PDOException $erro_banco) {
                $erro = 'Não foi possível salvar. Verifique os dados informados.';
            }
        }

        $editando = $id > 0;
    }
}

// Quando a página está no modo de edição, busca os dados do registro.
if ($editando && empty($valores)) {
    $consulta = $pdo->prepare("SELECT * FROM $tabela WHERE id = ?");
    $consulta->execute([$id]);
    $valores = $consulta->fetch();

    if (!$valores) {
        flash('Registro não encontrado.', 'error');
        header("Location: $entity.php");
        exit;
    }
}

// Define a consulta que vai preencher a tabela da página.
if ($entity === 'clientes') {
    $sql = 'SELECT * FROM clientes ORDER BY id DESC';
} elseif ($entity === 'veiculos') {
    $sql = 'SELECT veiculos.*, clientes.nome AS cliente
            FROM veiculos
            JOIN clientes ON clientes.id = veiculos.cliente_id
            ORDER BY veiculos.id DESC';
} else {
    $sql = "SELECT servicos.*, veiculos.modelo, veiculos.placa,
                   clientes.nome AS cliente,
                   CONCAT(veiculos.modelo, ' · ', veiculos.placa) AS veiculo
            FROM servicos
            JOIN veiculos ON veiculos.id = servicos.veiculo_id
            JOIN clientes ON clientes.id = veiculos.cliente_id
            ORDER BY servicos.id DESC";
}

$registros = $pdo->query($sql)->fetchAll();

page_start($config['titulo'], $entity);
?>

<section class="welcome">
    <div>
        <span class="eyebrow">CADASTROS</span>
        <h1><?= e($config['titulo']) ?><span class="accent">.</span></h1>
        <p>Consulte, cadastre e mantenha as informações atualizadas.</p>
    </div>
    <a class="btn primary" href="<?= $entity ?>.php?novo=1">＋ Novo <?= e($config['singular']) ?></a>
</section>

<?php if ($erro !== '') { ?>
    <div class="alert error"><?= e($erro) ?></div>
<?php } ?>

<?php if ($editando || isset($_GET['novo'])) { ?>
    <section class="panel form-panel">
        <div class="panel-heading">
            <div>
                <span class="eyebrow">DADOS DO REGISTRO</span>
                <h2><?= $editando ? 'Editar' : 'Cadastrar' ?> <?= e($config['singular']) ?></h2>
            </div>
            <a class="text-link" href="<?= $entity ?>.php">Cancelar</a>
        </div>

        <form method="post" class="data-form">
            <?php csrf_field(); ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editando ? $id : 0 ?>">

            <?php foreach ($config['campos'] as $campo) {
                $nome_campo = $campo[0];
                $rotulo = $campo[1];
                $tipo = $campo[2];
                $obrigatorio = $campo[3];
                $valor = $valores[$nome_campo] ?? $_POST[$nome_campo] ?? '';
            ?>
                <label class="field">
                    <?= e($rotulo) ?>

                    <?php if ($tipo === 'textarea') { ?>
                        <textarea name="<?= $nome_campo ?>" rows="3" <?= $obrigatorio ? 'required' : '' ?>><?= e($valor) ?></textarea>

                    <?php } elseif ($tipo === 'cliente' || $tipo === 'veiculo') { ?>
                        <select name="<?= $nome_campo ?>" <?= $obrigatorio ? 'required' : '' ?>>
                            <option value="">Selecione</option>

                            <?php $opcoes = $tipo === 'cliente' ? $clientes : $veiculos; ?>
                            <?php foreach ($opcoes as $opcao) { ?>
                                <option value="<?= $opcao['id'] ?>" <?= $valor == $opcao['id'] ? 'selected' : '' ?>>
                                    <?php if ($tipo === 'cliente') { ?>
                                        <?= e($opcao['nome']) ?>
                                    <?php } else { ?>
                                        <?= e($opcao['modelo'] . ' - ' . $opcao['placa'] . ' / ' . $opcao['cliente']) ?>
                                    <?php } ?>
                                </option>
                            <?php } ?>
                        </select>

                    <?php } elseif ($tipo === 'status') { ?>
                        <select name="status" required>
                            <?php foreach (['Pendente', 'Em andamento', 'Concluído', 'Cancelado'] as $status) { ?>
                                <option value="<?= e($status) ?>" <?= $valor === $status ? 'selected' : '' ?>>
                                    <?= e($status) ?>
                                </option>
                            <?php } ?>
                        </select>

                    <?php } else { ?>
                        <input
                            type="<?= $tipo === 'placa' ? 'text' : $tipo ?>"
                            name="<?= $nome_campo ?>"
                            value="<?= e($valor) ?>"
                            <?= $obrigatorio ? 'required' : '' ?>
                            <?= $tipo === 'number' ? 'min="0" step="1"' : '' ?>
                            <?= $tipo === 'placa' ? 'maxlength="7" placeholder="ABC1D23"' : '' ?>
                        >
                    <?php } ?>
                </label>
            <?php } ?>

            <div class="form-actions">
                <button class="btn primary" type="submit">
                    <?= $editando ? 'Salvar alterações' : 'Cadastrar' ?>
                </button>
                <a class="btn ghost" href="<?= $entity ?>.php">Cancelar</a>
            </div>
        </form>
    </section>
<?php } ?>

<section class="panel">
    <div class="panel-heading">
        <div>
            <span class="eyebrow">BASE DE DADOS</span>
            <h2>Registros cadastrados <span class="count"><?= count($registros) ?></span></h2>
        </div>
        <input class="search" type="search" placeholder="Buscar registro..." data-table-search>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <?php foreach ($config['colunas'] as $titulo_coluna => $campo) { ?>
                        <th><?= e($titulo_coluna) ?></th>
                    <?php } ?>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $registro) { ?>
                    <tr>
                        <?php foreach ($config['colunas'] as $campo) { ?>
                            <td>
                                <?php if ($campo === 'valor') { ?>
                                    R$ <?= number_format($registro[$campo], 2, ',', '.') ?>
                                <?php } elseif ($campo === 'data_servico') { ?>
                                    <?= date('d/m/Y', strtotime($registro[$campo])) ?>
                                <?php } elseif ($campo === 'status') { ?>
                                    <span class="status <?= e(strtolower(str_replace(" ", "-", $registro[$campo]))) ?>"><?= e($registro[$campo]) ?></span>
                                <?php } else { ?>
                                    <?= e($registro[$campo] ?? '-') ?>
                                <?php } ?>
                            </td>
                        <?php } ?>

                        <td class="row-actions">
                            <a class="icon-btn" href="<?= $entity ?>.php?editar=<?= $registro['id'] ?>" title="Editar">✎</a>

                            <form method="post" data-confirm="Deseja excluir este registro?">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $registro['id'] ?>">
                                <button class="icon-btn danger-text" type="submit" title="Excluir">⌫</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>

                <?php if (count($registros) === 0) { ?>
                    <tr>
                        <td colspan="<?= count($config['colunas']) + 1 ?>" class="empty">
                            Nenhum registro cadastrado.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>

<?php page_end(); ?>
