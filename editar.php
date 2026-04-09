<?php

require_once 'config.php';
session_start();

$sucesso = '';
$erro    = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: lista.php');
    exit;
}

try {
    $db   = getDB();
    $stmt = $db->prepare('SELECT id, nome, email, mensagem FROM contatos WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $contato = $stmt->fetch();
} catch (PDOException $e) {
    $contato = null;
}

if (!$contato) {
    header('Location: lista.php');
    exit;
}

$campos = [
    'nome'     => limpar($contato['nome']),
    'email'    => limpar($contato['email']),
    'mensagem' => limpar($contato['mensagem'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        $erro = 'Token de segurança inválido. Recarregue a página.';
    } else {
        $nome     = trim($_POST['nome']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $senha    = $_POST['senha']         ?? '';
        $mensagem = trim($_POST['mensagem'] ?? '');

        $campos = [
            'nome'     => limpar($nome),
            'email'    => limpar($email),
            'mensagem' => limpar($mensagem),
        ];

        $erros = [];

        if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
            $erros[] = 'Nome deve ter entre 3 e 100 caracteres.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'E-mail inválido.';
        }
        if (!empty($senha)) {
            if (mb_strlen($senha) < 8) {
                $erros[] = 'Senha deve ter no mínimo 8 caracteres.';
            }
            if (!preg_match('/[A-Z]/', $senha)) {
                $erros[] = 'Senha deve conter pelo menos uma letra maiúscula.';
            }
            if (!preg_match('/[0-9]/', $senha)) {
                $erros[] = 'Senha deve conter pelo menos um número.';
            }
        }
        if (mb_strlen($mensagem) > 250) {
            $erros[] = 'Mensagem deve ter no máximo 250 caracteres.';
        }

        if (empty($erros)) {
            try {
                $check = $db->prepare('SELECT id FROM contatos WHERE email = ? AND id != ? LIMIT 1');
                $check->execute([$email, $id]);
                if ($check->fetch()) {
                    $erro = 'Este e-mail já pertence a outro contato.';
                } else {
                    if (!empty($senha)) {
                        $hash   = password_hash($senha, PASSWORD_BCRYPT);
                        $update = $db->prepare(
                            'UPDATE contatos SET nome=?, email=?, senha=?, mensagem=? WHERE id=?'
                        );
                        $update->execute([$nome, $email, $hash, $mensagem, $id]);
                    } else {
                        $update = $db->prepare(
                            'UPDATE contatos SET nome=?, email=?, mensagem=? WHERE id=?'
                        );
                        $update->execute([$nome, $email, $mensagem, $id]);
                    }

                    $sucesso = 'Contato atualizado com sucesso!';
                    unset($_SESSION['csrf_token']);
                }
            } catch (PDOException $e) {
                $erro = 'Erro ao atualizar os dados. Tente novamente.';
            }
        } else {
            $erro = implode('<br>', $erros);
        }
    }
}

$csrfToken = gerarCSRF();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contato</title>
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
                <a href="lista.php" class="back-btn">&#8249;</a>
                <span class="app-icon">&#9998;</span>
                <h1>Editar</h1>
            </div>
            <nav class="header-nav">
                <a href="index.php" class="nav-btn">Cadastrar</a>
                <a href="lista.php" class="nav-btn">Lista</a>
            </nav>
        </header>

        <main class="content">
            <div class="card">
                <div class="card-header">
                    <div class="contact-avatar large"><?= mb_strtoupper(mb_substr($campos['nome'], 0, 1)) ?></div>
                    <h2><?= $campos['nome'] ?></h2>
                </div>

                <?php if ($sucesso): ?>
                    <div class="alert alert-success">
                        <span class="alert-icon">&#10003;</span>
                        <?= $sucesso ?>
                    </div>
                <?php endif; ?>
                <?php if ($erro): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">&#33;</span>
                        <?= $erro ?>
                    </div>
                <?php endif; ?>

                <form id="formCadastro" method="POST" action="editar.php?id=<?= $id ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="field-group" id="group-nome">
                        <label for="nome">Nome completo</label>
                        <input type="text" id="nome" name="nome" value="<?= $campos['nome'] ?>" maxlength="100" autocomplete="name">
                        <span class="field-error" id="err-nome"></span>
                    </div>

                    <div class="field-group" id="group-email">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="<?= $campos['email'] ?>" maxlength="150" autocomplete="email">
                        <span class="field-error" id="err-email"></span>
                    </div>

                    <div class="field-group" id="group-senha">
                        <label for="senha">Nova senha <span class="optional">(deixe em branco para manter)</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="senha" name="senha" placeholder="Nova senha (opcional)" maxlength="128" autocomplete="new-password">
                            <button type="button" class="toggle-pw" aria-label="Mostrar senha" id="toggleSenha">&#128065;</button>
                        </div>
                        <span class="field-error" id="err-senha"></span>
                        <div class="strength-bar" id="strengthBar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="strength-label" id="strengthLabel"></span>
                    </div>

                    <div class="field-group" id="group-mensagem">
                        <label for="mensagem">
                            Mensagem
                            <span class="char-counter"><span id="charCount">0</span>/250</span>
                        </label>
                        <textarea id="mensagem" name="mensagem" rows="4" maxlength="250"><?= $campos['mensagem'] ?></textarea>
                        <span class="field-error" id="err-mensagem"></span>
                    </div>

                    <div class="btn-row">
                        <a href="lista.php" class="btn-secondary">Cancelar</a>
                        <button type="submit" class="btn-primary" id="btnSubmit">
                            <span id="btnText">Salvar Alterações</span>
                            <span id="btnSpinner" class="spinner hidden"></span>
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <footer class="app-footer">
            <span>Sistema de Contatos &mdash; Projeto Escolar</span>
        </footer>
    </div>

    <script src="app.js"></script>
</body>
</html>
