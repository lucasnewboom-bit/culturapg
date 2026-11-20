
<?php

$imagensEventos = [
    1 => 'corposquefalam1.png',
    2 => 'sextaas61.png',
    3 => 'aquelequedizsimaquelequediznao1.png',
    4 => 'bugiganga1.png',
    5 => 'sinfoniadasaguas1.png',
    6 => 'mostradetalentos1.png'
];

$categorias = [];

foreach ($eventos as $evento) {
    if (!empty($evento['categoria'])) {
        $categorias[$evento['categoria']] = $evento['categoria'];
    }
}

$favoritos = array_map('intval', $favoritos ?? []);

$mostrarFavoritos = ($_GET['filtro'] ?? '') === 'favoritos';

?>

<div class="pagina-catalogo">

    <!-- CABEÇALHO -->
    <section class="cabecalho-catalogo">

        <div>
            <span class="subtitulo-catalogo">CULTURA PG</span>

            <h1 id="tituloCatalogo">
                <?= $mostrarFavoritos ? 'Eventos favoritados' : 'Descubra novos eventos' ?>
            </h1>

            <p id="descricaoCatalogo">
                <?= $mostrarFavoritos
                    ? 'Ao clicar no coraçãozinho na página de um evento, você o adiciona aos seus favoritos. Assim, poderá acompanhar notificações e atualizações sobre os eventos que escolheu.'
                    : 'Encontre espetáculos, apresentações e experiências culturais em Ponta Grossa.' ?>
            </p>
        </div>

    </section>

    <!-- PESQUISA E FILTROS -->
    <section class="filtros-catalogo">

        <div class="pesquisa-catalogo">
            <span>⌕</span>

            <input
                type="search"
                id="pesquisaEvento"
                placeholder="Pesquisar eventos..."
            >
        </div>

        <div class="opcoes-filtro-catalogo">

            <select id="filtroCategoria">
                <option value="">Todas as categorias</option>

                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <select id="filtroData">
                <option value="">Todas as datas</option>
                <option value="hoje">Hoje</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mês</option>
            </select>

        </div>

    </section>

    <!-- FILTRO DE FAVORITOS -->
    <section class="seletor-favoritos-catalogo">

        <button
            type="button"
            id="mostrarTodos"
            class="<?= !$mostrarFavoritos ? 'ativo' : '' ?>"
        >
            Todos os eventos
        </button>

        <button
            type="button"
            id="mostrarFavoritos"
            class="<?= $mostrarFavoritos ? 'ativo' : '' ?>"
        >
            ♥ Eventos favoritados
        </button>

    </section>

    <!-- RESULTADOS -->
    <section class="resultados-catalogo">

        <div class="cabecalho-resultados">
            <h2 id="tituloResultados">
                <?= $mostrarFavoritos ? 'Meus eventos favoritados' : 'Eventos disponíveis' ?>
            </h2>

            <span id="contadorEventos">
                <?= count($eventos) ?> eventos encontrados
            </span>
        </div>

        <?php if (empty($eventos)): ?>

            <p class="catalogo-vazio">
                Nenhum evento disponível no momento.
            </p>

        <?php else: ?>

            <div class="grade-eventos-catalogo" id="gradeEventos">

                <?php foreach ($eventos as $evento): ?>

                    <?php
                    $idEvento = (int) $evento['id_evento'];

                    $imagem = $imagensEventos[$idEvento] ?? '';

                    $data = $evento['proxima_data'] ?? '';

                    $dataFormatada = $data
                        ? date('d/m/Y', strtotime($data))
                        : 'Data a confirmar';

                    $dataFiltro = $data ?: '';

                    $favoritado = in_array($idEvento, $favoritos, true);
                    ?>

                    <a
                        href="index.php?pagina=evento&id=<?= $idEvento ?>"
                        class="card-evento-catalogo"
                        data-titulo="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                        data-categoria="<?= htmlspecialchars($evento['categoria'], ENT_QUOTES, 'UTF-8') ?>"
                        data-local="<?= htmlspecialchars($evento['local'], ENT_QUOTES, 'UTF-8') ?>"
                        data-data="<?= htmlspecialchars($dataFiltro, ENT_QUOTES, 'UTF-8') ?>"
                        data-favorito="<?= $favoritado ? '1' : '0' ?>"
                    >

                        <div class="imagem-card-catalogo">

                            <?php if ($imagem !== ''): ?>

                                <img
                                    src="img/eventos/<?= htmlspecialchars($imagem, ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                >

                            <?php else: ?>

                                <div class="imagem-padrao-catalogo">
                                    CULTURA PG
                                </div>

                            <?php endif; ?>

                            <span class="categoria-card-catalogo">
                                <?= htmlspecialchars($evento['categoria'], ENT_QUOTES, 'UTF-8') ?>
                            </span>

                        </div>

                        <div class="conteudo-card-catalogo">

                            <h3>
                                <?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <p class="data-card-catalogo">
                                📅 <?= htmlspecialchars($dataFormatada, ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <p class="local-card-catalogo">
                                📍 <?= htmlspecialchars($evento['local'], ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <div class="rodape-card-catalogo">

                                <strong>
                                    <?= !empty($evento['gratuito'])
                                        ? 'Gratuito'
                                        : 'R$ ' . number_format((float) $evento['valor'], 2, ',', '.') ?>
                                </strong>

                                <span>Ver detalhes →</span>

                            </div>

                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

            <p id="nenhumResultado" class="catalogo-vazio" hidden>
                Nenhum evento corresponde aos filtros selecionados.
            </p>

        <?php endif; ?>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const pesquisa = document.getElementById('pesquisaEvento');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const filtroData = document.getElementById('filtroData');

    const botaoTodos = document.getElementById('mostrarTodos');
    const botaoFavoritos = document.getElementById('mostrarFavoritos');

    const cards = document.querySelectorAll('.card-evento-catalogo');
    const contador = document.getElementById('contadorEventos');
    const mensagemVazia = document.getElementById('nenhumResultado');

    const tituloResultados = document.getElementById('tituloResultados');
    const tituloCatalogo = document.getElementById('tituloCatalogo');
    const descricaoCatalogo = document.getElementById('descricaoCatalogo');

    let somenteFavoritos = <?= $mostrarFavoritos ? 'true' : 'false' ?>;

    function filtrarEventos() {

        const termo = pesquisa.value.toLowerCase().trim();
        const categoria = filtroCategoria.value.toLowerCase();
        const periodo = filtroData.value;

        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        let quantidade = 0;

        cards.forEach(function (card) {

            const titulo = card.dataset.titulo.toLowerCase();
            const categoriaEvento = card.dataset.categoria.toLowerCase();
            const local = card.dataset.local.toLowerCase();
            const data = card.dataset.data;

            const correspondePesquisa =
                titulo.includes(termo) ||
                categoriaEvento.includes(termo) ||
                local.includes(termo);

            const correspondeCategoria =
                categoria === '' || categoriaEvento === categoria;

            const correspondeFavorito =
                !somenteFavoritos || card.dataset.favorito === '1';

            let correspondeData = true;

            if (periodo !== '') {

                if (data === '') {
                    correspondeData = false;
                } else {

                    const dataEvento = new Date(data + 'T00:00:00');

                    if (periodo === 'hoje') {
                        correspondeData =
                            dataEvento.getTime() === hoje.getTime();
                    }

                    if (periodo === 'semana') {
                        const fimSemana = new Date(hoje);
                        fimSemana.setDate(hoje.getDate() + 7);

                        correspondeData =
                            dataEvento >= hoje && dataEvento <= fimSemana;
                    }

                    if (periodo === 'mes') {
                        correspondeData =
                            dataEvento.getMonth() === hoje.getMonth() &&
                            dataEvento.getFullYear() === hoje.getFullYear() &&
                            dataEvento >= hoje;
                    }
                }
            }

            const mostrar =
                correspondePesquisa &&
                correspondeCategoria &&
                correspondeData &&
                correspondeFavorito;

            card.style.display = mostrar ? '' : 'none';

            if (mostrar) {
                quantidade++;
            }

        });

        contador.textContent = quantidade +
            (quantidade === 1 ? ' evento encontrado' : ' eventos encontrados');

        mensagemVazia.hidden = quantidade !== 0;

        tituloResultados.textContent = somenteFavoritos
            ? 'Meus eventos favoritados'
            : 'Eventos disponíveis';

        tituloCatalogo.textContent = somenteFavoritos
            ? 'Eventos favoritados'
            : 'Descubra novos eventos';

        descricaoCatalogo.textContent = somenteFavoritos
            ? 'Ao clicar no coraçãozinho na página de um evento, você o adiciona aos seus favoritos. Assim, poderá acompanhar notificações e atualizações sobre os eventos que escolheu.'
            : 'Encontre espetáculos, apresentações e experiências culturais em Ponta Grossa.';

        botaoTodos.classList.toggle('ativo', !somenteFavoritos);
        botaoFavoritos.classList.toggle('ativo', somenteFavoritos);
    }

    botaoTodos.addEventListener('click', function () {
        somenteFavoritos = false;
        filtrarEventos();

        history.replaceState(null, '', 'index.php?pagina=catalogo');
    });

    botaoFavoritos.addEventListener('click', function () {
        somenteFavoritos = true;
        filtrarEventos();

        history.replaceState(null, '', 'index.php?pagina=catalogo&filtro=favoritos');
    });

    pesquisa.addEventListener('input', filtrarEventos);
    filtroCategoria.addEventListener('change', filtrarEventos);
    filtroData.addEventListener('change', filtrarEventos);

    filtrarEventos();

});
</script>
