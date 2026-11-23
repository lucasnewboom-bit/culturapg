<?php
function hFormularioEvento($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
$emEdicao = is_array($evento);
$dadosEvento = $evento ?? [];
if ($dadosFormulario) {
    $dadosEvento = array_merge($dadosEvento, $dadosFormulario);
    if (isset($dadosFormulario['sessoes']) && is_array($dadosFormulario['sessoes'])) {
        $dadosEvento['sessoes'] = $dadosFormulario['sessoes'];
    }
    if (isset($dadosFormulario['links']) && is_array($dadosFormulario['links'])) {
        $dadosEvento['links_completos'] = [];
        foreach ($dadosFormulario['links'] as $tipo => $url) $dadosEvento['links_completos'][] = ['tipo_link' => $tipo, 'url' => $url];
    }
}
$sessoesForm = $dadosEvento['sessoes'] ?? [['data_evento' => '', 'horario_inicio' => '', 'horario_fim' => '']];
$linksForm = ['whatsapp' => '', 'instagram' => '', 'sympla' => ''];
foreach (($dadosEvento['links_completos'] ?? []) as $linkForm) $linksForm[$linkForm['tipo_link']] = $linkForm['url'] ?? '';
$acaoFormulario = $emEdicao ? 'atualizar' : 'cadastrar';
$urlFormulario = 'index.php?pagina=organizador&acao=' . $acaoFormulario . ($emEdicao ? '&id=' . (int) $evento['id_evento'] : '');
?>

<section class="formulario-evento secao-categorias">
    <div class="cabecalho-gerenciamento-eventos">
        <div>
            <h1><?= $emEdicao ? 'Editar evento' : 'Cadastrar evento' ?></h1>
            <p>Os campos de localidade são vinculados a Ponta Grossa - PR.</p>
        </div>
        <a class="link-voltar-eventos" href="index.php?pagina=organizador">Voltar aos meus eventos</a>
    </div>

    <?php if ($erro !== ''): ?><div class="mensagem-autenticacao" role="alert"><?= hFormularioEvento($erro) ?></div><?php endif; ?>
    <?php if ($mensagem !== ''): ?><div class="mensagem-autenticacao" role="status"><?= hFormularioEvento($mensagem) ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data" action="<?= hFormularioEvento($urlFormulario) ?>" class="form-evento" novalidate>
        <input type="hidden" name="csrf_token" value="<?= hFormularioEvento($csrfEventos) ?>">

        <h2>Informações do evento</h2>
        <div class="grade-formulario-evento">
            <div class="campo-evento campo-evento-largo">
                <label for="titulo">Título *</label>
                <input id="titulo" name="titulo" maxlength="150" required value="<?= hFormularioEvento($dadosEvento['titulo'] ?? '') ?>">
            </div>
            <div class="campo-evento">
                <label for="categoria_id">Categoria *</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id_categoria'] ?>" <?= (string) ($dadosEvento['categoria_id'] ?? '') === (string) $categoria['id_categoria'] ? 'selected' : '' ?>><?= hFormularioEvento($categoria['nome']) ?><?= empty($categoria['ativo']) ? ' (indisponível para novos eventos)' : '' ?></option>
                    <?php endforeach; ?>
                </select>
                <a
                    class="botao-secundario-evento botao-criar-categoria-evento"
                    href="index.php?pagina=categorias&amp;acao=cadastrar&amp;origem=evento"
                    target="_blank"
                    rel="opener"
                >Não encontrou uma categoria? Crie a sua para esse evento</a>
            </div>
            <div class="campo-evento">
                <label for="classificacao_indicativa">Classificação indicativa</label>
                <select id="classificacao_indicativa" name="classificacao_indicativa">
                    <option value="">Não informada</option>
                    <?php foreach (['Livre', '10', '12', '14', '16', '18'] as $classificacao): ?>
                        <option value="<?= $classificacao ?>" <?= (string) ($dadosEvento['classificacao_indicativa'] ?? '') === $classificacao ? 'selected' : '' ?>><?= $classificacao === 'Livre' ? 'Livre' : $classificacao . ' anos' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo-evento campo-evento-largo">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="4"><?= hFormularioEvento($dadosEvento['descricao'] ?? '') ?></textarea>
            </div>
            <div class="campo-evento campo-evento-check">
                <label><input type="checkbox" name="gratuito" value="1" <?= !empty($dadosEvento['gratuito']) ? 'checked' : '' ?>> Evento gratuito</label>
            </div>
            <div class="campo-evento">
                <label for="valor">Valor (R$)</label>
                <input id="valor" name="valor" type="number" min="0" step="0.01" value="<?= hFormularioEvento($dadosEvento['valor'] ?? '0.00') ?>">
            </div>
        </div>

        <h2>Local</h2>
        <p class="ajuda-mapa-evento">Clique no mapa para marcar o ponto do evento. Essa localização será usada no mapa interativo.</p>
        <div id="mapa-selecao-evento" class="mapa-selecao-evento" role="application" aria-label="Mapa para selecionar o local do evento"></div>
        <div class="grade-formulario-evento">
            <div class="campo-evento campo-evento-largo">
                <label for="local_predefinido">Local sugerido</label>
                <select id="local_predefinido">
                    <option value="">Escolha um local para preencher automaticamente</option>
                    <option value="opera">Cine Teatro Ópera</option>
                    <option value="marista">Teatro Marista (Cine Marista - Teatro Marista Pio XII)</option>
                    <option value="concha">Concha Acústica</option>
                </select>
            </div>
            <div class="campo-evento">
                <label for="local_nome">Nome do local *</label>
                <input id="local_nome" name="local_nome" maxlength="100" required value="<?= hFormularioEvento($dadosEvento['local_nome'] ?? '') ?>">
            </div>
            <div class="campo-evento campo-evento-largo">
                <label for="endereco">Endereço em Ponta Grossa - PR *</label>
                <input id="endereco" name="endereco" maxlength="255" required value="<?= hFormularioEvento($dadosEvento['endereco'] ?? '') ?>">
            </div>
            <div class="campo-evento">
                <label for="latitude">Latitude *</label>
                <input id="latitude" name="latitude" type="number" min="-90" max="90" step="any" value="<?= hFormularioEvento($dadosEvento['latitude'] ?? '') ?>">
            </div>
            <div class="campo-evento">
                <label for="longitude">Longitude *</label>
                <input id="longitude" name="longitude" type="number" min="-180" max="180" step="any" value="<?= hFormularioEvento($dadosEvento['longitude'] ?? '') ?>">
            </div>
        </div>

        <h2>Imagem do evento</h2>
        <div class="campo-evento campo-evento-imagem">
            <label for="imagem">Imagem principal (JPG, PNG ou WEBP, até 5 MB)</label>
            <input id="imagem" name="imagem" type="file" accept="image/jpeg,image/png,image/webp">
            <?php if (!empty($dadosEvento['imagem'])): ?>
                <img class="previa-imagem-evento" src="<?= hFormularioEvento($dadosEvento['imagem']) ?>" alt="Imagem atual do evento">
                <p>Escolha outra imagem para substituir a atual. Se deixar em branco, ela será mantida.</p>
            <?php endif; ?>
        </div>

        <h2>Contato</h2>
        <div class="grade-formulario-evento">
            <div class="campo-evento">
                <label for="tipo_contato">Tipo de contato</label>
                <select id="tipo_contato" name="tipo_contato">
                    <option value="">Não informado</option>
                    <?php foreach (['whatsapp' => 'WhatsApp', 'email' => 'E-mail', 'telefone' => 'Telefone', 'site' => 'Site', 'outro' => 'Outro'] as $tipo => $rotulo): ?>
                        <option value="<?= $tipo ?>" <?= ($dadosEvento['tipo_contato'] ?? '') === $tipo ? 'selected' : '' ?>><?= $rotulo ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo-evento campo-evento-largo">
                <label for="link_contato">Link ou informação de contato</label>
                <input id="link_contato" name="link_contato" maxlength="500" value="<?= hFormularioEvento($dadosEvento['link_contato'] ?? '') ?>" placeholder="https://...">
            </div>
        </div>

        <h2>Datas e horários</h2>
        <div id="sessoes-evento">
            <?php foreach ($sessoesForm as $indice => $sessao): ?>
                <div class="linha-sessao-evento">
                    <div class="campo-evento"><label>Data *</label><input type="date" name="sessoes[<?= (int) $indice ?>][data_evento]" value="<?= hFormularioEvento($sessao['data_evento'] ?? '') ?>"></div>
                    <div class="campo-evento"><label>Início *</label><input type="time" name="sessoes[<?= (int) $indice ?>][horario_inicio]" value="<?= hFormularioEvento(substr((string) ($sessao['horario_inicio'] ?? ''), 0, 5)) ?>"></div>
                    <div class="campo-evento"><label>Término</label><input type="time" name="sessoes[<?= (int) $indice ?>][horario_fim]" value="<?= hFormularioEvento(substr((string) ($sessao['horario_fim'] ?? ''), 0, 5)) ?>"></div>
                    <button type="button" class="botao-remover-sessao" aria-label="Remover data">Remover</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="adicionar-sessao-evento" class="botao-secundario-evento">Adicionar outra data</button>

        <h2>Links de divulgação</h2>
        <div class="grade-formulario-evento">
            <?php foreach (['whatsapp' => 'WhatsApp', 'instagram' => 'Instagram', 'sympla' => 'Sympla'] as $tipo => $rotulo): ?>
                <div class="campo-evento"><label for="link_<?= $tipo ?>"><?= $rotulo ?></label><input id="link_<?= $tipo ?>" type="url" name="links[<?= $tipo ?>]" maxlength="500" value="<?= hFormularioEvento($linksForm[$tipo]) ?>" placeholder="https://..."></div>
            <?php endforeach; ?>
        </div>

        <?php if (!$temOrganizador && !$emEdicao): ?>
            <h2>Seu cadastro de organizador</h2>
            <div class="campo-evento">
                <label for="tipo_organizador">Que tipo de organizador você é? *</label>
                <input id="tipo_organizador" name="tipo_organizador" maxlength="50" required value="<?= hFormularioEvento($dadosEvento['tipo_organizador'] ?? '') ?>" placeholder="Ex.: artista, produtor, espaço cultural">
            </div>
        <?php endif; ?>

        <div class="acoes-formulario-evento">
            <button type="submit" class="botao-organizador"><?= $emEdicao ? 'Salvar alterações' : 'Cadastrar evento' ?></button>
            <a class="link-voltar-eventos" href="index.php?pagina=organizador">Cancelar</a>
        </div>
    </form>
</section>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
window.addEventListener('message', function (eventoMensagem) {
    if (eventoMensagem.origin !== window.location.origin) return;

    const dados = eventoMensagem.data;
    if (!dados || !dados.categoria) return;

    const seletorCategoria = document.getElementById('categoria_id');
    const idCategoria = String(dados.categoria.id || '');
    if (!seletorCategoria || !idCategoria) return;

    if (dados.tipo === 'culturapg_categoria_excluida') {
        const opcaoRemovida = Array.from(seletorCategoria.options).find(function (item) {
            return item.value === idCategoria;
        });
        if (opcaoRemovida) opcaoRemovida.remove();
        if (seletorCategoria.value === idCategoria) seletorCategoria.value = '';
        return;
    }

    if (dados.tipo !== 'culturapg_categoria_criada') return;

    let opcao = Array.from(seletorCategoria.options).find(function (item) {
        return item.value === idCategoria;
    });

    if (!opcao) {
        opcao = document.createElement('option');
        opcao.value = idCategoria;
        opcao.textContent = String(dados.categoria.nome || 'Nova categoria');
        seletorCategoria.add(opcao);
    }

    seletorCategoria.value = idCategoria;
    seletorCategoria.dispatchEvent(new Event('change', { bubbles: true }));
});

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('sessoes-evento');
    const botao = document.getElementById('adicionar-sessao-evento');
    let proximoIndice = container.querySelectorAll('.linha-sessao-evento').length;

    function adicionarSessao() {
        const linha = document.createElement('div');
        linha.className = 'linha-sessao-evento';
        linha.innerHTML = '<div class="campo-evento"><label>Data *</label><input type="date" name="sessoes[' + proximoIndice + '][data_evento]"></div>' +
            '<div class="campo-evento"><label>Início *</label><input type="time" name="sessoes[' + proximoIndice + '][horario_inicio]"></div>' +
            '<div class="campo-evento"><label>Término</label><input type="time" name="sessoes[' + proximoIndice + '][horario_fim]"></div>' +
            '<button type="button" class="botao-remover-sessao" aria-label="Remover data">Remover</button>';
        container.appendChild(linha);
        proximoIndice++;
    }

    botao.addEventListener('click', adicionarSessao);
    container.addEventListener('click', function (evento) {
        if (evento.target.classList.contains('botao-remover-sessao')) {
            const linhas = container.querySelectorAll('.linha-sessao-evento');
            if (linhas.length > 1) evento.target.closest('.linha-sessao-evento').remove();
        }
    });

    const mapaEl = document.getElementById('mapa-selecao-evento');
    const latitude = document.getElementById('latitude');
    const longitude = document.getElementById('longitude');
    const localPredefinido = document.getElementById('local_predefinido');
    const centroPG = [-25.093633, -50.160567];
    const mapaLocal = L.map(mapaEl).setView(centroPG, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
    }).addTo(mapaLocal);
    let marcador = null;

    function posicionarMarcador(lat, lng, centralizar) {
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
        if (marcador) marcador.setLatLng([lat, lng]);
        else marcador = L.marker([lat, lng], { draggable: true }).addTo(mapaLocal);
        marcador.off('dragend').on('dragend', function (eventoMarcador) {
            const ponto = eventoMarcador.target.getLatLng();
            latitude.value = ponto.lat.toFixed(7);
            longitude.value = ponto.lng.toFixed(7);
        });
        latitude.value = lat.toFixed(7);
        longitude.value = lng.toFixed(7);
        if (centralizar) mapaLocal.setView([lat, lng], 16);
    }

    mapaLocal.on('click', function (eventoMapa) {
        posicionarMarcador(eventoMapa.latlng.lat, eventoMapa.latlng.lng, false);
    });
    const locaisPredefinidos = {
        opera: {
            nome: 'Cine Teatro Opera',
            endereco: 'Rua XV de Novembro, 468 - Centro, Ponta Grossa - PR',
            latitude: -25.0964720,
            longitude: -50.1601390
        },
        marista: {
            nome: 'Cine Marista - Teatro Marista Pio XII',
            endereco: 'Rua Rodrigues Alves, 701 - Ponta Grossa - PR',
            latitude: -25.0825200,
            longitude: -50.1615400
        },
        concha: {
            nome: 'Concha Acustica',
            endereco: 'Praca Barao do Rio Branco - Centro, Ponta Grossa - PR',
            latitude: -25.0936330,
            longitude: -50.1605670
        }
    };
    localPredefinido.addEventListener('change', function () {
        const local = locaisPredefinidos[localPredefinido.value];
        if (!local) return;
        document.getElementById('local_nome').value = local.nome;
        document.getElementById('endereco').value = local.endereco;
        posicionarMarcador(local.latitude, local.longitude, true);
    });
    const latInicial = Number(latitude.value);
    const lngInicial = Number(longitude.value);
    if (latitude.value !== '' && longitude.value !== '' && Number.isFinite(latInicial) && Number.isFinite(lngInicial)) {
        posicionarMarcador(latInicial, lngInicial, true);
    }
    [latitude, longitude].forEach(function (campo) {
        campo.addEventListener('change', function () {
            const lat = Number(latitude.value);
            const lng = Number(longitude.value);
            if (latitude.value !== '' && longitude.value !== '') posicionarMarcador(lat, lng, true);
        });
    });
    setTimeout(function () { mapaLocal.invalidateSize(); }, 100);
});
</script>
