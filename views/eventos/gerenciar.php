<?php
function hEvento($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>

<section class="secao-categorias gerenciamento-eventos">
    <div class="cabecalho-gerenciamento-eventos">
        <div>
            <h1>Meus eventos</h1>
            <p>Acompanhe e administre os eventos cadastrados pela sua conta.</p>
        </div>
        <a class="botao-organizador" href="index.php?pagina=organizador&amp;acao=novo">Cadastrar evento</a>
    </div>

    <?php if ($mensagem !== ''): ?>
        <div class="mensagem-autenticacao" role="status"><?= hEvento($mensagem) ?></div>
    <?php endif; ?>

    <?php if (!$temOrganizador): ?>
        <p class="mensagem-eventos-vazia">Este será seu primeiro evento. Você informará seu tipo de organizador no cadastro.</p>
    <?php endif; ?>

    <?php if (!$eventos): ?>
        <p class="mensagem-eventos-vazia">Você ainda não cadastrou eventos.</p>
    <?php else: ?>
        <div class="tabela-eventos-responsiva">
            <table>
                <thead><tr><th>Evento</th><th>Categoria</th><th>Local</th><th>Próxima data</th><th>Status</th><th>Ações</th></tr></thead>
                <tbody>
                <?php foreach ($eventos as $item): ?>
                    <tr>
                        <td><?= hEvento($item['titulo']) ?></td>
                        <td><?= hEvento($item['categoria']) ?></td>
                        <td><?= hEvento($item['local']) ?></td>
                        <td><?= !empty($item['proxima_data']) ? hEvento(date('d/m/Y', strtotime($item['proxima_data']))) : 'Sem próximas datas' ?></td>
                        <td><?= hEvento(ucfirst($item['status'])) ?></td>
                        <td class="acoes-evento">
                            <a href="index.php?pagina=evento&amp;id=<?= (int) $item['id_evento'] ?>">Detalhes</a>
                            <?php if ($item['status'] === 'ativo'): ?>
                                <a href="index.php?pagina=organizador&amp;acao=editar&amp;id=<?= (int) $item['id_evento'] ?>">Editar</a>
                                <a href="index.php?pagina=organizador&amp;acao=atualizacoes&amp;id=<?= (int) $item['id_evento'] ?>">Atualizações</a>
                                <form method="post" action="index.php?pagina=organizador&amp;acao=excluir&amp;id=<?= (int) $item['id_evento'] ?>" onsubmit="return confirm('Remover este evento da divulgação? Os favoritos e o histórico serão preservados.');">
                                    <input type="hidden" name="csrf_token" value="<?= hEvento($csrfEventos) ?>">
                                    <button type="submit" class="acao-excluir-evento">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
