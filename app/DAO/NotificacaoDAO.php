<?php

require_once __DIR__ . '/../Database/Conexao.php';

class NotificacaoDAO
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function listarDoUsuario(int $idUsuario): array
    {
        $sql = '
            SELECT n.id_notificacao, n.lida, n.data_envio,
                   a.titulo AS titulo_atualizacao,
                   a.mensagem,
                   a.data_atualizacao,
                   e.id_evento,
                   e.titulo AS titulo_evento
            FROM NOTIFICACAO n
            INNER JOIN ATUALIZACAO_EVENTO a ON a.id_atualizacao = n.atualizacao_id
            INNER JOIN EVENTO e ON e.id_evento = a.evento_id
            WHERE n.usuario_id = :usuario
            ORDER BY n.data_envio DESC, n.id_notificacao DESC
        ';
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarNaoLidas(int $idUsuario): int
    {
        $stmt = $this->conexao->prepare(
            'SELECT COUNT(*) FROM NOTIFICACAO WHERE usuario_id = :usuario AND lida = 0'
        );
        $stmt->execute([':usuario' => $idUsuario]);
        return (int) $stmt->fetchColumn();
    }

    public function marcarComoLida(int $idNotificacao, int $idUsuario): bool
    {
        $stmt = $this->conexao->prepare(
            'UPDATE NOTIFICACAO SET lida = 1 WHERE id_notificacao = :notificacao AND usuario_id = :usuario'
        );
        $stmt->execute([':notificacao' => $idNotificacao, ':usuario' => $idUsuario]);

        // Uma notificação já lida também conta como sucesso se pertencer ao usuário.
        if ($stmt->rowCount() > 0) return true;
        $stmt = $this->conexao->prepare(
            'SELECT 1 FROM NOTIFICACAO WHERE id_notificacao = :notificacao AND usuario_id = :usuario LIMIT 1'
        );
        $stmt->execute([':notificacao' => $idNotificacao, ':usuario' => $idUsuario]);
        return (bool) $stmt->fetchColumn();
    }
}
