<?php

require_once __DIR__ . '/../Database/Conexao.php';

class AtualizacaoEventoDAO
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function listarDoEvento(int $idEvento, int $idUsuario): array
    {
        $sql = '
            SELECT a.id_atualizacao, a.titulo, a.mensagem, a.data_atualizacao
            FROM ATUALIZACAO_EVENTO a
            INNER JOIN EVENTO e ON e.id_evento = a.evento_id
            INNER JOIN ORGANIZADOR o ON o.id_organizador = e.organizador_id
            WHERE e.id_evento = :evento AND o.usuario_id = :usuario
            ORDER BY a.data_atualizacao DESC, a.id_atualizacao DESC
        ';
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':evento' => $idEvento, ':usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Salva a atualização e as notificações dos usuários que favoritaram o evento na mesma transação. */
    public function publicar(int $idEvento, int $idUsuario, string $titulo, string $mensagem): int
    {
        try {
            $this->conexao->beginTransaction();

            $stmt = $this->conexao->prepare(
                "SELECT e.id_evento FROM EVENTO e
                 INNER JOIN ORGANIZADOR o ON o.id_organizador = e.organizador_id
                 WHERE e.id_evento = :evento AND o.usuario_id = :usuario AND e.status = 'ativo'
                 FOR UPDATE"
            );
            $stmt->execute([':evento' => $idEvento, ':usuario' => $idUsuario]);
            if (!$stmt->fetchColumn()) {
                throw new DomainException('Evento não encontrado, inativo ou sem permissão para publicar atualizações.');
            }

            $stmt = $this->conexao->prepare(
                'INSERT INTO ATUALIZACAO_EVENTO (evento_id, titulo, mensagem) VALUES (:evento, :titulo, :mensagem)'
            );
            $stmt->execute([':evento' => $idEvento, ':titulo' => $titulo, ':mensagem' => $mensagem]);
            $idAtualizacao = (int) $this->conexao->lastInsertId();

            $stmt = $this->conexao->prepare(
                "INSERT INTO NOTIFICACAO (usuario_id, atualizacao_id)
                 SELECT DISTINCT f.usuario_id, :atualizacao
                 FROM FAVORITO f
                 INNER JOIN USUARIO u ON u.id_usuario = f.usuario_id AND u.status = 'ativo'
                 WHERE f.evento_id = :evento AND f.usuario_id <> :organizador"
            );
            $stmt->execute([
                ':atualizacao' => $idAtualizacao,
                ':evento' => $idEvento,
                ':organizador' => $idUsuario
            ]);

            $this->conexao->commit();
            return $stmt->rowCount();
        } catch (Throwable $e) {
            if ($this->conexao->inTransaction()) $this->conexao->rollBack();
            throw $e;
        }
    }
}
