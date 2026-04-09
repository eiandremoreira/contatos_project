
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
