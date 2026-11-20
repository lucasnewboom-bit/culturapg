
<?php
$mensagem = $mensagem ?? '';
?>

<div class="pagina-autenticacao">

    <!-- LOGO -->
    <div class="logo-autenticacao">
        <img
            src="img/logo-culturapg.png"
            alt="CulturaPG - Encontre. Descubra. Participe."
        >
    </div>

    <div class="cartao-autenticacao">

        <h1>Crie sua conta</h1>

        <p class="subtitulo-autenticacao">
            Descubra tudo o que acontece na cultura da cidade.
        </p>

        <?php if ($mensagem !== ''): ?>
            <div class="mensagem-autenticacao">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form
            action="index.php?pagina=cadastro&acao=registrar"
            method="POST"
            novalidate
        >

            <div class="campo-autenticacao">
                <label for="nome">Nome completo</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="100"
                    placeholder="Digite seu nome"
                >
            </div>

            <div class="campo-autenticacao">
                <label for="username">Nome de usuário</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    maxlength="50"
                    placeholder="Escolha seu username"
                >
            </div>

            <div class="campo-autenticacao">
                <label for="email">E-mail</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    maxlength="150"
                    placeholder="seu@email.com"
                >
            </div>

            <div class="campo-autenticacao">
                <label for="senha">Senha</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Crie uma senha"
                >
            </div>

            <div class="campo-autenticacao">
                <label for="confirmar_senha">Confirmar senha</label>
                <input
                    type="password"
                    id="confirmar_senha"
                    name="confirmar_senha"
                    placeholder="Digite a senha novamente"
                >
            </div>

            <button type="submit" class="botao-autenticacao">
                Criar minha conta
            </button>

        </form>

        <div class="link-autenticacao">
            Já possui uma conta?
            <a href="index.php?pagina=login">Entrar</a>
        </div>

    </div>

</div>
