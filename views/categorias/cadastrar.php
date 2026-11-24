<?php
$escaparCategoria = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};
$acaoFormularioCategoria = 'index.php?pagina=categorias&amp;acao=cadastrar'
    . ($origemCategoria === 'evento' ? '&amp;origem=evento' : '');
?>

<section class="secao-categorias">
    <h1>Criar categoria</h1>
    <p>Cadastre uma categoria para usar neste evento e nos próximos.</p>

    <?php if ($erroCategoria !== ''): ?>
        <div class="mensagem-autenticacao" role="alert">
            <?= $escaparCategoria($erroCategoria) ?>
        </div>
    <?php endif; ?>

    <?php if ($mensagemCategoria !== ''): ?>
        <div class="mensagem-autenticacao" role="status">
            <?= $escaparCategoria($mensagemCategoria) ?>
        </div>
    <?php endif; ?>

    <?php if ($categoriaCriada instanceof Categoria): ?>
        <div class="mensagem-autenticacao" role="status">
            A categoria <strong><?= $escaparCategoria($categoriaCriada->getNome()) ?></strong> foi salva.
            Volte à aba do evento; ela já estará selecionada no formulário.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $acaoFormularioCategoria ?>">
        <input type="hidden" name="csrf_token" value="<?= $escaparCategoria($_SESSION['csrf_token'] ?? '') ?>">
        <input type="hidden" name="origem" value="<?= $escaparCategoria($origemCategoria) ?>">

        <div class="campo-evento">
            <label for="nome_categoria">Nome da categoria *</label>
            <input
                id="nome_categoria"
                name="nome"
                type="text"
                maxlength="50"
                required
                autofocus
                value="<?= $escaparCategoria($nomeCategoria) ?>"
                placeholder="Ex.: Literatura"
            >
        </div>

        <div class="acoes-formulario-evento">
            <button type="submit" class="botao-organizador">Salvar categoria</button>
            <?php if ($origemCategoria === 'evento'): ?>
                <button type="button" class="botao-secundario-evento" onclick="window.close()">Voltar ao evento</button>
            <?php else: ?>
                <a class="link-voltar-eventos" href="index.php?pagina=categorias&amp;acao=listar">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>

    <h2>Minhas categorias</h2>
    <?php if (!$categoriasCriadas): ?>
        <p>Você ainda não criou categorias.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Categoria</th><th>Ação</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categoriasCriadas as $categoria): ?>
                    <tr>
                        <td><?= $escaparCategoria($categoria->getNome()) ?></td>
                        <td>
                            <a href="index.php?pagina=categorias&amp;acao=editar&amp;id=<?= (int) $categoria->getIdCategoria() ?>">Editar</a>
                            <form method="post" action="<?= $acaoFormularioCategoria ?>" onsubmit="return confirm('Remover esta categoria das opções de novos eventos? Eventos já cadastrados serão preservados.');">
                                <input type="hidden" name="csrf_token" value="<?= $escaparCategoria($_SESSION['csrf_token'] ?? '') ?>">
                                <input type="hidden" name="origem" value="<?= $escaparCategoria($origemCategoria) ?>">
                                <input type="hidden" name="operacao" value="excluir">
                                <input type="hidden" name="id_categoria" value="<?= (int) $categoria->getIdCategoria() ?>">
                                <button type="submit" class="botao-remover-sessao">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php if ($categoriaCriada instanceof Categoria || $categoriaExcluidaId > 0): ?>
    <script>
    if (window.opener && !window.opener.closed) {
        const mensagemCategoria = {
            tipo: <?= json_encode($categoriaCriada instanceof Categoria ? 'culturapg_categoria_criada' : 'culturapg_categoria_excluida') ?>,
            categoria: {
                id: <?= $categoriaCriada instanceof Categoria ? (int) $categoriaCriada->getIdCategoria() : (int) $categoriaExcluidaId ?>,
                nome: <?= json_encode($categoriaCriada instanceof Categoria ? $categoriaCriada->getNome() : '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
            }
        };
        window.opener.postMessage(mensagemCategoria, window.location.origin);
    }
    </script>
<?php endif; ?>
