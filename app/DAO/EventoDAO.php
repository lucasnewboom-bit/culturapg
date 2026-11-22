
<?php

require_once __DIR__ . '/../Database/Conexao.php';

class EventoDAO {

    private PDO $conexao;

    public function __construct() {
        $this->conexao = Conexao::conectar();
    }


    // ============================================
    // BUSCAR EVENTO PELO ID
    // ============================================

    public function buscarPorId(int $id): ?array {

        $sql = "
            SELECT
                e.id_evento,
                e.titulo,
                e.descricao,
                e.valor,
                e.gratuito,
                e.tipo_contato,
                e.link_contato,
                e.classificacao_indicativa,
                e.imagem,

                c.nome AS categoria,

                l.id_local,
                l.nome AS local,
                l.endereco,
                l.latitude,
                l.longitude

            FROM EVENTO e

            INNER JOIN CATEGORIA c
                ON e.categoria_id = c.id_categoria

            INNER JOIN LOCAL l
                ON e.local_id = l.id_local

            WHERE e.id_evento = :id
              AND e.status = 'ativo'

            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $evento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$evento) {
            return null;
        }


        // ========================================
        // BUSCAR SESSÕES DO EVENTO
        // ========================================

        $sqlSessoes = "
            SELECT
                data_evento,
                horario_inicio,
                horario_fim

            FROM DATA_EVENTO

            WHERE evento_id = :id

            ORDER BY data_evento, horario_inicio
        ";

        $stmtSessoes = $this->conexao->prepare($sqlSessoes);

        $stmtSessoes->execute([
            ':id' => $id
        ]);

        $evento['sessoes'] = $stmtSessoes->fetchAll(
            PDO::FETCH_ASSOC
        );


        // ========================================
        // BUSCAR TIPOS DE CONTATO
        // MANTÉM COMPATIBILIDADE COM O MAPA
        // ========================================

        $sqlLinks = "
            SELECT
                tipo_link

            FROM LINK_EVENTO

            WHERE evento_id = :id

            ORDER BY id_link
        ";

        $stmtLinks = $this->conexao->prepare($sqlLinks);

        $stmtLinks->execute([
            ':id' => $id
        ]);

        $evento['links'] = $stmtLinks->fetchAll(
            PDO::FETCH_COLUMN
        );


        // ========================================
        // BUSCAR LINKS COMPLETOS
        // PARA A PÁGINA DE DETALHES
        // ========================================

        $sqlLinksCompletos = "
            SELECT
                tipo_link,
                url

            FROM LINK_EVENTO

            WHERE evento_id = :id

            ORDER BY id_link
        ";

        $stmtLinksCompletos = $this->conexao->prepare(
            $sqlLinksCompletos
        );

        $stmtLinksCompletos->execute([
            ':id' => $id
        ]);

        $evento['links_completos'] = $stmtLinksCompletos->fetchAll(
            PDO::FETCH_ASSOC
        );

