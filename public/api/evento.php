
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../app/DAO/EventoDAO.php';
require_once __DIR__ . '/../../app/DAO/FavoritoDAO.php';
require_once __DIR__ . '/../../app/DAO/HistoricoDAO.php';

header('Content-Type: application/json; charset=utf-8');

try {

    // Recebe o ID do evento
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        http_response_code(400);

        echo json_encode([
            'erro' => 'ID do evento inválido.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // Busca o evento
    $eventoDAO = new EventoDAO();
    $evento = $eventoDAO->buscarPorId($id);

    if (!$evento) {
        http_response_code(404);

        echo json_encode([
            'erro' => 'Evento não encontrado.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // ============================================
    // REGISTRAR ÚLTIMOS EVENTOS VISUALIZADOS
    // ============================================

    if (isset($_SESSION['usuario'])) {
        $idUsuario = (int) ($_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_usuario'] ?? 0);
        $historicoDAO = new HistoricoDAO();
        $historicoDAO->importarSessao($idUsuario, $_SESSION['historico_eventos'] ?? []);
        unset($_SESSION['historico_eventos']);
        $historicoDAO->registrarVisualizacao($idUsuario, (int) $evento['id_evento']);
    }

    // ============================================
    // VERIFICAR SE O EVENTO ESTÁ FAVORITADO
    // ============================================

    $evento['favoritado'] = false;
    if (isset($_SESSION['usuario']['id'])) {
        $favoritoDAO = new FavoritoDAO();
        $idUsuario = (int) $_SESSION['usuario']['id'];
        $favoritoDAO->importarSessao($idUsuario, $_SESSION['favoritos'] ?? []);
        unset($_SESSION['favoritos']);
        $evento['favoritado'] = $favoritoDAO->ehFavorito(
            $idUsuario,
            (int) $evento['id_evento']
        );
    }

    // ============================================
    // BUSCAR EVENTOS DO MESMO LOCAL
    // ============================================

    $eventosMesmoLocal = $eventoDAO->buscarEventosMesmoLocal(
        (int) $evento['id_local'],
        (int) $evento['id_evento']
    );

    $evento['eventos_mesmo_local'] = $eventosMesmoLocal;

    // ============================================
    // RETORNAR DADOS
    // ============================================

    echo json_encode(
        $evento,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'erro' => 'Não foi possível carregar os detalhes do evento.'
    ], JSON_UNESCAPED_UNICODE);

}
