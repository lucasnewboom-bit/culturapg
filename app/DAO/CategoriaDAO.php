
<?php

require_once __DIR__ . '/../Database/Conexao.php';
require_once __DIR__ . '/../Models/Categoria.php';

class CategoriaDAO {

    private PDO $conexao;

    // Construtor
    public function __construct() {
        $this->conexao = Conexao::conectar();
    }

    // CREATE - Cadastrar categoria
    public function inserir(Categoria $categoria, int $idUsuarioCriador): int {

        $sql = "INSERT INTO CATEGORIA (nome, ativo, criado_por_usuario_id)
                VALUES (:nome, :ativo, :criador)";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':nome' => $categoria->getNome(),
            ':ativo' => $categoria->getAtivo(),
            ':criador' => $idUsuarioCriador
        ]);

        return (int) $this->conexao->lastInsertId();
    }

    // READ - Listar todas as categorias
    public function listar(): array {

        $sql = "SELECT * FROM CATEGORIA ORDER BY nome";

        $stmt = $this->conexao->query($sql);

        $categorias = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $categorias[] = new Categoria(
                $dados['nome'],
                (bool) $dados['ativo'],
                (int) $dados['id_categoria']
            );
        }

        return $categorias;
    }

    // Lista somente as categorias ativas criadas pelo usuário autenticado.
    public function listarCriadasPor(int $idUsuario): array {

        $sql = "SELECT nome, ativo, id_categoria
                FROM CATEGORIA
                WHERE criado_por_usuario_id = :usuario
                  AND ativo = 1
                ORDER BY nome";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':usuario' => $idUsuario]);

        $categorias = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categorias[] = new Categoria(
                $dados['nome'],
                (bool) $dados['ativo'],
                (int) $dados['id_categoria']
            );
        }

        return $categorias;
    }

    // A desativação mantém intactos os eventos que já usam a categoria.
    public function excluirCriadaPor(int $idCategoria, int $idUsuario): bool {

        $sql = "UPDATE CATEGORIA
                SET ativo = 0
                WHERE id_categoria = :categoria
                  AND criado_por_usuario_id = :usuario
                  AND ativo = 1";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([
            ':categoria' => $idCategoria,
            ':usuario' => $idUsuario
        ]);

        return $stmt->rowCount() === 1;
    }

    // READ - Buscar categoria por ID
    public function buscarPorId(int $id): ?Categoria {

        $sql = "SELECT * FROM CATEGORIA
                WHERE id_categoria = :id";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new Categoria(
            $dados['nome'],
            (bool) $dados['ativo'],
            (int) $dados['id_categoria']
        );
    }

    // UPDATE - Atualizar categoria
    public function atualizar(
        int $id,
        Categoria $categoria
    ): bool {

        $sql = "UPDATE CATEGORIA
                SET nome = :nome,
                    ativo = :ativo
                WHERE id_categoria = :id";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':nome' => $categoria->getNome(),
            ':ativo' => $categoria->getAtivo(),
            ':id' => $id
        ]);
    }

}