        return $evento;
    }


    // ============================================
    // BUSCAR OUTROS EVENTOS DO MESMO LOCAL
    // ============================================

    public function buscarEventosMesmoLocal(
        int $idLocal,
        int $idEventoAtual
    ): array {

        $sql = "
            SELECT
                e.id_evento,
                e.titulo,
                c.nome AS categoria,
                d.data_evento

            FROM EVENTO e

            INNER JOIN CATEGORIA c
                ON e.categoria_id = c.id_categoria

            LEFT JOIN DATA_EVENTO d
                ON d.evento_id = e.id_evento

            WHERE e.local_id = :idLocal
              AND e.id_evento <> :idEventoAtual
              AND e.status = 'ativo'

            ORDER BY d.data_evento, e.titulo
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':idLocal' => $idLocal,
            ':idEventoAtual' => $idEventoAtual
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================
    // LISTAR TODOS OS EVENTOS DO CATÁLOGO
    // ============================================

    public function listarTodos(): array {

        $sql = "
            SELECT
                e.id_evento,
                e.titulo,
                e.descricao,
                e.valor,
                e.gratuito,
                e.imagem,

                c.nome AS categoria,

                l.nome AS local,
                l.endereco,

                (
                    SELECT MIN(d.data_evento)
                    FROM DATA_EVENTO d
                    WHERE d.evento_id = e.id_evento
                      AND d.data_evento >= CURDATE()
                ) AS proxima_data

            FROM EVENTO e

            INNER JOIN CATEGORIA c
                ON e.categoria_id = c.id_categoria

            INNER JOIN LOCAL l
                ON e.local_id = l.id_local

            WHERE e.status = 'ativo'

            ORDER BY proxima_data IS NULL, proxima_data, e.titulo
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ============================================
    // LISTAR CATEGORIAS ATIVAS
    // ============================================

    public function listarCategorias(): array {

        $sql = "
            SELECT id_categoria, nome
            FROM CATEGORIA
            WHERE ativo = 1
            ORDER BY nome
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function possuiOrganizador(int $idUsuario): bool {
        $stmt = $this->conexao->prepare(
            "SELECT 1 FROM ORGANIZADOR WHERE usuario_id = :usuario AND status = 'ativo' LIMIT 1"
        );
        $stmt->execute([':usuario' => $idUsuario]);
        return (bool) $stmt->fetchColumn();
    }

    public function listarDoUsuario(int $idUsuario): array {
        $sql = "
            SELECT e.id_evento, e.titulo, e.imagem, e.status, c.nome AS categoria,
                   l.nome AS local,
                   (SELECT MIN(d.data_evento) FROM DATA_EVENTO d
                    WHERE d.evento_id = e.id_evento AND d.data_evento >= CURDATE()) AS proxima_data
            FROM EVENTO e
            INNER JOIN ORGANIZADOR o ON o.id_organizador = e.organizador_id
            INNER JOIN CATEGORIA c ON c.id_categoria = e.categoria_id
            INNER JOIN LOCAL l ON l.id_local = e.local_id
            WHERE o.usuario_id = :usuario
            ORDER BY e.status = 'ativo' DESC, proxima_data IS NULL, proxima_data, e.titulo
        ";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarDoUsuario(int $idEvento, int $idUsuario): ?array {
        $sql = "
            SELECT e.id_evento, e.organizador_id, e.categoria_id, e.local_id, e.imagem,
                   e.titulo, e.descricao, e.valor, e.gratuito, e.tipo_contato,
                   e.link_contato, e.classificacao_indicativa, e.status,
                   l.nome AS local_nome, l.endereco, l.latitude, l.longitude
            FROM EVENTO e
            INNER JOIN ORGANIZADOR o ON o.id_organizador = e.organizador_id
            INNER JOIN LOCAL l ON l.id_local = e.local_id
            WHERE e.id_evento = :evento AND o.usuario_id = :usuario
            LIMIT 1
        ";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':evento' => $idEvento, ':usuario' => $idUsuario]);
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$evento) return null;

        $stmt = $this->conexao->prepare(
            'SELECT data_evento, horario_inicio, horario_fim FROM DATA_EVENTO WHERE evento_id = :evento ORDER BY data_evento, horario_inicio'
        );
        $stmt->execute([':evento' => $idEvento]);
        $evento['sessoes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->conexao->prepare(
            'SELECT tipo_link, url FROM LINK_EVENTO WHERE evento_id = :evento ORDER BY id_link'
        );
        $stmt->execute([':evento' => $idEvento]);
        $evento['links_completos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $evento;
    }

    private function obterOuCriarLocal(array $dados): int {
        // O projeto define Ponta Grossa como a cidade fixa de id 1.
        $stmt = $this->conexao->prepare(
            'SELECT id_cidade FROM CIDADE WHERE id_cidade = 1 LIMIT 1'
        );
        $stmt->execute();
        $cidadeId = $stmt->fetchColumn();
        if (!$cidadeId) throw new RuntimeException('A cidade fixa de Ponta Grossa (id 1) não existe.');

        $stmt = $this->conexao->prepare(
            'SELECT id_local FROM LOCAL WHERE cidade_id = :cidade AND nome = :nome AND endereco = :endereco LIMIT 1'
        );
        $stmt->execute([
            ':cidade' => $cidadeId,
            ':nome' => $dados['local_nome'],
            ':endereco' => $dados['endereco']
        ]);
        $idLocal = $stmt->fetchColumn();
        if ($idLocal) {
            if ($dados['latitude'] !== '' && $dados['longitude'] !== '') {
                $stmt = $this->conexao->prepare('UPDATE LOCAL SET latitude = :latitude, longitude = :longitude WHERE id_local = :local AND latitude IS NULL AND longitude IS NULL');
                $stmt->execute([':latitude' => $dados['latitude'], ':longitude' => $dados['longitude'], ':local' => $idLocal]);
            }
            return (int) $idLocal;
        }

        $stmt = $this->conexao->prepare(
            'INSERT INTO LOCAL (cidade_id, nome, endereco, latitude, longitude) VALUES (:cidade, :nome, :endereco, :latitude, :longitude)'
        );
        $stmt->execute([
            ':cidade' => $cidadeId,
            ':nome' => $dados['local_nome'],
            ':endereco' => $dados['endereco'],
            ':latitude' => $dados['latitude'] !== '' ? $dados['latitude'] : null,
            ':longitude' => $dados['longitude'] !== '' ? $dados['longitude'] : null
        ]);
        return (int) $this->conexao->lastInsertId();
    }

    public function atualizar(int $idEvento, int $idUsuario, array $dados): bool {
        try {
            $this->conexao->beginTransaction();
            $evento = $this->buscarDoUsuario($idEvento, $idUsuario);
            if (!$evento) throw new RuntimeException('Evento não encontrado ou sem permissão para editá-lo.');

            $localId = $this->obterOuCriarLocal($dados);
            $stmt = $this->conexao->prepare("SELECT id_categoria FROM CATEGORIA WHERE id_categoria = :categoria AND ativo = 1");
            $stmt->execute([':categoria' => $dados['categoria_id']]);
            if (!$stmt->fetchColumn()) throw new RuntimeException('Selecione uma categoria ativa.');

            $stmt = $this->conexao->prepare("UPDATE EVENTO SET categoria_id = :categoria, local_id = :local, titulo = :titulo,
                descricao = :descricao, valor = :valor, gratuito = :gratuito, tipo_contato = :tipo_contato,
                link_contato = :link_contato, classificacao_indicativa = :classificacao,
                imagem = COALESCE(:imagem, imagem)
                WHERE id_evento = :evento AND organizador_id = :organizador");
            $stmt->execute([
                ':categoria' => $dados['categoria_id'], ':local' => $localId, ':titulo' => $dados['titulo'],
                ':descricao' => $dados['descricao'] ?: null, ':valor' => $dados['gratuito'] ? 0 : $dados['valor'],
                ':gratuito' => $dados['gratuito'] ? 1 : 0, ':tipo_contato' => $dados['tipo_contato'] ?: null,
                ':link_contato' => $dados['link_contato'] ?: null, ':classificacao' => $dados['classificacao_indicativa'] ?: null,
                ':imagem' => $dados['imagem'] ?? null,
                ':evento' => $idEvento, ':organizador' => $evento['organizador_id']
            ]);

            $stmt = $this->conexao->prepare('DELETE FROM DATA_EVENTO WHERE evento_id = :evento');
            $stmt->execute([':evento' => $idEvento]);
            $stmtData = $this->conexao->prepare('INSERT INTO DATA_EVENTO (evento_id, data_evento, horario_inicio, horario_fim) VALUES (:evento, :data, :inicio, :fim)');
            foreach ($dados['sessoes'] as $sessao) {
                $stmtData->execute([':evento' => $idEvento, ':data' => $sessao['data_evento'], ':inicio' => $sessao['horario_inicio'], ':fim' => $sessao['horario_fim'] ?: null]);
            }

            $stmt = $this->conexao->prepare('DELETE FROM LINK_EVENTO WHERE evento_id = :evento');
            $stmt->execute([':evento' => $idEvento]);
            $stmtLink = $this->conexao->prepare('INSERT INTO LINK_EVENTO (evento_id, tipo_link, url) VALUES (:evento, :tipo, :url)');
            foreach ($dados['links'] as $link) {
                if ($link['url'] !== '') $stmtLink->execute([':evento' => $idEvento, ':tipo' => $link['tipo_link'], ':url' => $link['url']]);
            }

            $this->conexao->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->conexao->inTransaction()) $this->conexao->rollBack();
            throw $e;
        }
    }

    public function excluirLogicamente(int $idEvento, int $idUsuario): bool {
        $sql = "UPDATE EVENTO e INNER JOIN ORGANIZADOR o ON o.id_organizador = e.organizador_id
                SET e.status = 'inativo' WHERE e.id_evento = :evento AND o.usuario_id = :usuario AND e.status = 'ativo'";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':evento' => $idEvento, ':usuario' => $idUsuario]);
        return $stmt->rowCount() > 0;
    }


    // ============================================
    // CADASTRAR EVENTO
    // ============================================

    public function cadastrar(int $idUsuario, array $dados): int {

        try {

            $this->conexao->beginTransaction();

            // ----------------------------------------
            // 1. BUSCAR OU CRIAR ORGANIZADOR
            // ----------------------------------------

            $sqlOrganizador = "
                SELECT id_organizador, status
                FROM ORGANIZADOR
                WHERE usuario_id = :usuario
                LIMIT 1
            ";

            $stmt = $this->conexao->prepare($sqlOrganizador);

            $stmt->execute([
                ':usuario' => $idUsuario
            ]);

            $organizador = $stmt->fetch(PDO::FETCH_ASSOC);
            $organizadorId = $organizador['id_organizador'] ?? null;

            if ($organizador && $organizador['status'] !== 'ativo') {
                throw new RuntimeException('O cadastro de organizador desta conta está inativo.');
            }

            if (!$organizadorId) {

                if (empty($dados['tipo_organizador'])) {
                    throw new Exception("Informe o tipo de organizador.");
                }

                $sqlCriarOrganizador = "
                    INSERT INTO ORGANIZADOR (
                        usuario_id,
                        tipo_organizador
                    ) VALUES (
                        :usuario,
                        :tipo
                    )
                ";

                $stmt = $this->conexao->prepare($sqlCriarOrganizador);

                $stmt->execute([
                    ':usuario' => $idUsuario,
                    ':tipo' => $dados['tipo_organizador']
                ]);

                $organizadorId = $this->conexao->lastInsertId();
            }


            // ----------------------------------------
            // 2. BUSCAR CIDADE DE PONTA GROSSA
            // ----------------------------------------

            $sqlCidade = "SELECT id_cidade FROM CIDADE WHERE id_cidade = 1 LIMIT 1";

            $stmt = $this->conexao->prepare($sqlCidade);
            $stmt->execute();

            $cidadeId = $stmt->fetchColumn();

            if (!$cidadeId) {
                throw new Exception("Cidade de Ponta Grossa não encontrada.");
            }


            // ----------------------------------------
            // 3. BUSCAR OU CRIAR LOCAL
            // ----------------------------------------

            $sqlLocal = "
                SELECT id_local
                FROM LOCAL
                WHERE cidade_id = :cidade
                  AND nome = :nome
                  AND endereco = :endereco
                LIMIT 1
            ";

            $stmt = $this->conexao->prepare($sqlLocal);

            $stmt->execute([
                ':cidade' => $cidadeId,
                ':nome' => $dados['local_nome'],
                ':endereco' => $dados['endereco']
            ]);

            $localId = $stmt->fetchColumn();

            if ($localId && $dados['latitude'] !== '' && $dados['longitude'] !== '') {
                $stmt = $this->conexao->prepare('UPDATE LOCAL SET latitude = :latitude, longitude = :longitude WHERE id_local = :local AND latitude IS NULL AND longitude IS NULL');
                $stmt->execute([':latitude' => $dados['latitude'], ':longitude' => $dados['longitude'], ':local' => $localId]);
            }

            if (!$localId) {

                $sqlCriarLocal = "
                    INSERT INTO LOCAL (
                        cidade_id,
                        nome,
                        endereco,
                        latitude,
                        longitude
                    ) VALUES (
                        :cidade,
                        :nome,
                        :endereco,
                        :latitude,
                        :longitude
                    )
                ";

                $stmt = $this->conexao->prepare($sqlCriarLocal);

                $stmt->execute([
                    ':cidade' => $cidadeId,
                    ':nome' => $dados['local_nome'],
                    ':endereco' => $dados['endereco'],
                    ':latitude' => $dados['latitude'] !== '' ? $dados['latitude'] : null,
                    ':longitude' => $dados['longitude'] !== '' ? $dados['longitude'] : null
                ]);

                $localId = $this->conexao->lastInsertId();
            }


            // ----------------------------------------
            // 4. CADASTRAR EVENTO
            // ----------------------------------------

            $sqlEvento = "
                INSERT INTO EVENTO (
                    organizador_id,
                    categoria_id,
                    local_id,
                    titulo,
                    descricao,
                    valor,
                    gratuito,
                    tipo_contato,
                    link_contato,
                    classificacao_indicativa,
                    imagem
                ) VALUES (
                    :organizador,
                    :categoria,
                    :local,
                    :titulo,
                    :descricao,
                    :valor,
                    :gratuito,
                    :tipo_contato,
                    :link_contato,
                    :classificacao,
                    :imagem
                )
            ";

            $stmt = $this->conexao->prepare($sqlEvento);

            $stmt->execute([
                ':organizador' => $organizadorId,
                ':categoria' => $dados['categoria_id'],
                ':local' => $localId,
                ':titulo' => $dados['titulo'],
                ':descricao' => $dados['descricao'] ?: null,
                ':valor' => $dados['gratuito'] ? 0 : $dados['valor'],
                ':gratuito' => $dados['gratuito'] ? 1 : 0,
                ':tipo_contato' => $dados['tipo_contato'],
                ':link_contato' => $dados['link_contato'] ?: null,
                ':classificacao' => $dados['classificacao_indicativa'] ?: null,
                ':imagem' => $dados['imagem'] ?? null
            ]);

            $eventoId = (int) $this->conexao->lastInsertId();


            // ----------------------------------------
            // 5. CADASTRAR DATAS E HORÁRIOS
            // ----------------------------------------

            if (empty($dados['sessoes'])) {
                throw new Exception("O evento precisa ter pelo menos uma data.");
            }

            $sqlData = "
                INSERT INTO DATA_EVENTO (
                    evento_id,
                    data_evento,
                    horario_inicio,
                    horario_fim
                ) VALUES (
                    :evento,
                    :data,
                    :inicio,
                    :fim
                )
            ";

            $stmtData = $this->conexao->prepare($sqlData);

            foreach ($dados['sessoes'] as $sessao) {

                $stmtData->execute([
                    ':evento' => $eventoId,
                    ':data' => $sessao['data_evento'],
                    ':inicio' => $sessao['horario_inicio'],
                    ':fim' => $sessao['horario_fim'] ?: null
                ]);
            }


            // ----------------------------------------
            // 6. CADASTRAR LINKS
            // ----------------------------------------

            $tiposPermitidos = [
                'whatsapp',
                'instagram',
                'sympla'
            ];

            if (!empty($dados['links'])) {

                $sqlLink = "
                    INSERT INTO LINK_EVENTO (
                        evento_id,
                        tipo_link,
                        url
                    ) VALUES (
                        :evento,
                        :tipo,
                        :url
                    )
                ";

                $stmtLink = $this->conexao->prepare($sqlLink);

                foreach ($dados['links'] as $link) {

                    if (!in_array($link['tipo_link'], $tiposPermitidos, true)) {
                        throw new Exception("Tipo de link inválido.");
                    }

                    $stmtLink->execute([
                        ':evento' => $eventoId,
                        ':tipo' => $link['tipo_link'],
                        ':url' => !empty($link['url']) ? $link['url'] : null
                    ]);
                }
            }


            // ----------------------------------------
            // 7. FINALIZAR TRANSAÇÃO
            // ----------------------------------------

            $this->conexao->commit();

            return $eventoId;

        } catch (Throwable $e) {

            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw $e;
        }
    }

}
