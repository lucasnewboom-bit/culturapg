
<?php
$mensagem = $mensagem ?? '';
?>

<div class="pagina-autenticacao">

    <div class="logo-autenticacao">
        <img
            src="img/logo-culturapg.png"
            alt="CulturaPG - Encontre. Descubra. Participe."
        >
    </div>

    <div class="cartao-autenticacao">

        <h1>Bem-vindo de volta!</h1>

        <p class="subtitulo-autenticacao">
            Entre na sua conta para continuar.
        </p>

        <?php if ($mensagem !== ''): ?>
            <div class="mensagem-autenticacao">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="index.php?pagina=login&acao=entrar"
              method="POST"
              novalidate>

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
                    placeholder="Digite sua senha"
                    required
                >
            </div>

            <button type="submit" class="botao-autenticacao">
                Entrar
            </button>

        </form>

        <div class="link-autenticacao">
            Ainda não possui uma conta?
            <a href="index.php?pagina=cadastro">Criar conta</a>
        </div>

    </div>

</div>
