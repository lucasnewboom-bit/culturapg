
// ============================================
// CULTURAPG - MAPA DINÂMICO
// ============================================


// 1. INICIALIZAÇÃO DO MAPA

function iconeSprite(nome, classe = '') {
    return `<svg class="icone-cultura ${classe}" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><use href="img/icones.svg#${nome}"></use></svg>`;
}

const mapa = L.map('mapa').setView(
    [-25.093633, -50.160567],
    16
);


// 2. CAMADA DO OPENSTREETMAP

L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }
).addTo(mapa);


// 3. CONFIGURAÇÃO DOS AGRUPAMENTOS

const grupoMarcadores = L.markerClusterGroup({
    disableClusteringAtZoom: 15,
    maxClusterRadius: 50,
    animate: true
});

mapa.addLayer(grupoMarcadores);


// 4. CORES E CATEGORIAS

const categorias = {
    musica: {
        cor: '#2865f5',
        nome: 'Música'
    },

    teatro: {
        cor: '#d414b8',
        nome: 'Teatro'
    },

    danca: {
        cor: '#eacb36',
        nome: 'Dança'
    }
};


// 4.1. IMAGENS DOS EVENTOS

const imagensEventos = {
    1: {
        principal: 'corposquefalam1.png',
        popup: 'corposquefalam2.png'
    },
    2: {
        principal: 'sextaas61.png',
        popup: 'sextaas62.png'
    },
    3: {
        principal: 'aquelequedizsimaquelequediznao1.png',
        popup: 'aquelequedizsimaquelequediznao2.png'
    },
    4: {
        principal: 'bugiganga1.png',
        popup: 'bugiganga2.png'
    },
    5: {
        principal: 'sinfoniadasaguas1.png',
        popup: 'sinfoniadasaguas2.png'
    },
    6: {
        principal: 'mostradetalentos1.png',
        popup: 'mostradetalentos2.png'
    }
};


// Retornar os caminhos das imagens

function obterImagensEvento(evento) {

    if (evento && typeof evento.imagem === 'string' && evento.imagem !== '') {
        const imagem = new URL(evento.imagem, document.baseURI).href;
        return { principal: imagem, popup: imagem };
    }

    const imagens = imagensEventos[evento.id_evento];

    if (!imagens) {
        return {
            principal: '',
            popup: ''
        };
    }

    return {
        principal: `/culturapg/public/img/eventos/${imagens.principal}`,
        popup: `/culturapg/public/img/eventos/${imagens.popup}`
    };
}


// 5. NORMALIZAR O NOME DA CATEGORIA

function normalizarCategoria(nome) {

    const categoria = nome
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();

    if (categoria.includes('musica')) return 'musica';
    if (categoria.includes('teatro')) return 'teatro';
    if (categoria.includes('danca')) return 'danca';

    return null;
}


// 6. DESENHAR OS SÍMBOLOS DOS PINS EM SVG

function desenharSimbolo(categoria, x = 12, y = 12, tamanho = 24) {
    const simbolos = { musica: 'musica', teatro: 'teatro', danca: 'danca' };
    const icone = simbolos[categoria];
    if (!icone) return '';
    return `<svg x="${x}" y="${y}" width="${tamanho}" height="${tamanho}" viewBox="0 0 24 24" style="color:#090909" aria-hidden="true"><use href="img/icones.svg#${icone}"></use></svg>`;
}

