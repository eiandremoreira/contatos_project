<?php
require_once 'config.php';
session_start();

$sucesso = '';
$erro    = '';
$campos  = ['nome' => '', 'email' => '', 'mensagem' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!validarCSRF($_POST['csrf_token'] ?? '')) {
        $erro = 'Token de segurança inválido. Recarregue a página e tente novamente.';
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
        if (mb_strlen($senha) < 8) {
            $erros[] = 'Senha deve ter no mínimo 8 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $senha)) {
            $erros[] = 'Senha deve conter pelo menos uma letra maiúscula.';
        }
        if (!preg_match('/[0-9]/', $senha)) {
            $erros[] = 'Senha deve conter pelo menos um número.';
        }
        if (mb_strlen($mensagem) > 250) {
            $erros[] = 'Mensagem deve ter no máximo 250 caracteres.';
        }

        if (empty($erros)) {
            try {
                $db = getDB();

                $stmt = $db->prepare('SELECT id FROM contatos WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $erro = 'Este e-mail já está cadastrado.';
                } else {
                    $hash = password_hash($senha, PASSWORD_BCRYPT);

                    $insert = $db->prepare(
                        'INSERT INTO contatos (nome, email, senha, mensagem) VALUES (?, ?, ?, ?)'
                    );
                    $insert->execute([$nome, $email, $hash, $mensagem]);

                    $sucesso = 'Contato cadastrado com sucesso!';
                    $campos  = ['nome' => '', 'email' => '', 'mensagem' => ''];

                    unset($_SESSION['csrf_token']);
                }
            } catch (PDOException $e) {
                $erro = 'Erro ao salvar os dados. Tente novamente.';
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
    <title>Cadastro de Contato</title>
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
                <a href="index.php" class="nav-btn active">Cadastrar</a>
                <a href="lista.php" class="nav-btn">Lista</a>
            </nav>
        </header>

        <main class="content">
            <div class="card">
                <div class="card-header">
                    <span class="card-icon">&#43;</span>
                    <h2>Novo Contato</h2>
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

                <form id="formCadastro" method="POST" action="index.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="field-group" id="group-nome">
                        <label for="nome">Nome completo</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            value="<?= $campos['nome'] ?>"
                            placeholder="Ex: João Silva"
                            maxlength="100"
                            autocomplete="name"
                        >
                        <span class="field-error" id="err-nome"></span>
                    </div>

                    <div class="field-group" id="group-email">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= $campos['email'] ?>"
                            placeholder="exemplo@email.com"
                            maxlength="150"
                            autocomplete="email"
                        >
                        <span class="field-error" id="err-email"></span>
                    </div>

                    <div class="field-group" id="group-senha">
                        <label for="senha">Senha</label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                id="senha"
                                name="senha"
                                placeholder="Mín. 8 caracteres, 1 maiúscula, 1 número"
                                maxlength="128"
                                autocomplete="new-password"
                            >
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
                        <textarea
                            id="mensagem"
                            name="mensagem"
                            rows="4"
                            placeholder="Escreva uma mensagem (opcional)..."
                            maxlength="250"
                        ><?= $campos['mensagem'] ?></textarea>
                        <span class="field-error" id="err-mensagem"></span>
                    </div>

                    <button type="submit" class="btn-primary" id="btnSubmit">
                        <span id="btnText">Cadastrar Contato</span>
                        <span id="btnSpinner" class="spinner hidden"></span>
                    </button>
                </form>
            </div>
        </main>

        <footer class="app-footer">
            <span>Sistema de Contatos &mdash; Evelyn e André</span>
        </footer>
    </div>

    <script src="app.js"></script>
</body>
</html>
