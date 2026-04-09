'use strict';


function atualizarRelogio() {
    const el = document.getElementById('clock');
    if (!el) return;
    const now  = new Date();
    const h    = now.getHours().toString().padStart(2, '0');
    const m    = now.getMinutes().toString().padStart(2, '0');
    el.textContent = `${h}:${m}`;
}
atualizarRelogio();
setInterval(atualizarRelogio, 30000);

const Validar = {
    nome(v) {
        if (!v) return 'Nome é obrigatório.';
        if (v.length < 3) return 'Nome deve ter pelo menos 3 caracteres.';
        if (v.length > 100) return 'Nome deve ter no máximo 100 caracteres.';
        return '';
    },
    email(v) {
        if (!v) return 'E-mail é obrigatório.';
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'E-mail inválido.';
        return '';
    },
    senha(v, obrigatoria = true) {
        if (!v) return obrigatoria ? 'Senha é obrigatória.' : '';
        if (v.length < 8)        return 'Mínimo de 8 caracteres.';
        if (!/[A-Z]/.test(v))    return 'Inclua pelo menos uma letra maiúscula.';
        if (!/[0-9]/.test(v))    return 'Inclua pelo menos um número.';
        return '';
    },
    mensagem(v) {
        if (v.length > 250) return 'Máximo de 250 caracteres.';
        return '';
    }
};

function setFieldState(groupId, errorMsg) {
    const group = document.getElementById(groupId);
    const errEl = document.getElementById('err-' + groupId.replace('group-', ''));
    if (!group) return;

    group.classList.remove('has-error', 'is-valid');
    if (errorMsg) {
        group.classList.add('has-error');
        if (errEl) errEl.textContent = errorMsg;
    } else {
        group.classList.add('is-valid');
        if (errEl) errEl.textContent = '';
    }
}

function avaliarForca(senha) {
    let score = 0;
    if (senha.length >= 8)  score++;
    if (senha.length >= 12) score++;
    if (/[A-Z]/.test(senha)) score++;
    if (/[0-9]/.test(senha)) score++;
    if (/[^A-Za-z0-9]/.test(senha)) score++;
    return score;
}

function atualizarBarraSenha(senha) {
    const bar   = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    if (!bar || !label) return;

    const score = avaliarForca(senha);
    const map = [
        { pct: 0,   color: 'transparent', text: '' },
        { pct: 20,  color: '#FF3B30',     text: 'Muito fraca' },
        { pct: 40,  color: '#FF9500',     text: 'Fraca' },
        { pct: 60,  color: '#FFCC00',     text: 'Razoável' },
        { pct: 80,  color: '#34C759',     text: 'Forte' },
        { pct: 100, color: '#34C759',     text: 'Muito forte' },
    ];
    const { pct, color, text } = map[Math.min(score, map.length - 1)];
    bar.style.width      = pct + '%';
    bar.style.background = color;
    label.textContent    = text;
    label.style.color    = color;
}

document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('formCadastro');
    if (!form) return;

    const isEditar = window.location.pathname.includes('editar');

    const textarea  = document.getElementById('mensagem');
    const charCount = document.getElementById('charCount');
    const charCtr   = document.querySelector('.char-counter');

    if (textarea && charCount) {
        const atualizar = () => {
            const len = textarea.value.length;
            charCount.textContent = len;
            charCtr.classList.remove('warning', 'danger');
            if (len > 220) charCtr.classList.add('danger');
            else if (len > 180) charCtr.classList.add('warning');
        };
        atualizar();
        textarea.addEventListener('input', atualizar);
    }

    const toggleBtn = document.getElementById('toggleSenha');
    const senhaInput = document.getElementById('senha');
    if (toggleBtn && senhaInput) {
        toggleBtn.addEventListener('click', () => {
            const visible = senhaInput.type === 'text';
            senhaInput.type = visible ? 'password' : 'text';
            toggleBtn.setAttribute('aria-label', visible ? 'Mostrar senha' : 'Ocultar senha');
        });
    }

    if (senhaInput) {
        senhaInput.addEventListener('input', () => {
            atualizarBarraSenha(senhaInput.value);
        });
    }

    const nomeInput  = document.getElementById('nome');
    const emailInput = document.getElementById('email');

    if (nomeInput) {
        nomeInput.addEventListener('blur', () => {
            setFieldState('group-nome', Validar.nome(nomeInput.value.trim()));
        });
    }
    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            setFieldState('group-email', Validar.email(emailInput.value.trim()));
        });
    }
    if (senhaInput) {
        senhaInput.addEventListener('blur', () => {
            setFieldState('group-senha', Validar.senha(senhaInput.value, !isEditar));
        });
    }
    if (textarea) {
        textarea.addEventListener('blur', () => {
            setFieldState('group-mensagem', Validar.mensagem(textarea.value));
        });
    }

    form.addEventListener('submit', function(e) {
        const erros = {};

        erros.nome     = Validar.nome(nomeInput  ? nomeInput.value.trim()  : '');
        erros.email    = Validar.email(emailInput ? emailInput.value.trim() : '');
        erros.senha    = Validar.senha(senhaInput ? senhaInput.value        : '', !isEditar);
        erros.mensagem = Validar.mensagem(textarea ? textarea.value         : '');

        setFieldState('group-nome',     erros.nome);
        setFieldState('group-email',    erros.email);
        setFieldState('group-senha',    erros.senha);
        setFieldState('group-mensagem', erros.mensagem);

        const temErro = Object.values(erros).some(v => v !== '');
        if (temErro) {
            e.preventDefault();
            const primeiro = form.querySelector('.has-error input, .has-error textarea');
            if (primeiro) primeiro.focus();
            return;
        }

        const btn     = document.getElementById('btnSubmit');
        const btnText = document.getElementById('btnText');
        const spinner = document.getElementById('btnSpinner');
        if (btn && btnText && spinner) {
            btn.disabled = true;
            btnText.textContent = 'Salvando...';
            spinner.classList.remove('hidden');
        }
    });
});