function criarIcone(eventosDoLocal) {

    const categoriasDoLocal = [
        ...new Set(
            eventosDoLocal
                .map(evento => normalizarCategoria(evento.categoria))
                .filter(Boolean)
        )
    ];

    const cores = categoriasDoLocal.map(
        categoria => categorias[categoria].cor
    );

    const quantidade = eventosDoLocal.length;

    const largura = 48;
    const altura = 60;

    const formatoPin = `
        M 48 25
        C 48 12 37 2 24 2
        C 11 2 0 12 0 25
        C 0 38 24 59 24 59
        C 24 59 48 38 48 25 Z
    `;

    let preenchimento = '';

    if (cores.length === 1) {

        preenchimento = `
            <path d="${formatoPin}" fill="${cores[0]}"/>
        `;

    } else if (cores.length > 1) {

        const partes = cores.length;

        cores.forEach((cor, indice) => {

            const inicio = (indice / partes) * 48;
            const fim = ((indice + 1) / partes) * 48;

            preenchimento += `
                <clipPath id="parte-${indice}">
                    <rect x="${inicio}" y="0"
                          width="${fim - inicio}" height="60"/>
                </clipPath>

                <path d="${formatoPin}"
                      fill="${cor}"
                      clip-path="url(#parte-${indice})"/>
            `;
        });

    } else {

        preenchimento = `
            <path d="${formatoPin}" fill="#888888"/>
        `;
    }

    let simbolos = '';

    if (cores.length === 1) {

        simbolos = desenharSimbolo(categoriasDoLocal[0]);

    } else if (cores.length > 1) {

        const centros = cores.length === 2
            ? [12, 36]
            : cores.map((_, i) => 48 * (i + 0.5) / cores.length);

        categoriasDoLocal.forEach((categoria, i) => {

            simbolos += desenharSimbolo(categoria, centros[i] - 7, 16, 14);
        });
    }

    const svg = `
        <svg width="${largura}" height="${altura}"
             viewBox="0 0 48 60"
             xmlns="http://www.w3.org/2000/svg">

            <defs>
                ${cores.map((_, i) => `
                    <clipPath id="parte-${i}">
                        <rect x="${(i / cores.length) * 48}"
                              y="0"
                              width="${48 / cores.length}"
                              height="60"/>
                    </clipPath>
                `).join('')}
            </defs>

            ${preenchimento}

            <path d="${formatoPin}"
                  fill="none"
                  stroke="#050505"
                  stroke-width="2.5"/>

            ${simbolos}

        </svg>

        ${quantidade > 1 ? `
            <span class="quantidade-pin">${quantidade}</span>
        ` : ''}
    `;

    return L.divIcon({
        className: 'marcador-culturapg',
        html: svg,
        iconSize: [largura, altura],
        iconAnchor: [24, 59],
        popupAnchor: [0, -55]
    });
}


// 8. AGRUPAR EVENTOS DO MESMO LOCAL

function agruparPorLocal(eventos) {

    const locais = [];

    function normalizarNomeLocal(nome) {
        return String(nome || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]/g, '');
    }

    function distanciaMetros(lat1, lng1, lat2, lng2) {
        const radianos = valor => valor * Math.PI / 180;
        const diferencaLat = radianos(lat2 - lat1);
        const diferencaLng = radianos(lng2 - lng1);
        const a = Math.sin(diferencaLat / 2) ** 2 +
            Math.cos(radianos(lat1)) * Math.cos(radianos(lat2)) *
            Math.sin(diferencaLng / 2) ** 2;
        return 6371000 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    eventos.forEach(evento => {
        const latitude = Number(evento.latitude);
        const longitude = Number(evento.longitude);
        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) return;

        const nomeNormalizado = normalizarNomeLocal(evento.local);
        let local = locais.find(item =>
            item.nomeNormalizado === nomeNormalizado &&
            distanciaMetros(item.latitude, item.longitude, latitude, longitude) <= 40
        );

        if (!local) {
            local = {
                nome: evento.local,
                nomeNormalizado,
                latitude,
                longitude,
                eventos: []
            };
            locais.push(local);
        }

        local.eventos.push(evento);
    });

    return locais;
}


// 9. FUNÇÃO PARA ESCAPAR HTML

