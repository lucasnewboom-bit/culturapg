<?php
function hNotificacao($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>

<section class="secao-categorias pagina-notificacoes">
    <div class="cabecalho-gerenciamento-eventos">
        <div>
            <h1>Notificações</h1>
            <p>Atualizações dos eventos que você favoritou.</p>
        </div>
        <span class="contador-nao-lidas"><?= (int) $quantidadeNaoLidas ?> não lida(s)</span>
    </div>

    <?php if ($mensagemNotificacao !== ''): ?>
        <div class="mensagem-autenticacao" role="status"><?= hNotificacao($mensagemNotificacao) ?></div>
    <?php endif; ?>
    <?php if ($erroNotificacao !== ''): ?>
        <div class="mensagem-autenticacao" role="alert"><?= hNotificacao($erroNotificacao) ?></div>
    <?php endif; ?>

    <?php if (!$notificacoes): ?>
        <p class="mensagem-eventos-vazia">Você ainda não recebeu notificações.</p>
    <?php else: ?>
        <div class="lista-notificacoes">
            <?php foreach ($notificacoes as $notificacao): ?>
                <article class="item-notificacao <?= empty($notificacao['lida']) ? 'nao-lida' : 'lida' ?>">
                    <div class="conteudo-notificacao">
                        <small><?= hNotificacao(date('d/m/Y H:i', strtotime($notificacao['data_envio']))) ?></small>
                        <h2><?= hNotificacao($notificacao['titulo_atualizacao']) ?></h2>
                        <p class="evento-da-notificacao">
                            <a href="index.php?pagina=evento&amp;id=<?= (int) $notificacao['id_evento'] ?>">
                                <?= hNotificacao($notificacao['titulo_evento']) ?>
                            </a>
                        </p>
                        <p><?= nl2br(hNotificacao($notificacao['mensagem'])) ?></p>
                    </div>
                    <?php if (empty($notificacao['lida'])): ?>
                        <form method="post" action="index.php?pagina=notificacoes&amp;acao=ler">
                            <input type="hidden" name="csrf_token" value="<?= hNotificacao($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="id_notificacao" value="<?= (int) $notificacao['id_notificacao'] ?>">
                            <button type="submit" class="botao-secundario-evento">Marcar como lida</button>
                        </form>
                    <?php else: ?>
                        <span class="rotulo-lida">Lida</span>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
