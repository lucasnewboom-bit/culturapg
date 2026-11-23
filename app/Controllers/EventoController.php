<?php

require_once __DIR__ . '/../DAO/EventoDAO.php';
require_once __DIR__ . '/../Services/ImagemEventoService.php';

class EventoController
{
    private EventoDAO $eventoDAO;
    private ImagemEventoService $imagemEventoService;

    public function __construct()
    {
        $this->eventoDAO = new EventoDAO();
        $this->imagemEventoService = new ImagemEventoService();
    }

    public function listar(int $idUsuario): array
    {
        return $this->eventoDAO->listarDoUsuario($idUsuario);
    }

    public function possuiOrganizador(int $idUsuario): bool
    {
        return $this->eventoDAO->possuiOrganizador($idUsuario);
    }

    public function buscar(int $idEvento, int $idUsuario): ?array
    {
        return $this->eventoDAO->buscarDoUsuario($idEvento, $idUsuario);
    }

    public function categorias(?int $categoriaAtualId = null): array
    {
        return $this->eventoDAO->listarCategorias($categoriaAtualId);
    }

    public function cadastrar(int $idUsuario, array $entrada, ?array $arquivoImagem = null): int
    {
        $dados = $this->validar($entrada, $idUsuario);
        $imagemNova = $this->imagemEventoService->salvarUpload($arquivoImagem);
        $dados['imagem'] = $imagemNova;
        try {
            return $this->eventoDAO->cadastrar($idUsuario, $dados);
        } catch (Throwable $e) {
            $this->imagemEventoService->remover($imagemNova);
            throw $e;
        }
    }

    public function atualizar(int $idEvento, int $idUsuario, array $entrada, ?array $arquivoImagem = null): bool
    {
        $eventoAtual = $this->eventoDAO->buscarDoUsuario($idEvento, $idUsuario);
        if (!$eventoAtual) {
            throw new RuntimeException('Evento não encontrado ou sem permissão para editá-lo.');
        }
        $dados = $this->validar($entrada, $idUsuario, [(int) $eventoAtual['categoria_id']]);
        $imagemNova = $this->imagemEventoService->salvarUpload($arquivoImagem);
        $dados['imagem'] = $imagemNova;
        try {
            $salvou = $this->eventoDAO->atualizar($idEvento, $idUsuario, $dados);
            if (!$salvou) {
                $this->imagemEventoService->remover($imagemNova);
                return false;
            }
            if ($salvou && $imagemNova) {
                $this->imagemEventoService->remover($eventoAtual['imagem'] ?? null);
            }
            return $salvou;
        } catch (Throwable $e) {
            $this->imagemEventoService->remover($imagemNova);
            throw $e;
        }
    }

    public function excluir(int $idEvento, int $idUsuario): bool
    {
        return $this->eventoDAO->excluirLogicamente($idEvento, $idUsuario);
    }

