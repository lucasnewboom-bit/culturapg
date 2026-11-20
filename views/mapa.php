
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<section class="pagina-mapa">

    <div class="controles-mapa">

        <div class="titulo-mapa">
            <h2>Descubra eventos culturais</h2>
            <p>Encontre o que acontece em Ponta Grossa.</p>
        </div>

        <div class="filtros-mapa">

            <select id="filtroCategoria">
                <option value="todos">Todas as categorias</option>
                <option value="musica">Música</option>
                <option value="teatro">Teatro</option>
                <option value="danca">Dança</option>
            </select>

            <select id="filtroPeriodo">
                <option value="todos">Qualquer período</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mês</option>
            </select>

        </div>

    </div>

    <div id="mapa"></div>

<!-- MODAL DE DETALHES DO EVENTO -->
<div id="modalEvento" class="modal-evento">

    <div class="modal-fundo" id="fecharModalFundo"></div>

    <div class="modal-painel">

        <button
            type="button"
            class="modal-fechar"
            id="fecharModal"
            aria-label="Fechar detalhes"
        >
            &times;
        </button>

        <div id="conteudoModalEvento">

            <!-- Os detalhes do evento serão carregados aqui -->

        </div>

    </div>

</div>

</section>

<!-- Biblioteca Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Agrupamento de marcadores -->
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"
>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"
>

<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<!-- Nosso mapa -->
<script src="js/mapa.js"></script>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>
