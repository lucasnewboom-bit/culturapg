
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

    public function listarCriadasPor(int $idUsuario): array {
        return $this->categoriaDAO->listarCriadasPor($idUsuario);
    }

    // READ - Buscar categoria
    public function buscar(int $id): ?Categoria {
        return $this->categoriaDAO->buscarPorId($id);
    }

    public function buscarCriadaPor(int $idCategoria, int $idUsuario): ?Categoria {
        if ($idCategoria <= 0 || $idUsuario <= 0) return null;
        return $this->categoriaDAO->buscarCriadaPor($idCategoria, $idUsuario);
    }

    // CREATE - Cadastrar categoria
    public function cadastrar(string $nome, int $idUsuarioCriador): int {

        $nome = trim($nome);

        if ($nome === '') {
            throw new InvalidArgumentException(
                "O nome da categoria é obrigatório."
            );
        }

        if (mb_strlen($nome) > 50) {
            throw new InvalidArgumentException(
                "O nome da categoria deve ter no máximo 50 caracteres."
            );
        }

        if ($idUsuarioCriador <= 0) {
            throw new InvalidArgumentException("É necessário entrar na sua conta para criar uma categoria.");
        }

        $categoria = new Categoria($nome);

        return $this->categoriaDAO->inserir($categoria, $idUsuarioCriador);
    }

    public function excluir(int $idCategoria, int $idUsuario): bool {
        if ($idCategoria <= 0 || $idUsuario <= 0) {
            throw new InvalidArgumentException("Categoria ou usuário inválido.");
        }

        return $this->categoriaDAO->excluirCriadaPor($idCategoria, $idUsuario);
    }

    // UPDATE - Atualizar categoria
    public function atualizar(int $id, string $nome, int $idUsuario): bool {
        $nome = trim($nome);
        if ($id <= 0 || $idUsuario <= 0) {
            throw new InvalidArgumentException("Categoria ou usuário inválido.");
        }
        if ($nome === '') {
            throw new InvalidArgumentException("O nome da categoria é obrigatório.");
        }
        if (mb_strlen($nome) > 50) {
            throw new InvalidArgumentException("O nome da categoria deve ter no máximo 50 caracteres.");
        }
        return $this->categoriaDAO->atualizarCriadaPor($id, $nome, $idUsuario);
    }

}