    private function validar(array $entrada, int $idUsuario, array $categoriasInativasPermitidas = []): array
    {
        $titulo = trim((string) ($entrada['titulo'] ?? ''));
        $local = trim((string) ($entrada['local_nome'] ?? ''));
        $endereco = trim((string) ($entrada['endereco'] ?? ''));
        $categoriaId = filter_var($entrada['categoria_id'] ?? null, FILTER_VALIDATE_INT);

        if ($titulo === '' || mb_strlen($titulo) > 150) {
            throw new InvalidArgumentException('Informe um título com até 150 caracteres.');
        }
        if ($local === '' || mb_strlen($local) > 100 || $endereco === '' || mb_strlen($endereco) > 255) {
            throw new InvalidArgumentException('Informe o nome do local (até 100 caracteres) e o endereço (até 255 caracteres).');
        }
        $categoriasAtivas = array_map('intval', array_column($this->categorias(), 'id_categoria'));
        $categoriasPermitidas = array_merge($categoriasAtivas, array_map('intval', $categoriasInativasPermitidas));
        if (!$categoriaId || !in_array((int) $categoriaId, $categoriasPermitidas, true)) {
            throw new InvalidArgumentException('Selecione uma categoria ativa.');
        }

        $gratuito = isset($entrada['gratuito']) && (string) $entrada['gratuito'] === '1';
        $valorTexto = str_replace(',', '.', trim((string) ($entrada['valor'] ?? '0')));
        if (!$gratuito && (!is_numeric($valorTexto) || (float) $valorTexto < 0 || (float) $valorTexto > 99999999.99)) {
            throw new InvalidArgumentException('Informe um valor válido, maior ou igual a zero.');
        }

        $tipoContato = trim((string) ($entrada['tipo_contato'] ?? ''));
        $tiposContato = ['', 'whatsapp', 'email', 'telefone', 'site', 'outro'];
        if (!in_array($tipoContato, $tiposContato, true)) {
            throw new InvalidArgumentException('Selecione um tipo de contato válido.');
        }
        $linkContato = trim((string) ($entrada['link_contato'] ?? ''));
        if (strlen($linkContato) > 500) {
            throw new InvalidArgumentException('A informação de contato pode ter até 500 caracteres.');
        }
        if ($linkContato !== '') {
            $contatoValido = true;
            if ($tipoContato === 'email') {
                $contatoValido = (bool) filter_var($linkContato, FILTER_VALIDATE_EMAIL);
            } elseif ($tipoContato === 'telefone' || $tipoContato === 'whatsapp') {
                $contatoValido = (bool) filter_var($linkContato, FILTER_VALIDATE_URL)
                    || (bool) preg_match('/^\+?[0-9\s().-]{7,30}$/', $linkContato);
            } elseif ($tipoContato === 'site') {
                $contatoValido = (bool) filter_var($linkContato, FILTER_VALIDATE_URL);
            }
            if (!$contatoValido) {
                throw new InvalidArgumentException('Informe um endereço de contato válido para o tipo selecionado.');
            }
        }

        $latitude = trim((string) ($entrada['latitude'] ?? ''));
        $longitude = trim((string) ($entrada['longitude'] ?? ''));
        if (($latitude === '') !== ($longitude === '')) {
            throw new InvalidArgumentException('Preencha latitude e longitude juntas ou deixe as duas vazias.');
        }
        if ($latitude === '' || $longitude === '') {
            throw new InvalidArgumentException('Marque a localização do evento no mapa para que ele apareça no mapa interativo.');
        }
        if ($latitude !== '' && (!is_numeric($latitude) || (float) $latitude < -90 || (float) $latitude > 90 || !is_numeric($longitude) || (float) $longitude < -180 || (float) $longitude > 180)) {
            throw new InvalidArgumentException('As coordenadas geográficas são inválidas.');
        }

        $sessoesEntrada = $entrada['sessoes'] ?? [];
        if (!is_array($sessoesEntrada)) $sessoesEntrada = [];
        $sessoes = [];
        foreach ($sessoesEntrada as $sessao) {
            if (!is_array($sessao)) continue;
            $data = trim((string) ($sessao['data_evento'] ?? ''));
            $inicio = trim((string) ($sessao['horario_inicio'] ?? ''));
            $fim = trim((string) ($sessao['horario_fim'] ?? ''));
            if ($data === '' && $inicio === '' && $fim === '') continue;
            $dataValida = DateTime::createFromFormat('Y-m-d', $data);
            if (!$dataValida || $dataValida->format('Y-m-d') !== $data || $inicio === '' || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $inicio) || ($fim !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $fim))) {
                throw new InvalidArgumentException('Revise as datas e horários. Toda sessão deve ter data e horário de início válidos.');
            }
            $sessoes[] = ['data_evento' => $data, 'horario_inicio' => $inicio, 'horario_fim' => $fim];
        }
        if (!$sessoes) throw new InvalidArgumentException('Adicione pelo menos uma data e horário para o evento.');

        $urlsEntrada = $entrada['links'] ?? [];
        if (!is_array($urlsEntrada)) $urlsEntrada = [];
        $links = [];
        foreach (['whatsapp', 'instagram', 'sympla'] as $tipo) {
            $url = trim((string) ($urlsEntrada[$tipo] ?? ''));
            if ($url === '') continue;
            if (strlen($url) > 500 || !filter_var($url, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException('Informe uma URL válida para o link de ' . ucfirst($tipo) . '.');
            }
            $links[] = ['tipo_link' => $tipo, 'url' => $url];
        }

        $classificacao = trim((string) ($entrada['classificacao_indicativa'] ?? ''));
        if ($classificacao !== '' && !in_array($classificacao, ['Livre', '10', '12', '14', '16', '18'], true)) {
            throw new InvalidArgumentException('Selecione uma classificação indicativa válida.');
        }

        $tipoOrganizador = trim((string) ($entrada['tipo_organizador'] ?? ''));
        if (!$this->possuiOrganizador($idUsuario) && ($tipoOrganizador === '' || mb_strlen($tipoOrganizador) > 50)) {
            throw new InvalidArgumentException('Informe o tipo de organizador com até 50 caracteres para criar seu cadastro de organizador.');
        }

        return [
            'titulo' => $titulo,
            'descricao' => trim((string) ($entrada['descricao'] ?? '')),
            'categoria_id' => (int) $categoriaId,
            'valor' => $gratuito ? 0 : number_format((float) $valorTexto, 2, '.', ''),
            'gratuito' => $gratuito,
            'tipo_contato' => $tipoContato,
            'link_contato' => $linkContato,
            'classificacao_indicativa' => $classificacao,
            'local_nome' => $local,
            'endereco' => $endereco,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'sessoes' => $sessoes,
            'links' => $links,
            'tipo_organizador' => $tipoOrganizador
        ];
    }
}
