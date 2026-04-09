<?php
require_once 'config.php';
session_start();

$mensagem = '';
$tipoMsg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'deletar') {
    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        $mensagem = 'Token de segurança inválido.';
        $tipoMsg  = 'error';
    } else {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $db   = getDB();
                $stmt = $db->prepare('DELETE FROM contatos WHERE id = ?');
                $stmt->execute([$id]);
                $mensagem = 'Contato removido com sucesso.';
                $tipoMsg  = 'success';
                unset($_SESSION['csrf_token']);
            } catch (PDOException $e) {
                $mensagem = 'Erro ao remover contato.';
                $tipoMsg  = 'error';
            }
        }
    }
}

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

    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <div class="modal-icon">&#128465;</div>
            <h3>Remover contato?</h3>
            <p id="modalMsg">Tem certeza que deseja remover este contato?</p>
            <div class="modal-actions">
                <button type="button" class="btn-modal btn-cancel" onclick="fecharModal()">Cancelar</button>
                <form method="POST" action="lista.php" id="formDelete">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="acao" value="deletar">
                    <input type="hidden" name="id" id="deleteId" value="">
                    <button type="submit" class="btn-modal btn-confirm">Remover</button>
                </form>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
    <script>
        function confirmarDelete(id, nome) {
            document.getElementById('deleteId').value = id;
            document.getElementById('modalMsg').textContent =
                'Tem certeza que deseja remover "' + nome + '"? Esta ação não pode ser desfeita.';
            document.getElementById('modalOverlay').classList.add('active');
        }
        function fecharModal() {
            document.getElementById('modalOverlay').classList.remove('active');
        }
        document.getElementById('modalOverlay').addEventListener('click', function(e) {
            if (e.target === this) fecharModal();
        });
    </script>
</body>
</html>
