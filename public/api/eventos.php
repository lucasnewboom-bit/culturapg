
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../app/DAO/MapaDAO.php';
require_once __DIR__ . '/../../app/DAO/FavoritoDAO.php';

header('Content-Type: application/json; charset=utf-8');

try {

    $categoria = $_GET['categoria'] ?? 'todos';
    $periodo = $_GET['periodo'] ?? 'todos';

    $categoriasPermitidas = [
        'todos',
        'musica',
        'teatro',
        'danca'
    ];

    $periodosPermitidos = [
        'todos',
        'semana',
        'mes'
    ];

    if (!in_array($categoria, $categoriasPermitidas, true)) {
        $categoria = 'todos';
    }

    if (!in_array($periodo, $periodosPermitidos, true)) {
        $periodo = 'todos';
    }

    $mapaDAO = new MapaDAO();

    $eventos = $mapaDAO->listarEventos(
        $categoria,
        $periodo
    );

    // ============================================
    // IDENTIFICAR EVENTOS FAVORITADOS
    // ============================================

    $favoritos = [];
    if (isset($_SESSION['usuario']['id'])) {
        $favoritoDAO = new FavoritoDAO();
        $idUsuario = (int) $_SESSION['usuario']['id'];
        $favoritoDAO->importarSessao($idUsuario, $_SESSION['favoritos'] ?? []);
        unset($_SESSION['favoritos']);
        $favoritos = $favoritoDAO->listarIds($idUsuario);
    }

    foreach ($eventos as &$evento) {

        $evento['favoritado'] = in_array(
            (int) $evento['id_evento'],
            $favoritos,
            true
        );

    }

    unset($evento);

    echo json_encode(
        $eventos,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'erro' => 'Não foi possível carregar os eventos.'
    ], JSON_UNESCAPED_UNICODE);

}
