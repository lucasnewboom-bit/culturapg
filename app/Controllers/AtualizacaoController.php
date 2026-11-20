<?php

require_once __DIR__ . '/../DAO/AtualizacaoEventoDAO.php';

class AtualizacaoController
{
    private AtualizacaoEventoDAO $atualizacaoDAO;

    public function __construct()
    {
        $this->atualizacaoDAO = new AtualizacaoEventoDAO();
    }

    public function listar(int $idEvento, int $idUsuario): array
    {
        return $this->atualizacaoDAO->listarDoEvento($idEvento, $idUsuario);
    }

    public function publicar(int $idEvento, int $idUsuario, array $entrada): int
    {
        $titulo = trim((string) ($entrada['titulo'] ?? ''));
        $mensagem = trim((string) ($entrada['mensagem'] ?? ''));

        if ($titulo === '' || mb_strlen($titulo) > 150) {
            throw new InvalidArgumentException('Informe um título com até 150 caracteres.');
        }
        if ($mensagem === '') {
            throw new InvalidArgumentException('Escreva a mensagem da atualização.');
        }

        return $this->atualizacaoDAO->publicar($idEvento, $idUsuario, $titulo, $mensagem);
    }
}
