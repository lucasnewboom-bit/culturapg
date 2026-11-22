<?php

function iconeCultura(string $nome, string $classe = ''): string
{
    static $permitidos = [
        'mapa', 'calendario', 'calendario-adicionar', 'usuario', 'usuario-adicionar',
        'notificacao', 'sair', 'entrar', 'pesquisa', 'filtro', 'coracao',
        'coracao-preenchido', 'musica', 'teatro', 'danca', 'pin', 'relogio',
        'ingresso', 'compartilhar', 'editar', 'imagem', 'adicionar', 'excluir',
        'link-externo', 'fechar', 'voltar', 'avancar', 'chevron-baixo',
        'confirmar', 'expandir', 'link', 'lista', 'whatsapp', 'instagram'
    ];

    if (!in_array($nome, $permitidos, true)) {
        return '';
    }

    $classes = 'icone-cultura' . ($classe !== '' ? ' ' . htmlspecialchars($classe, ENT_QUOTES, 'UTF-8') : '');
    return '<svg class="' . $classes . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><use href="img/icones.svg#' . $nome . '"></use></svg>';
}
