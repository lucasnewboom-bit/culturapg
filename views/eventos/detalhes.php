
<?php

if (!isset($evento) || !$evento) {
    echo "<h2>Evento não encontrado.</h2>";
    return;
}

function escapar($valor) {
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Identificação do evento
$idEvento = (int) $evento['id_evento'];

$favoritado = $favoritado ?? false;

// Imagens principais dos eventos
$imagensEventos = [
    1 => 'corposquefalam1.png',
    2 => 'sextaas61.png',
    3 => 'aquelequedizsimaquelequediznao1.png',
    4 => 'bugiganga1.png',
    5 => 'sinfoniadasaguas1.png',
    6 => 'mostradetalentos1.png'
];

$imagem = $imagensEventos[$idEvento] ?? '';

$imagemCapa = $imagem !== ''
    ? "img/eventos/" . $imagem
    : '';

$configuracaoContato = [
    'whatsapp' => [
        'nome' => 'WhatsApp',
        'classe' => 'botao-contato-whatsapp',
        'icone' => '💬'
    ],
    'instagram' => [
        'nome' => 'Instagram',
        'classe' => 'botao-contato-instagram',
        'icone' => '◎'
    ],
    'sympla' => [
        'nome' => 'Sympla',
        'classe' => 'botao-contato-sympla',
        'icone' => '🎟'
    ]
];

?>

<a
    href="index.php?pagina=catalogo"
    class="botao-voltar-evento"
    onclick="
        if (
            document.referrer &&
            new URL(document.referrer).origin === window.location.origin
        ) {
            window.history.back();
            return false;
        }
    "
>
    ← Voltar
</a>

<div class="pagina-evento">

    <!-- CAPA DO EVENTO -->
    <div
        class="evento-capa"
        style="
            background-image:
                linear-gradient(
                    to top,
                    rgba(8, 5, 34, 0.95),
                    rgba(49, 13, 48, 0.25)
                ),
                url('<?= escapar($imagemCapa) ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        "
    >

        <span class="evento-categoria">
            <?= escapar($evento['categoria']) ?>
        </span>

        <h1>
            <?= escapar($evento['titulo']) ?>
        </h1>

        <!-- FAVORITAR EVENTO -->
        <form
            action="index.php?pagina=evento&id=<?= $idEvento ?>&acao=favoritar"
            method="POST"
            class="form-favorito-evento"
        >
            <input type="hidden" name="csrf_token" value="<?= escapar($_SESSION['csrf_token'] ?? '') ?>">
            <button
                type="submit"
                class="botao-favorito-evento <?= $favoritado ? 'favoritado' : '' ?>"
                aria-label="<?= $favoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos' ?>"
                title="<?= $favoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos' ?>"
            >♥</button>
        </form>

    </div>

    <div class="evento-conteudo">

        <!-- DESCRIÇÃO -->
        <section class="evento-descricao">

            <h2>Sobre o espetáculo</h2>

            <p>
                <?= nl2br(escapar($evento['descricao'])) ?>
            </p>

        </section>

        <!-- INFORMAÇÕES -->
        <section class="evento-informacoes">

            <h2>Informações do evento</h2>

            <div class="informacao-item">
                <strong>Local</strong>
                <p><?= escapar($evento['local']) ?></p>
                <p><?= escapar($evento['endereco']) ?></p>
            </div>

            <div class="informacao-item">
                <strong>Classificação indicativa</strong>
                <p>
                    <?= escapar($evento['classificacao_indicativa']) ?>
                </p>
            </div>

            <div class="informacao-item">
                <strong>Ingressos</strong>

                <?php if ($evento['gratuito']): ?>

                    <p>Entrada gratuita</p>

                <?php else: ?>

                    <p>
                        R$ <?= number_format(
                            (float) $evento['valor'],
                            2,
                            ',',
                            '.'
                        ) ?>
                    </p>

                <?php endif; ?>

            </div>

        </section>

        <!-- DATAS E HORÁRIOS -->
        <section class="evento-sessoes">

            <h2>Datas e horários</h2>

            <?php if (!empty($evento['sessoes'])): ?>

                <?php foreach ($evento['sessoes'] as $sessao): ?>

                    <div class="sessao-item">

                        <span>
                            <?= date(
                                'd/m/Y',
                                strtotime($sessao['data_evento'])
                            ) ?>
                        </span>

                        <span>
                            <?= substr(
                                $sessao['horario_inicio'],
                                0,
                                5
                            ) ?>

                            <?php if (!empty($sessao['horario_fim'])): ?>
                                –
                                <?= substr(
                                    $sessao['horario_fim'],
                                    0,
                                    5
                                ) ?>
                            <?php endif; ?>

                        </span>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p>Não há sessões cadastradas.</p>

            <?php endif; ?>

        </section>

        <!-- CONTATO E INGRESSOS -->
        <section class="evento-contato">

            <h2>Contato e ingressos</h2>

            <div class="botoes-contato">

                <?php
                $tiposContato = $evento['links'] ?? [];

                foreach ($tiposContato as $tipo):

                    if (!isset($configuracaoContato[$tipo])) {
                        continue;
                    }

                    $config = $configuracaoContato[$tipo];
                ?>

                    <button
                        type="button"
                        class="botao-contato <?= escapar($config['classe']) ?>"
                    >
                        <span><?= $config['icone'] ?></span>
                        <span><?= escapar($config['nome']) ?></span>
                    </button>

                <?php endforeach; ?>

            </div>

        </section>

    </div>

</div>