function escaparHTML(valor) {

    return String(valor ?? '').replace(/[&<>"']/g, caractere => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[caractere]);
}

// ============================================
// 10. CRIAR O CONTEÚDO DOS POPUPS
// ============================================

function criarPopup(local) {

    const listaEventos = local.eventos.map(evento => {

        const categoria = normalizarCategoria(evento.categoria);

        const nomeCategoria = categoria
            ? categorias[categoria].nome
            : evento.categoria;

        const imagens = obterImagensEvento(evento);

        const sessoes = evento.sessoes
            ? evento.sessoes.split('||')
            : [];

        const listaSessoes = sessoes.length > 0
            ? sessoes.map(sessao => `
                <div class="sessao-popup">
                    <span>${iconeSprite('calendario')} ${escaparHTML(sessao)}</span>
                </div>
            `).join('')
            : '<p class="sem-sessoes">Nenhuma sessão cadastrada.</p>';

        return `
            <div
                class="evento-popup"
                style="--imagem-popup: url('${imagens.popup}')"
            >

                <div class="cabecalho-evento-popup">

                     <strong class="evento-titulo">
                        ${escaparHTML(evento.titulo)}
                     </strong>

                    <span
                        class="coracao-favorito-popup ${evento.favoritado ? 'favoritado' : ''}"
                        title="${evento.favoritado ? 'Evento favoritado' : 'Evento não favoritado'}"
                    >${iconeSprite(evento.favoritado ? 'coracao-preenchido' : 'coracao')}</span>

                </div>
                <span class="evento-categoria">
                    ${escaparHTML(nomeCategoria)}
                </span>

                <div class="sessoes-popup">
                    ${listaSessoes}
                </div>

                <a
                    href="index.php?pagina=evento&id=${encodeURIComponent(evento.id_evento)}"
                    class="botao-detalhes"
                >
                    Ver detalhes
                </a>

            </div>
        `;

    }).join('');

    return `
        <div class="popup-culturapg">

            <h3>${escaparHTML(local.nome)}</h3>

            ${listaEventos}

        </div>
    `;
}


// ============================================
// 11. BUSCAR EVENTOS NA API E ATUALIZAR O MAPA
// ============================================

async function carregarEventos() {

    try {

        const categoria = document.getElementById('filtroCategoria').value;
        const periodo = document.getElementById('filtroPeriodo').value;

        const parametros = new URLSearchParams({
            categoria: categoria,
            periodo: periodo
        });

        const resposta = await fetch(
            `api/eventos.php?${parametros.toString()}`,
            { cache: 'no-store' }
        );

        if (!resposta.ok) {
            throw new Error('Erro ao consultar a API.');
        }

        const eventos = await resposta.json();

        grupoMarcadores.clearLayers();

        const locais = agruparPorLocal(eventos);

        locais.forEach(local => {

            const marcador = L.marker(
                [local.latitude, local.longitude],
                {
                    icon: criarIcone(local.eventos)
                }
            );

            marcador.bindPopup(criarPopup(local));

            grupoMarcadores.addLayer(marcador);
        });

        console.log('Eventos carregados:', eventos);
        console.log('Locais com pins:', locais);

    } catch (erro) {

        console.error('Erro ao carregar eventos:', erro);
    }
}


// 12. ATUALIZAR MAPA QUANDO OS FILTROS MUDAR

document.getElementById('filtroCategoria')
    .addEventListener('change', carregarEventos);

document.getElementById('filtroPeriodo')
    .addEventListener('change', carregarEventos);


// 13. CARREGAR EVENTOS INICIALMENTE

carregarEventos();


// ========================================
// 14. MODAL DE DETALHES DO EVENTO
// ========================================

const modalEvento = document.getElementById('modalEvento');
const conteudoModal = document.getElementById('conteudoModalEvento');

const botaoFecharModal = document.getElementById('fecharModal');
const fundoModal = document.getElementById('fecharModalFundo');


// Formatar datas

function formatarData(data) {

    const partes = data.split('-');

    return `${partes[2]}/${partes[1]}/${partes[0]}`;
}


// ========================================
// CALENDÁRIO INDIVIDUAL E EVENTOS DO MESMO LOCAL
// ========================================

let semanaCalendario = 0;
let atualizarCalendarioAtual = null;


// Símbolo de cada categoria

function simboloCategoria(categoria) {
    const chave = normalizarCategoria(categoria);
    const simbolos = { musica: 'musica', teatro: 'teatro', danca: 'danca' };
    return iconeSprite(simbolos[chave] || 'lista');
}


// Cores das categorias

function corDaCategoria(categoria) {

    const chave = normalizarCategoria(categoria);

    return chave && categorias[chave]
        ? categorias[chave].cor
        : '#8425c7';
}


// Criar calendário

function criarCalendario(
    sessoes,
    categoria,
    idEventoAtual,
    tituloEventoAtual,
    eventosMesmoLocal
) {

    if (!sessoes || sessoes.length === 0) {
        return '<p>Não há datas cadastradas.</p>';
    }

    const datasEvento = sessoes.map(sessao => sessao.data_evento);

    const primeiraData = datasEvento.slice().sort()[0];

    const partes = primeiraData.split('-');

    const dataInicial = new Date(
        Number(partes[0]),
        Number(partes[1]) - 1,
        Number(partes[2])
    );

    // Encontrar a segunda-feira da semana inicial

    const diaSemana = dataInicial.getDay();

    dataInicial.setDate(
        dataInicial.getDate() - ((diaSemana + 6) % 7)
    );

    // Navegação entre semanas

    dataInicial.setDate(
        dataInicial.getDate() + (semanaCalendario * 7)
    );

    const nomesDias = [
        'DOM', 'SEG', 'TER', 'QUA',
        'QUI', 'SEX', 'SÁB'
    ];

    let diasHTML = '';

    for (let i = 0; i < 7; i++) {

        const data = new Date(dataInicial);

        data.setDate(dataInicial.getDate() + i);

        const ano = data.getFullYear();
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const dia = String(data.getDate()).padStart(2, '0');

        const dataFormatada = `${ano}-${mes}-${dia}`;

        // Verifica se o evento atual acontece neste dia

        const acontece = datasEvento.includes(dataFormatada);

        // Ícone do evento atual

        let icones = '';

        if (acontece) {

            icones += `
                <button
                    type="button"
                    class="icone-evento-calendario"
                    data-event-id="${idEventoAtual}"
                    title="${escaparHTML(tituloEventoAtual)}"
                    style="--cor-icone: ${corDaCategoria(categoria)}">

                    ${simboloCategoria(categoria)}

                </button>
            `;
        }

        // Outros eventos que acontecem neste mesmo local e dia

        const eventosDoDia = eventosMesmoLocal.filter(evento =>
            evento.data_evento === dataFormatada
        );

        // Evita repetir o mesmo evento no mesmo dia

        const eventosUnicos = [
            ...new Map(
                eventosDoDia.map(evento => [
                    evento.id_evento,
                    evento
                ])
            ).values()
        ];

        eventosUnicos.forEach(evento => {

            icones += `
                <button
                    type="button"
                    class="icone-evento-calendario"
                    data-event-id="${evento.id_evento}"
                    title="${escaparHTML(evento.titulo)}"
                    style="--cor-icone: ${corDaCategoria(evento.categoria)}">

                    ${simboloCategoria(evento.categoria)}

                </button>
            `;
        });

        // Define qual evento será aberto ao clicar no dia.

        const eventoPrincipalDoDia = acontece
            ? {
                id_evento: idEventoAtual,
                titulo: tituloEventoAtual,
                categoria: categoria
            }
            : eventosUnicos[0];

        const temEventoNoDia = Boolean(eventoPrincipalDoDia);

        const corDoDia = temEventoNoDia
            ? corDaCategoria(eventoPrincipalDoDia.categoria)
            : '';

        diasHTML += `
            <div
                class="dia-calendario ${temEventoNoDia ? 'dia-evento dia-clicavel' : ''} ${acontece ? 'dia-evento-atual' : ''}"
                ${temEventoNoDia ? `style="--cor-evento: ${corDoDia}; background-color: ${corDoDia}; opacity: 1;"` : ''}
                ${temEventoNoDia ? `data-event-id="${eventoPrincipalDoDia.id_evento}"` : ''}
                ${temEventoNoDia ? `role="button" tabindex="0" title="Abrir ${escaparHTML(eventoPrincipalDoDia.titulo)}"` : ''}>

                <strong>${dia}</strong>

                <span>${nomesDias[data.getDay()]}</span>

                <div class="icones-dia">
                    ${icones}
                </div>

            </div>
        `;

    }

    return `
        <div class="calendario-evento">

            <button
                type="button"
                class="navegar-calendario"
                data-direcao="-1"
                aria-label="Semana anterior">
                &#10094;
            </button>

            <div class="dias-calendario">
                ${diasHTML}
            </div>

            <button
                type="button"
                class="navegar-calendario"
                data-direcao="1"
                aria-label="Próxima semana">
                &#10095;
            </button>

        </div>
    `;
}


// Navegação entre semanas

function navegarCalendario(direcao) {

    semanaCalendario += Number(direcao);

    if (typeof atualizarCalendarioAtual === 'function') {
        atualizarCalendarioAtual();
    }
}

// ========================================
// 15. BOTÕES PERSONALIZADOS DE CONTATO
// ========================================

function criarBotoesContato(links) {

    if (!Array.isArray(links) || links.length === 0) {
        return '<p>Contato não informado.</p>';
    }

    const configuracao = {
        whatsapp: { nome: 'WhatsApp', classe: 'botao-contato-whatsapp', icone: iconeSprite('whatsapp') },
        instagram: { nome: 'Instagram', classe: 'botao-contato-instagram', icone: iconeSprite('instagram') },
        sympla: { nome: 'Sympla', classe: 'botao-contato-sympla', icone: iconeSprite('ingresso') }
    };
    const botoes = links.map(tipo => {

        const item = configuracao[tipo];

        if (!item) return '';

        return `
            <button
                type="button"
                class="botao-contato ${item.classe}"
                data-tipo="${tipo}">

                ${item.icone}

                <span>${item.nome}</span>

            </button>
        `;

    }).join('');

    if (!botoes) {
        return '<p>Contato não informado.</p>';
    }

    return `
        <div class="botoes-contato">
            ${botoes}
        </div>

        <p class="mensagem-contato" aria-live="polite">
            Selecione um canal de contato.
        </p>
    `;
}


// ========================================
// 16. ABRIR MODAL E BUSCAR OS DADOS
// ========================================

async function abrirModalEvento(id) {

    modalEvento.classList.add('aberto');

    document.body.style.overflow = 'hidden';

    conteudoModal.innerHTML = '<p>Carregando detalhes do evento...</p>';

    try {

        const resposta = await fetch(
            `api/evento.php?id=${encodeURIComponent(id)}`
        );

        if (!resposta.ok) {
            throw new Error('Não foi possível carregar o evento.');
        }

        const evento = await resposta.json();

        const sessoes = evento.sessoes || [];

        const eventosMesmoLocal = evento.eventos_mesmo_local || [];

        const imagens = obterImagensEvento(evento);

        const ingresso = Number(evento.gratuito)
            ? 'Entrada gratuita'
            : `R$ ${Number(evento.valor).toFixed(2).replace('.', ',')}`;

        semanaCalendario = 0;

        conteudoModal.innerHTML = `
            <div class="pagina-evento">

                <div
                    class="evento-capa"
                    style="--imagem-capa: url('${imagens.principal}')"
                >

                    <span class="evento-categoria">
                        ${escaparHTML(evento.categoria)}
                    </span>

                    <h1>${escaparHTML(evento.titulo)}</h1>

                    <button
                        type="button"
                        class="coracao-favorito-modal ${evento.favoritado ? 'favoritado' : ''}"
                        id="botaoFavoritoModal"
                        title="${evento.favoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos'}"
                        aria-label="${evento.favoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos'}"
                        aria-pressed="${evento.favoritado ? 'true' : 'false'}"
                        onclick="alternarFavoritoModal(${Number(evento.id_evento)})"
                    >${iconeSprite(evento.favoritado ? 'coracao-preenchido' : 'coracao')}</button>

                </div>

                <div class="evento-conteudo">

                    <!-- DATAS E HORÁRIOS -->
                    <section class="evento-sessoes">

                        <h2>Datas e horários</h2>

                        <div id="calendarioEvento"></div>

                    </section>

                    <!-- SOBRE O EVENTO -->
                    <section class="evento-descricao">
                        <h2>Sobre o evento</h2>
                        <p>${escaparHTML(evento.descricao)}</p>
                    </section>

                    <!-- INFORMAÇÕES DO EVENTO -->
                    <section class="evento-informacoes">

                        <h2>Informações do evento</h2>

                        <div class="informacao-item">
                            <strong>Local</strong>
                            <p>${escaparHTML(evento.local)}</p>
                            <p>${escaparHTML(evento.endereco)}</p>
                        </div>

                        <div class="informacao-item">
                            <strong>Classificação indicativa</strong>
                            <p>${escaparHTML(evento.classificacao_indicativa)}</p>
                        </div>

                        <div class="informacao-item">
                            <strong>Ingressos</strong>
                            <p>${ingresso}</p>

                            ${Number(evento.gratuito) === 0 ? `
                                <span class="ingressos-disponiveis-modal">
                                    ${iconeSprite('confirmar')} DISPONÍVEIS
                                </span>
                            ` : ''}
                        </div>

                        <div class="informacao-item informacao-sessoes">

                            <strong>Horários das sessões</strong>

                            ${sessoes.length > 0
                                ? sessoes.map(sessao => `
                                    <div class="horario-sessao-modal">

                                        <span class="data-sessao-modal">
                                            ${formatarData(sessao.data_evento)}
                                        </span>

                                        <p>
                                            ${escaparHTML(sessao.horario_inicio.substring(0, 5))}
                                            –
                                            ${escaparHTML(sessao.horario_fim.substring(0, 5))}
                                        </p>

                                    </div>
                                `).join('')
                                : '<p>Horários não informados.</p>'
                            }

                        </div>

                    </section>

                    <!-- CONTATO E INGRESSOS -->
                    <section class="evento-contato">

                        <h2>Contato e ingressos</h2>

                        ${criarBotoesContato(evento.links)}

                    </section>

                </div>
            </div>
        `;

        // Atualiza o calendário

        atualizarCalendarioAtual = function() {

            const calendario = document.getElementById('calendarioEvento');

            if (calendario) {

                calendario.innerHTML = criarCalendario(
                    sessoes,
                    evento.categoria,
                    evento.id_evento,
                    evento.titulo,
                    eventosMesmoLocal
                );

            }
        };

        atualizarCalendarioAtual();

    } catch (erro) {

        conteudoModal.innerHTML = `
            <p>Não foi possível carregar os detalhes do evento.</p>
        `;

        console.error(erro);
    }
}


// ========================================
// 17. CLIQUES NOS BOTÕES DE CONTATO
// ========================================

document.addEventListener('click', function(evento) {

    const botao = evento.target.closest('.botao-contato');

    if (!botao) return;

    const tipo = botao.dataset.tipo;

    const mensagem = conteudoModal.querySelector('.mensagem-contato');

    if (!mensagem) return;

    const nomes = {
        whatsapp: 'WhatsApp',
        instagram: 'Instagram',
        sympla: 'Sympla'
    };

    mensagem.textContent =
        `O endereço do ${nomes[tipo]} ainda não foi cadastrado.`;

});


// ========================================
// 18. FECHAR MODAL
// ========================================

function fecharModalEvento() {

    modalEvento.classList.remove('aberto');

    document.body.style.overflow = '';
}


// Fechar pelo botão

botaoFecharModal.addEventListener(
    'click',
    fecharModalEvento
);


// Fechar clicando no fundo

fundoModal.addEventListener(
    'click',
    fecharModalEvento
);


// Fechar pressionando ESC

document.addEventListener('keydown', function(evento) {

    if (evento.key === 'Escape') {
        fecharModalEvento();
    }
});


// ========================================
// 19. DETECTAR CLIQUE EM VER DETALHES
// ========================================

document.addEventListener('click', function(evento) {

    const botao = evento.target.closest('.botao-detalhes');

    if (!botao) return;

    evento.preventDefault();

    const url = new URL(botao.href);

    const id = url.searchParams.get('id');

    if (id) {
        abrirModalEvento(id);
    }
});


// ========================================
// 20. CONTROLE DAS SETAS DO CALENDÁRIO
// ========================================

document.addEventListener('click', function(evento) {

    const botao = evento.target.closest('.navegar-calendario');

    if (!botao) return;

    navegarCalendario(botao.dataset.direcao);

});


// ========================================
// 21. CLIQUES NO CALENDÁRIO
// ========================================

document.addEventListener('click', function(evento) {

    // Prioriza o ícone: ele abre exatamente o evento escolhido.

    const icone = evento.target.closest('.icone-evento-calendario');

    if (icone) {

        evento.stopPropagation();

        const idEvento = icone.dataset.eventId;

        if (idEvento) {
            abrirModalEvento(idEvento);
        }

        return;
    }

    // Clicar em qualquer outra área do dia abre o evento principal.

    const dia = evento.target.closest('.dia-clicavel');

    if (dia) {

        const idEvento = dia.dataset.eventId;

        if (idEvento) {
            abrirModalEvento(idEvento);
        }
    }

});


// Permite abrir o dia usando Enter ou Espaço.

document.addEventListener('keydown', function(evento) {

    if (evento.key !== 'Enter' && evento.key !== ' ') return;

    const dia = evento.target.closest('.dia-clicavel');

    if (!dia) return;

    evento.preventDefault();

    const idEvento = dia.dataset.eventId;

    if (idEvento) {
        abrirModalEvento(idEvento);
    }

});

// ========================================
// FAVORITAR EVENTO PELO MODAL DO MAPA
// ========================================

async function alternarFavoritoModal(idEvento) {

    const botao = document.getElementById('botaoFavoritoModal');

    if (!botao) return;

    botao.disabled = true;

    try {

        const dados = new URLSearchParams();
        dados.append('id_evento', idEvento);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const resposta = await fetch('api/favorito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': csrfToken
            },
            body: dados
        });

        const resultado = await resposta.json();

        if (!resposta.ok || !resultado.sucesso) {

            if (resposta.status === 401) {
                alert('Você precisa entrar na sua conta para favoritar eventos.');
                window.location.href = 'index.php?pagina=login';
                return;
            }

            throw new Error(resultado.mensagem || 'Não foi possível atualizar o favorito.');
        }

        // Atualiza a aparência do coração

        botao.classList.toggle('favoritado', resultado.favoritado);
        botao.innerHTML = iconeSprite(resultado.favoritado ? 'coracao-preenchido' : 'coracao');

        botao.title = resultado.favoritado
            ? 'Remover dos favoritos'
            : 'Adicionar aos favoritos';

        botao.setAttribute('aria-pressed', resultado.favoritado ? 'true' : 'false');

        botao.setAttribute(
            'aria-label',
            botao.title
        );

    } catch (erro) {

        console.error(erro);
        alert('Não foi possível atualizar o favorito.');

    } finally {

        botao.disabled = false;

    }
}
