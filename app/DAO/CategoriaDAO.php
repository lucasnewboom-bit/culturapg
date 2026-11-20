
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
    public function inserir(Categoria $categoria): int {

        $sql = "INSERT INTO CATEGORIA (nome, ativo)
                VALUES (:nome, :ativo)";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':nome' => $categoria->getNome(),
            ':ativo' => $categoria->getAtivo()
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

    // DELETE - Excluir categoria
    public function excluir(int $id): bool {

        $sql = "DELETE FROM CATEGORIA
                WHERE id_categoria = :id";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}
