
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="secao-categorias">

    <h2>Categorias cadastradas</h2>

    <?php if (!empty($mensagemCategorias)): ?>
        <div class="mensagem-autenticacao" role="status">
            <?= htmlspecialchars($mensagemCategorias, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <a href="index.php?pagina=categorias&acao=cadastrar">
        Nova categoria
    </a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($categorias as $categoria): ?>

                <tr>
                    <td><?= htmlspecialchars((string) $categoria->getIdCategoria()) ?></td>

                    <td><?= htmlspecialchars($categoria->getNome()) ?></td>

                    <td>
                        <?= $categoria->getAtivo() ? 'Ativa' : 'Inativa' ?>
                    </td>

                    <td>
                        <?php if (isset($_SESSION['usuario']['id']) && $categoria->getAtivo() && $categoria->getCriadoPorUsuarioId() === (int) $_SESSION['usuario']['id']): ?>
                            <a href="index.php?pagina=categorias&amp;acao=editar&amp;id=<?= (int) $categoria->getIdCategoria() ?>">Editar</a>
                        <?php else: ?>
                            <span>—</span>
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
