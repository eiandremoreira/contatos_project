<?php
require_once 'config.php';
session_start();

$mensagem = '';
$tipoMsg  = '';



try {
    $db       = getDB();
    $stmt     = $db->query('SELECT id, nome, email, mensagem, criado_em FROM contatos ORDER BY criado_em DESC');
    $contatos = $stmt->fetchAll();
} catch (PDOException $e) {
    $contatos = [];
    $mensagem = 'Erro ao carregar contatos.';
    $tipoMsg  = 'error';
}

$csrfToken = gerarCSRF();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Contatos</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="ios-statusbar">
        <span class="time" id="clock">9:41</span>
        <span class="icons">&#9679;&#9679;&#9679;</span>
    </div>

    <div class="app-container">
        <header class="app-header">
            <div class="header-inner">
                <span class="app-icon">&#128101;</span>
                <h1>Contatos</h1>
            </div>
            <nav class="header-nav">
                <a href="index.php" class="nav-btn">Cadastrar</a>
                <a href="lista.php" class="nav-btn active">Lista</a>
            </nav>
        </header>

        <main class="content">

            <?php if ($mensagem): ?>
                <div class="alert alert-<?= $tipoMsg ?>">
                    <span class="alert-icon"><?= $tipoMsg === 'success' ? '&#10003;' : '&#33;' ?></span>
                    <?= limpar($mensagem) ?>
                </div>
            <?php endif; ?>

            <div class="list-header">
                <h2>Todos os Contatos</h2>
                <span class="badge"><?= count($contatos) ?></span>
            </div>

            <?php if (empty($contatos)): ?>
                <div class="empty-state">
                    <span class="empty-icon">&#128100;</span>
                    <p>Nenhum contato cadastrado ainda.</p>
                    <a href="index.php" class="btn-primary" style="display:inline-block;margin-top:12px;">Cadastrar agora</a>
                </div>
            <?php else: ?>
                <div class="contact-list">
                    <?php foreach ($contatos as $c): ?>
                        <div class="contact-card" id="card-<?= $c['id'] ?>">
                            <div class="contact-avatar">
                                <?= mb_strtoupper(mb_substr(limpar($c['nome']), 0, 1)) ?>
                            </div>
                            <div class="contact-info">
                                <strong><?= limpar($c['nome']) ?></strong>
                                <span class="contact-email"><?= limpar($c['email']) ?></span>
                                <?php if (!empty($c['mensagem'])): ?>
                                    <p class="contact-msg"><?= limpar($c['mensagem']) ?></p>
                                <?php endif; ?>
                                <small class="contact-date">
                                    Cadastrado em <?= date('d/m/Y \à\s H:i', strtotime($c['criado_em'])) ?>
                                </small>
                            </div>
                            <div class="contact-actions">
                                <a href="editar.php?id=<?= $c['id'] ?>" class="btn-action btn-edit" title="Editar">
                                    &#9998;
                                </a>
                                <button
                                    type="button"
                                    class="btn-action btn-delete"
                                    title="Deletar"
                                    onclick="confirmarDelete(<?= $c['id'] ?>, '<?= limpar($c['nome']) ?>')"
                                >
                                    &#128465;
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <footer class="app-footer">
            <span>Sistema de Contatos &mdash; Projeto Escolar</span>
        </footer>
    </div>


</body>
</html>
