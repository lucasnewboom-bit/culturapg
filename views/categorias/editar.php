<?php
$escaparCategoria = static fn($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>

<section class="secao-categorias">
    <h1>Editar categoria</h1>
    <p>Altere o nome da categoria. Eventos existentes continuarão vinculados a ela.</p>

    <?php if ($erroCategoria !== ''): ?>
        <div class="mensagem-autenticacao" role="alert">
            <?= $escaparCategoria($erroCategoria) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php?pagina=categorias&amp;acao=atualizar&amp;id=<?= (int) $categoria->getIdCategoria() ?>">
        <input type="hidden" name="csrf_token" value="<?= $escaparCategoria($_SESSION['csrf_token'] ?? '') ?>">
        <div class="campo-evento">
            <label for="nome_categoria">Nome da categoria *</label>
            <input id="nome_categoria" name="nome" type="text" maxlength="50" required autofocus value="<?= $escaparCategoria($nomeCategoria) ?>">
        </div>
        <div class="acoes-formulario-evento">
            <button type="submit" class="botao-organizador">Salvar alterações</button>
            <a class="link-voltar-eventos" href="index.php?pagina=categorias&amp;acao=listar">Cancelar</a>
        </div>
    </form>
</section>
