
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="secao-categorias">

    <h2>Categorias cadastradas</h2>

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
                        <a href="index.php?pagina=categorias&acao=editar&id=<?= $categoria->getIdCategoria() ?>">
                            Editar
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
