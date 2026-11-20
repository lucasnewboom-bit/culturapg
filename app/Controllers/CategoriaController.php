
<?php

require_once __DIR__ . '/../DAO/CategoriaDAO.php';

class CategoriaController {

    private CategoriaDAO $categoriaDAO;

    // Construtor
    public function __construct() {
        $this->categoriaDAO = new CategoriaDAO();
    }

    // READ - Listar categorias
    public function listar(): array {
        return $this->categoriaDAO->listar();
    }

    // READ - Buscar categoria
    public function buscar(int $id): ?Categoria {
        return $this->categoriaDAO->buscarPorId($id);
    }

    // CREATE - Cadastrar categoria
    public function cadastrar(string $nome): int {

        $nome = trim($nome);

        if (empty($nome)) {
            throw new InvalidArgumentException(
                "O nome da categoria é obrigatório."
            );
        }

        $categoria = new Categoria($nome);

        return $this->categoriaDAO->inserir($categoria);
    }

    // UPDATE - Atualizar categoria
    public function atualizar(
        int $id,
        string $nome,
        bool $ativo
    ): bool {

        $nome = trim($nome);

        if (empty($nome)) {
            throw new InvalidArgumentException(
                "O nome da categoria é obrigatório."
            );
        }

        $categoria = new Categoria($nome, $ativo);

        return $this->categoriaDAO->atualizar(
            $id,
            $categoria
        );
    }

    // DELETE - Excluir categoria
    public function excluir(int $id): bool {

        if ($id <= 0) {
            throw new InvalidArgumentException(
                "ID de categoria inválido."
            );
        }

        return $this->categoriaDAO->excluir($id);
    }
}
