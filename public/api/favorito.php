<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Faça login para favoritar eventos.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$token = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '');
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(403);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'A sessão expirou. Atualize a página e tente novamente.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
if (!$idEvento || $idEvento <= 0) {
    http_response_code(400);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Evento inválido.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../../app/DAO/FavoritoDAO.php';

try {
    $idUsuario = (int) ($_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_usuario'] ?? 0);
    if ($idUsuario <= 0) throw new RuntimeException('Usuário inválido.');

    $favoritoDAO = new FavoritoDAO();
    $favoritoDAO->importarSessao($idUsuario, $_SESSION['favoritos'] ?? []);
    unset($_SESSION['favoritos']);
    $favoritado = $favoritoDAO->alternar($idUsuario, (int) $idEvento);

    echo json_encode([
        'sucesso' => true,
        'favoritado' => $favoritado
    ], JSON_UNESCAPED_UNICODE);
} catch (DomainException $e) {
    http_response_code(404);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Não foi possível atualizar o favorito.'
    ], JSON_UNESCAPED_UNICODE);
}
