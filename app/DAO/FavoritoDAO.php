<?php

require_once __DIR__ . '/../Database/Conexao.php';

class FavoritoDAO
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function listarIds(int $idUsuario): array
    {
        $stmt = $this->conexao->prepare(
            'SELECT evento_id FROM FAVORITO WHERE usuario_id = :usuario'
        );
        $stmt->execute([':usuario' => $idUsuario]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function ehFavorito(int $idUsuario, int $idEvento): bool
    {
        $stmt = $this->conexao->prepare(
            'SELECT 1 FROM FAVORITO WHERE usuario_id = :usuario AND evento_id = :evento LIMIT 1'
        );
        $stmt->execute([':usuario' => $idUsuario, ':evento' => $idEvento]);
        return (bool) $stmt->fetchColumn();
    }

    /** Importa favoritos que existiam na sessão antes da migração. */
    public function importarSessao(int $idUsuario, array $idsEvento): void
    {
        if (!$idsEvento) return;

        $this->conexao->beginTransaction();
        try {
            $stmt = $this->conexao->prepare(
                "INSERT IGNORE INTO FAVORITO (usuario_id, evento_id)
                 SELECT :usuario, e.id_evento FROM EVENTO e
                 WHERE e.id_evento = :evento AND e.status = 'ativo'"
            );
            foreach (array_unique(array_map('intval', $idsEvento)) as $idEvento) {
                if ($idEvento > 0) {
                    $stmt->execute([':usuario' => $idUsuario, ':evento' => $idEvento]);
                }
            }
            $this->conexao->commit();
        } catch (Throwable $e) {
            if ($this->conexao->inTransaction()) $this->conexao->rollBack();
            throw $e;
        }
    }

    /** Retorna o novo estado e usa o índice único da tabela para evitar duplicatas. */
    public function alternar(int $idUsuario, int $idEvento): bool
    {
        $stmt = $this->conexao->prepare(
            'SELECT status FROM EVENTO WHERE id_evento = :evento LIMIT 1'
        );
        $stmt->execute([':evento' => $idEvento]);
        $statusEvento = $stmt->fetchColumn();
        if ($statusEvento === false) {
            throw new DomainException('Evento não encontrado ou indisponível.');
        }

        $stmt = $this->conexao->prepare(
            'DELETE FROM FAVORITO WHERE usuario_id = :usuario AND evento_id = :evento'
        );
        $stmt->execute([':usuario' => $idUsuario, ':evento' => $idEvento]);
        if ($stmt->rowCount() > 0) return false;

        if ($statusEvento !== 'ativo') {
            throw new DomainException('Este evento não está mais disponível para novos favoritos.');
        }

        $stmt = $this->conexao->prepare(
            "INSERT IGNORE INTO FAVORITO (usuario_id, evento_id)
             SELECT :usuario, e.id_evento FROM EVENTO e
             WHERE e.id_evento = :evento AND e.status = 'ativo'"
        );
        $stmt->execute([':usuario' => $idUsuario, ':evento' => $idEvento]);
        return $this->ehFavorito($idUsuario, $idEvento);
    }
}
