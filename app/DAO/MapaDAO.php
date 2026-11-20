
<?php

require_once __DIR__ . '/../Database/Conexao.php';

class MapaDAO {

    private PDO $conexao;

    public function __construct() {
        $this->conexao = Conexao::conectar();
    }

    // ============================================
    // LISTAR EVENTOS COM LOCALIZAÇÃO E SESSÕES
    // ============================================

    public function listarEventos(
        string $categoria = 'todos',
        string $periodo = 'todos'
    ): array {

        $sql = "
            SELECT
                e.id_evento,
                e.titulo,
                e.descricao,
                e.valor,
                e.gratuito,

                c.nome AS categoria,

                l.nome AS local,
                l.latitude,
                l.longitude,

                (
                    SELECT GROUP_CONCAT(
                        CONCAT(
                            DATE_FORMAT(d.data_evento, '%d/%m/%Y'),
                            ' às ',
                            TIME_FORMAT(d.horario_inicio, '%H:%i'),
                            ' - ',
                            TIME_FORMAT(d.horario_fim, '%H:%i')
                        )
                        ORDER BY d.data_evento, d.horario_inicio
                        SEPARATOR '||'
                    )
                    FROM DATA_EVENTO d
                    WHERE d.evento_id = e.id_evento
                ) AS sessoes

            FROM EVENTO e

            INNER JOIN CATEGORIA c
                ON e.categoria_id = c.id_categoria

            INNER JOIN LOCAL l
                ON e.local_id = l.id_local

            WHERE l.latitude IS NOT NULL
              AND l.longitude IS NOT NULL
        ";

        $parametros = [];

        // ========================================
        // FILTRO DE CATEGORIA
        // ========================================

        $categoriasPermitidas = [
            'musica' => 'Música',
            'teatro' => 'Teatro',
            'danca'  => 'Dança'
        ];

        if (isset($categoriasPermitidas[$categoria])) {

            $sql .= " AND c.nome = :categoria";

            $parametros[':categoria'] =
                $categoriasPermitidas[$categoria];
        }

        // ========================================
        // FILTRO DE PERÍODO
        // ========================================

        if ($periodo === 'semana' || $periodo === 'mes') {

            $hoje = new DateTimeImmutable('today');

            if ($periodo === 'semana') {

                // Segunda-feira até domingo
                $inicio = $hoje->modify('monday this week');

                $fim = $inicio->modify('+6 days');

            } else {

                // Primeiro até último dia do mês
                $inicio = $hoje->modify('first day of this month');

                $fim = $hoje->modify('last day of this month');
            }

            $sql .= "
                AND EXISTS (
                    SELECT 1
                    FROM DATA_EVENTO d
                    WHERE d.evento_id = e.id_evento
                      AND d.data_evento BETWEEN :inicio AND :fim
                )
            ";

            $parametros[':inicio'] =
                $inicio->format('Y-m-d');

            $parametros[':fim'] =
                $fim->format('Y-m-d');
        }

        // ========================================
        // EXECUÇÃO DA CONSULTA
        // ========================================

        $sql .= " ORDER BY e.titulo";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
