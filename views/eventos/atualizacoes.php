<?php
function hAtualizacaoEvento($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>

<section class="secao-categorias painel-atualizacoes">
    <div class="cabecalho-gerenciamento-eventos">
        <div>
            <h1>Atualizações do evento</h1>
            <p><?= hAtualizacaoEvento($eventoAtual['titulo']) ?></p>
        </div>
        <a class="link-voltar-eventos" href="index.php?pagina=organizador">Voltar aos meus eventos</a>
    </div>

    <?php if ($mensagem !== ''): ?>
        <div class="mensagem-autenticacao" role="status"><?= hAtualizacaoEvento($mensagem) ?></div>
    <?php endif; ?>
    <?php if ($erroAtualizacao !== ''): ?>
        <div class="mensagem-autenticacao" role="alert"><?= hAtualizacaoEvento($erroAtualizacao) ?></div>
    <?php endif; ?>

    <?php if ($eventoAtual['status'] === 'ativo'): ?>
        <form method="post" action="index.php?pagina=organizador&amp;acao=publicar_atualizacao&amp;id=<?= (int) $eventoAtual['id_evento'] ?>" class="form-publicar-atualizacao">
            <input type="hidden" name="csrf_token" value="<?= hAtualizacaoEvento($csrfEventos) ?>">
            <h2>Publicar uma atualização</h2>
            <p>As pessoas que favoritaram este evento receberão uma notificação no CulturaPG.</p>
            <div class="campo-evento">
                <label for="titulo-atualizacao">Título *</label>
                <input id="titulo-atualizacao" name="titulo" maxlength="150" required value="<?= hAtualizacaoEvento($tituloAtualizacao ?? '') ?>">
            </div>
            <div class="campo-evento">
                <label for="mensagem-atualizacao">Mensagem *</label>
                <textarea id="mensagem-atualizacao" name="mensagem" rows="5" required><?= hAtualizacaoEvento($mensagemAtualizacao ?? '') ?></textarea>
            </div>
            <button type="submit" class="botao-organizador">Publicar atualização</button>
        </form>
    <?php else: ?>
        <p class="mensagem-eventos-vazia">Este evento está inativo e não pode receber novas atualizações.</p>
    <?php endif; ?>

    <div class="lista-atualizacoes-evento">
        <h2>Atualizações publicadas</h2>
        <?php if (!$atualizacoes): ?>
            <p class="mensagem-eventos-vazia">Nenhuma atualização foi publicada para este evento.</p>
        <?php else: ?>
            <?php foreach ($atualizacoes as $atualizacao): ?>
                <article class="item-atualizacao-evento">
                    <small><?= hAtualizacaoEvento(date('d/m/Y H:i', strtotime($atualizacao['data_atualizacao']))) ?></small>
                    <h3><?= hAtualizacaoEvento($atualizacao['titulo']) ?></h3>
                    <p><?= nl2br(hAtualizacaoEvento($atualizacao['mensagem'])) ?></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
