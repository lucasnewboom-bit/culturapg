<?php

class ImagemEventoService
{
    private const PASTA_RELATIVA = 'img/eventos/organizadores';
    private const TAMANHO_MAXIMO = 5242880;

    public function salvarUpload(?array $arquivo): ?string
    {
        if (!$arquivo || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Não foi possível receber a imagem. Tente novamente.');
        }
        if (!isset($arquivo['tmp_name'], $arquivo['size']) || !is_uploaded_file($arquivo['tmp_name'])) {
            throw new InvalidArgumentException('O arquivo enviado não é um upload válido.');
        }
        if ((int) $arquivo['size'] < 1 || (int) $arquivo['size'] > self::TAMANHO_MAXIMO) {
            throw new InvalidArgumentException('A imagem deve ter até 5 MB.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
        $extensoes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $infoImagem = @getimagesize($arquivo['tmp_name']);
        if (!isset($extensoes[$mime]) || !$infoImagem || !in_array($infoImagem[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            throw new InvalidArgumentException('Use uma imagem JPG, PNG ou WEBP válida.');
        }

        $pasta = dirname(__DIR__, 2) . '/public/' . self::PASTA_RELATIVA;
        if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
            throw new RuntimeException('Não foi possível preparar o armazenamento das imagens.');
        }
        $nome = bin2hex(random_bytes(16)) . '.' . $extensoes[$mime];
        if (!move_uploaded_file($arquivo['tmp_name'], $pasta . '/' . $nome)) {
            throw new RuntimeException('Não foi possível salvar a imagem enviada.');
        }
        return self::PASTA_RELATIVA . '/' . $nome;
    }

    public function remover(?string $caminhoRelativo): void
    {
        if (!$caminhoRelativo || !preg_match('#^' . preg_quote(self::PASTA_RELATIVA, '#') . '/[a-f0-9]{32}\.(jpg|png|webp)$#', $caminhoRelativo)) {
            return;
        }
        $raiz = realpath(dirname(__DIR__, 2) . '/public/' . self::PASTA_RELATIVA);
        $arquivo = realpath(dirname(__DIR__, 2) . '/public/' . $caminhoRelativo);
        if ($raiz && $arquivo && dirname($arquivo) === $raiz && is_file($arquivo)) {
            unlink($arquivo);
        }
    }
}
