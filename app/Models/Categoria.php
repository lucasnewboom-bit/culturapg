
<?php

class Categoria {

    // Atributos
    private ?int $idCategoria;
    private string $nome;
    private bool $ativo;
    private ?int $criadoPorUsuarioId;

    // Construtor
    public function __construct(
        string $nome,
        bool $ativo = true,
        ?int $idCategoria = null,
        ?int $criadoPorUsuarioId = null
    ) {
        $this->idCategoria = $idCategoria;
        $this->nome = $nome;
        $this->ativo = $ativo;
        $this->criadoPorUsuarioId = $criadoPorUsuarioId;
    }

    // Getters
    public function getIdCategoria(): ?int {
        return $this->idCategoria;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getAtivo(): bool {
        return $this->ativo;
    }

    public function getCriadoPorUsuarioId(): ?int {
        return $this->criadoPorUsuarioId;
    }

    // Setters
    public function setNome(string $nome): void {
        $this->nome = $nome;
    }

    public function setAtivo(bool $ativo): void {
        $this->ativo = $ativo;
    }
}
