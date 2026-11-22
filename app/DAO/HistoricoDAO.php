<?php

require_once __DIR__ . '/../Database/Conexao.php';

class HistoricoDAO
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function registrarVisualizacao(int $idUsuario, int $idEvento): bool
    {
        $stmt = $this->conexao->prepare(
            'INSERT INTO HISTORICO (usuario_id, evento_id)
             SELECT :usuario, e.id_evento FROM EVENTO e WHERE e.id_evento = :evento'
        );
        $stmt->execute([':usuario' => $idUsuario, ':evento' => $idEvento]);
        return $stmt->rowCount() > 0;
    }

    /** Importa os últimos acessos que ainda estavam na sessão durante a migração. */
    public function importarSessao(int $idUsuario, array $historico): void
    {
        if (!$historico) return;

        $this->conexao->beginTransaction();
        try {
            $stmt = $this->conexao->prepare(
                'INSERT INTO HISTORICO (usuario_id, evento_id, data_visualizacao)
                 SELECT :usuario, e.id_evento, :visualizacao FROM EVENTO e WHERE e.id_evento = :evento'
            );

            foreach (array_reverse($historico) as $item) {
                $idEvento = filter_var($item['id_evento'] ?? null, FILTER_VALIDATE_INT);
                if (!$idEvento || $idEvento <= 0) continue;

                $data = (string) ($item['data_visualizacao'] ?? '');
                $momento = strtotime($data);
                $dataValida = $momento ? date('Y-m-d H:i:s', $momento) : date('Y-m-d H:i:s');
                $stmt->execute([
                    ':usuario' => $idUsuario,
                    ':evento' => (int) $idEvento,
                    ':visualizacao' => $dataValida
                ]);
            }
            $this->conexao->commit();
        } catch (Throwable $e) {
            if ($this->conexao->inTransaction()) $this->conexao->rollBack();
            throw $e;
        }
    }

    public function listarRecentes(int $idUsuario): array
    {
        $sql = '
            SELECT e.id_evento, e.titulo, e.imagem, c.nome AS categoria, recentes.data_visualizacao
            FROM (
                SELECT h.usuario_id, h.evento_id, h.data_visualizacao,
                       ROW_NUMBER() OVER (
                           PARTITION BY h.evento_id
                           ORDER BY h.data_visualizacao DESC, h.id_historico DESC
                       ) AS posicao
                FROM HISTORICO h
                WHERE h.usuario_id = :usuario
            ) recentes
            INNER JOIN EVENTO e ON e.id_evento = recentes.evento_id
            INNER JOIN CATEGORIA c ON c.id_categoria = e.categoria_id
            WHERE recentes.posicao = 1
            ORDER BY recentes.data_visualizacao DESC
            LIMIT 3
        ';
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
