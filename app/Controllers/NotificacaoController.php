<?php

require_once __DIR__ . '/../DAO/NotificacaoDAO.php';

class NotificacaoController
{
    private NotificacaoDAO $notificacaoDAO;

    public function __construct()
    {
        $this->notificacaoDAO = new NotificacaoDAO();
    }

    public function listar(int $idUsuario): array
    {
        return $this->notificacaoDAO->listarDoUsuario($idUsuario);
    }

    public function contarNaoLidas(int $idUsuario): int
    {
        return $this->notificacaoDAO->contarNaoLidas($idUsuario);
    }

    public function marcarComoLida(int $idNotificacao, int $idUsuario): bool
    {
        return $this->notificacaoDAO->marcarComoLida($idNotificacao, $idUsuario);
    }
}
