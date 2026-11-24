
<?php

// Inicia a sessão antes de qualquer saída HTML
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Identifica a página e a ação solicitadas
$pagina = $_GET['pagina'] ?? 'mapa';
$acao = $_GET['acao'] ?? 'listar';

try {

    // ============================================
    // CADASTRO DE USUÁRIO
    // ============================================

    if ($pagina === 'cadastro') {

        require_once __DIR__ . '/../app/Controllers/AutenticacaoController.php';

        $autenticacaoController = new AutenticacaoController();
        $mensagem = '';

        if (
            $acao === 'registrar' &&
            $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {
            $resultado = $autenticacaoController->cadastrar($_POST);

            if ($resultado['sucesso']) {
                $_SESSION['mensagem_autenticacao'] = $resultado['mensagem'];

                header('Location: index.php?pagina=login');
                exit;
            }

            $mensagem = $resultado['mensagem'];
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/autenticacao/cadastro.php';
        require_once __DIR__ . '/../views/layouts/footer.php';

    }

    // ============================================
    // LOGIN DE USUÁRIO
    // ============================================

    elseif ($pagina === 'login') {

        require_once __DIR__ . '/../app/Controllers/AutenticacaoController.php';

        $autenticacaoController = new AutenticacaoController();

        $mensagem = $_SESSION['mensagem_autenticacao'] ?? '';
        unset($_SESSION['mensagem_autenticacao']);

        if (
            $acao === 'entrar' &&
            $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {
            $resultado = $autenticacaoController->login($_POST);

            if ($resultado['sucesso']) {
                header('Location: index.php?pagina=mapa');
                exit;
            }

            $mensagem = $resultado['mensagem'];
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/autenticacao/login.php';
        require_once __DIR__ . '/../views/layouts/footer.php';

    }

    // ============================================
    // LOGOUT
    // ============================================

    elseif ($pagina === 'logout') {

        require_once __DIR__ . '/../app/Controllers/AutenticacaoController.php';

        $autenticacaoController = new AutenticacaoController();
        $autenticacaoController->logout();

        header('Location: index.php?pagina=login');
        exit;

    }

    // ============================================
    // TELA PRINCIPAL: MAPA
    // ============================================

    elseif ($pagina === 'mapa') {

        require_once __DIR__ . '/../views/mapa.php';

    }

  
    // ============================================
    // PERFIL DO USUÁRIO
    // ============================================

    elseif ($pagina === 'perfil') {

        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?pagina=login');
            exit;
        }

        require_once __DIR__ . '/../app/Controllers/AutenticacaoController.php';
        require_once __DIR__ . '/../app/DAO/HistoricoDAO.php';

        $autenticacaoController = new AutenticacaoController();
        $historicoDAO = new HistoricoDAO();
        $idUsuario = (int) ($_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_usuario'] ?? 0);
        $historicoDAO->importarSessao(
            $idUsuario,
            $_SESSION['historico_eventos'] ?? []
        );
        unset($_SESSION['historico_eventos']);
        $historico = $historicoDAO->listarRecentes($idUsuario);

        // Salvar alterações do perfil
        if (
            $acao === 'atualizar' &&
            $_SERVER['REQUEST_METHOD'] === 'POST'
        ) {
            $tokenCSRFPerfil = (string) ($_POST['csrf_token'] ?? '');
            if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenCSRFPerfil)) {
                $_SESSION['mensagem_perfil'] = 'A sessÃ£o expirou. Atualize a pÃ¡gina e tente novamente.';
                header('Location: index.php?pagina=perfil&acao=editar');
                exit;
            }

            $resultado = $autenticacaoController->atualizarPerfil(
                $_POST,
                $_FILES
            );

            $_SESSION['mensagem_perfil'] = $resultado['mensagem'];

            if ($resultado['sucesso']) {
                header('Location: index.php?pagina=perfil');
            } else {
                header('Location: index.php?pagina=perfil&acao=editar');
            }

            exit;
        }

        // Abrir formulário de edição
        if ($acao === 'editar') {

            $mensagem = $_SESSION['mensagem_perfil'] ?? '';
            unset($_SESSION['mensagem_perfil']);

            require_once __DIR__ . '/../views/layouts/header.php';
            require_once __DIR__ . '/../views/perfil/editar.php';
            require_once __DIR__ . '/../views/layouts/footer.php';

        } else {

            $mensagem = $_SESSION['mensagem_perfil'] ?? '';
            unset($_SESSION['mensagem_perfil']);

            require_once __DIR__ . '/../views/layouts/header.php';
            require_once __DIR__ . '/../views/perfil/index.php';
            require_once __DIR__ . '/../views/layouts/footer.php';

        }

    }

    // ============================================
    // GERENCIAMENTO DOS EVENTOS DO ORGANIZADOR
    // ============================================

    elseif ($pagina === 'organizador') {

        if (!isset($_SESSION['usuario']['id'])) {
            header('Location: index.php?pagina=login');
            exit;
        }

        require_once __DIR__ . '/../app/Controllers/EventoController.php';
        require_once __DIR__ . '/../app/Controllers/AtualizacaoController.php';
        $eventoController = new EventoController();
        $atualizacaoController = new AtualizacaoController();
        $idUsuario = (int) $_SESSION['usuario']['id'];
        $idEvento = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        $mensagem = $_SESSION['mensagem_eventos'] ?? '';
        unset($_SESSION['mensagem_eventos']);
        $erro = '';
        $erroAtualizacao = '';
        $tituloAtualizacao = trim((string) ($_POST['titulo'] ?? ''));
        $mensagemAtualizacao = trim((string) ($_POST['mensagem'] ?? ''));
        $dadosFormulario = $_POST;
        $categorias = $eventoController->categorias();
        $temOrganizador = $eventoController->possuiOrganizador($idUsuario);

        if (!isset($_SESSION['csrf_eventos'])) {
            $_SESSION['csrf_eventos'] = bin2hex(random_bytes(32));
        }
        $csrfEventos = $_SESSION['csrf_eventos'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $token = (string) ($_POST['csrf_token'] ?? '');
                if (!hash_equals($csrfEventos, $token)) {
                    throw new RuntimeException('A sessão do formulário expirou. Atualize a página e tente novamente.');
                }

                if ($acao === 'cadastrar') {
                    $novoId = $eventoController->cadastrar($idUsuario, $_POST, $_FILES['imagem'] ?? null);
                    $_SESSION['mensagem_eventos'] = 'Evento cadastrado com sucesso.';
                    header('Location: index.php?pagina=organizador');
                    exit;
                } elseif ($acao === 'atualizar' && $idEvento > 0) {
                    $eventoController->atualizar($idEvento, $idUsuario, $_POST, $_FILES['imagem'] ?? null);
                    $_SESSION['mensagem_eventos'] = 'Evento atualizado com sucesso.';
                    header('Location: index.php?pagina=organizador');
                    exit;
                } elseif ($acao === 'excluir' && $idEvento > 0) {
                    if (!$eventoController->excluir($idEvento, $idUsuario)) {
                        throw new RuntimeException('O evento não foi encontrado ou já está inativo.');
                    }
                    $_SESSION['mensagem_eventos'] = 'Evento removido da divulgação. Os registros relacionados foram preservados.';
                    header('Location: index.php?pagina=organizador');
                    exit;
                } elseif ($acao === 'publicar_atualizacao' && $idEvento > 0) {
                    $quantidadeNotificacoes = $atualizacaoController->publicar(
                        $idEvento,
                        $idUsuario,
                        $_POST
                    );
                    $_SESSION['mensagem_eventos'] = 'Atualização publicada. Notificações enviadas a ' . $quantidadeNotificacoes . ' usuário(s) que favoritaram o evento.';
                    header('Location: index.php?pagina=organizador&acao=atualizacoes&id=' . $idEvento);
                    exit;
                } else {
                    throw new RuntimeException('Ação de evento inválida.');
                }
            } catch (Throwable $e) {
                $erro = $e instanceof InvalidArgumentException || $e instanceof RuntimeException || $e instanceof DomainException
                    ? $e->getMessage()
                    : 'Não foi possível salvar o evento. Revise os dados e tente novamente.';
                if ($acao === 'publicar_atualizacao') {
                    $erroAtualizacao = $erro;
                }
                if (!$e instanceof InvalidArgumentException && !$e instanceof RuntimeException && !$e instanceof DomainException) {
                    error_log($e->getMessage());
                }
            }
        }

        if (in_array($acao, ['atualizacoes', 'publicar_atualizacao'], true)) {
            $eventoAtual = $eventoController->buscar($idEvento, $idUsuario);
            if (!$eventoAtual) {
                http_response_code(404);
                echo 'Evento não encontrado ou você não tem permissão para acessá-lo.';
            } else {
                $atualizacoes = $atualizacaoController->listar($idEvento, $idUsuario);
                require_once __DIR__ . '/../views/layouts/header.php';
                require_once __DIR__ . '/../views/eventos/atualizacoes.php';
                require_once __DIR__ . '/../views/layouts/footer.php';
            }
        } elseif ($acao === 'novo' || $acao === 'editar' || in_array($acao, ['cadastrar', 'atualizar'], true)) {
            $evento = null;
            if ($acao === 'editar' || $acao === 'atualizar') {
                $evento = $eventoController->buscar($idEvento, $idUsuario);
                if (!$evento) {
                    http_response_code(404);
                    echo 'Evento não encontrado ou você não tem permissão para acessá-lo.';
                    } else {
                        $categorias = $eventoController->categorias((int) $evento['categoria_id']);
                }
            }

            if ($acao === 'novo' || $acao === 'cadastrar' || $evento) {
                require_once __DIR__ . '/../views/layouts/header.php';
                require_once __DIR__ . '/../views/eventos/formulario.php';
                require_once __DIR__ . '/../views/layouts/footer.php';
            }
        } else {
            $eventos = $eventoController->listar($idUsuario);
            require_once __DIR__ . '/../views/layouts/header.php';
            require_once __DIR__ . '/../views/eventos/gerenciar.php';
            require_once __DIR__ . '/../views/layouts/footer.php';
        }

    }

    // ============================================
    // NOTIFICAÇÕES DO USUÁRIO
    // ============================================

    elseif ($pagina === 'notificacoes') {

        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?pagina=login');
            exit;
        }

        require_once __DIR__ . '/../app/Controllers/NotificacaoController.php';
        $notificacaoController = new NotificacaoController();
        $idUsuario = (int) ($_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_usuario'] ?? 0);
        $mensagemNotificacao = $_SESSION['mensagem_notificacao'] ?? '';
        unset($_SESSION['mensagem_notificacao']);
        $erroNotificacao = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $acao === 'ler') {
            $token = (string) ($_POST['csrf_token'] ?? '');
            if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                $erroNotificacao = 'A sessão expirou. Atualize a página e tente novamente.';
            } else {
                $idNotificacao = filter_input(INPUT_POST, 'id_notificacao', FILTER_VALIDATE_INT);
                if (!$idNotificacao || !$notificacaoController->marcarComoLida((int) $idNotificacao, $idUsuario)) {
                    $erroNotificacao = 'A notificação não foi encontrada na sua conta.';
                } else {
                    $_SESSION['mensagem_notificacao'] = 'Notificação marcada como lida.';
                    header('Location: index.php?pagina=notificacoes');
                    exit;
                }
            }
        }

        $notificacoes = $notificacaoController->listar($idUsuario);
        $quantidadeNaoLidas = $notificacaoController->contarNaoLidas($idUsuario);
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/notificacoes/listar.php';
        require_once __DIR__ . '/../views/layouts/footer.php';

    }


    // ============================================
    // CATÁLOGO DE EVENTOS
    // ============================================

    elseif ($pagina === 'catalogo') {

        require_once __DIR__ . '/../app/DAO/EventoDAO.php';
        require_once __DIR__ . '/../app/DAO/FavoritoDAO.php';

        $eventoDAO = new EventoDAO();
        $eventos = $eventoDAO->listarTodos();
        $favoritos = [];

        if (isset($_SESSION['usuario']['id'])) {
            $favoritoDAO = new FavoritoDAO();
            $favoritoDAO->importarSessao(
                (int) $_SESSION['usuario']['id'],
                $_SESSION['favoritos'] ?? []
            );
            unset($_SESSION['favoritos']);
            $favoritos = $favoritoDAO->listarIds((int) $_SESSION['usuario']['id']);
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/eventos/catalogo.php';
        require_once __DIR__ . '/../views/layouts/footer.php';

    }

    // ============================================
    // ÁREA DE CATEGORIAS
    // ============================================

    elseif ($pagina === 'categorias') {

        require_once __DIR__ . '/../app/Controllers/CategoriaController.php';

        $categoriaController = new CategoriaController();

        switch ($acao) {

            case 'listar':

                $categorias = $categoriaController->listar();
                $mensagemCategorias = $_SESSION['mensagem_categorias'] ?? '';
                unset($_SESSION['mensagem_categorias']);

                require_once __DIR__ . '/../views/categorias/listar.php';

                break;

            case 'cadastrar':

                if (!isset($_SESSION['usuario']['id'])) {
                    header('Location: index.php?pagina=login');
                    exit;
                }

                $idUsuarioCategoria = (int) $_SESSION['usuario']['id'];
                $erroCategoria = '';
                $mensagemCategoria = $_SESSION['mensagem_categorias'] ?? '';
                unset($_SESSION['mensagem_categorias']);
                $nomeCategoria = trim((string) ($_POST['nome'] ?? ''));
                $origemCategoria = (($_POST['origem'] ?? $_GET['origem'] ?? '') === 'evento')
                    ? 'evento'
                    : 'lista';
                $categoriaCriada = null;
                $categoriaExcluidaId = (int) ($_SESSION['categoria_excluida_id'] ?? 0);
                unset($_SESSION['categoria_excluida_id']);

                if (isset($_GET['categoria_criada'])) {
                    $idCategoriaCriada = filter_var($_GET['categoria_criada'], FILTER_VALIDATE_INT);
                    if ($idCategoriaCriada) {
                        $categoriaCriada = $categoriaController->buscar((int) $idCategoriaCriada);
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $tokenCategoria = (string) ($_POST['csrf_token'] ?? '');

                    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenCategoria)) {
                        $erroCategoria = 'A sessão expirou. Atualize a página e tente novamente.';
                    } elseif (($_POST['operacao'] ?? 'cadastrar') === 'excluir') {
                        $idCategoriaExcluir = filter_var($_POST['id_categoria'] ?? null, FILTER_VALIDATE_INT);

                        try {
                            if ($idCategoriaExcluir && $categoriaController->excluir((int) $idCategoriaExcluir, $idUsuarioCategoria)) {
                                $_SESSION['mensagem_categorias'] = 'Sua categoria foi removida. Os eventos já cadastrados foram preservados.';
                                $_SESSION['categoria_excluida_id'] = (int) $idCategoriaExcluir;
                            } else {
                                $_SESSION['mensagem_categorias'] = 'Essa categoria não existe, já foi removida ou não foi criada pela sua conta.';
                            }
                        } catch (Throwable $e) {
                            error_log($e->getMessage());
                            $_SESSION['mensagem_categorias'] = 'Não foi possível remover a categoria. Tente novamente.';
                        }

                        header('Location: index.php?pagina=categorias&acao=cadastrar' . ($origemCategoria === 'evento' ? '&origem=evento' : ''));
                        exit;
                    } else {
                        try {
                            $idNovaCategoria = $categoriaController->cadastrar($nomeCategoria, $idUsuarioCategoria);

                            if ($origemCategoria === 'evento') {
                                header('Location: index.php?pagina=categorias&acao=cadastrar&origem=evento&categoria_criada=' . $idNovaCategoria);
                            } else {
                                $_SESSION['mensagem_categorias'] = 'Categoria cadastrada com sucesso.';
                                header('Location: index.php?pagina=categorias&acao=listar');
                            }

                            exit;
                        } catch (InvalidArgumentException $e) {
                            $erroCategoria = $e->getMessage();
                        } catch (PDOException $e) {
                            if ($e->getCode() === '23000') {
                                $erroCategoria = 'Já existe uma categoria com esse nome.';
                            } else {
                                error_log($e->getMessage());
                                $erroCategoria = 'Não foi possível salvar a categoria. Tente novamente.';
                            }
                        }
                    }
                }

                $categoriasCriadas = $categoriaController->listarCriadasPor($idUsuarioCategoria);

                require_once __DIR__ . '/../views/layouts/header.php';
                require_once __DIR__ . '/../views/categorias/cadastrar.php';
                require_once __DIR__ . '/../views/layouts/footer.php';

                break;

            case 'editar':
            case 'atualizar':

                if (!isset($_SESSION['usuario']['id'])) {
                    header('Location: index.php?pagina=login');
                    exit;
                }

                $idCategoriaEditar = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
                if (!$idCategoriaEditar || $idCategoriaEditar <= 0) {
                    http_response_code(400);
                    echo 'Identificador de categoria inválido.';
                    break;
                }

                $idUsuarioCategoria = (int) $_SESSION['usuario']['id'];
                $categoria = $categoriaController->buscarCriadaPor((int) $idCategoriaEditar, $idUsuarioCategoria);
                if (!$categoria) {
                    http_response_code(404);
                    echo 'Categoria não encontrada ou sem permissão para editá-la.';
                    break;
                }

                $erroCategoria = '';
                $nomeCategoria = $categoria->getNome();
                if ($acao === 'atualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $tokenCategoria = (string) ($_POST['csrf_token'] ?? '');
                    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenCategoria)) {
                        $erroCategoria = 'A sessão expirou. Atualize a página e tente novamente.';
                    } else {
                        $nomeCategoria = trim((string) ($_POST['nome'] ?? ''));
                        try {
                            if (!$categoriaController->atualizar((int) $idCategoriaEditar, $nomeCategoria, $idUsuarioCategoria)) {
                                http_response_code(404);
                                throw new RuntimeException('Categoria não encontrada ou sem permissão para editá-la.');
                            }
                            $_SESSION['mensagem_categorias'] = 'Categoria atualizada com sucesso.';
                            header('Location: index.php?pagina=categorias&acao=listar');
                            exit;
                        } catch (InvalidArgumentException $e) {
                            $erroCategoria = $e->getMessage();
                        } catch (PDOException $e) {
                            if ($e->getCode() === '23000') {
                                $erroCategoria = 'Já existe uma categoria com esse nome.';
                            } else {
                                error_log($e->getMessage());
                                $erroCategoria = 'Não foi possível atualizar a categoria. Tente novamente.';
                            }
                        } catch (Throwable $e) {
                            if ($e->getMessage() === 'Categoria não encontrada ou sem permissão para editá-la.') {
                                $erroCategoria = $e->getMessage();
                            } else {
                                error_log($e->getMessage());
                                $erroCategoria = 'Não foi possível atualizar a categoria. Tente novamente.';
                            }
                        }
                    }
                }

                require_once __DIR__ . '/../views/layouts/header.php';
                require_once __DIR__ . '/../views/categorias/editar.php';
                require_once __DIR__ . '/../views/layouts/footer.php';

                break;

            default:

                echo "Ação não encontrada.";

                break;
        }

    }

    // ============================================
    // DETALHES DO EVENTO
    // ============================================

    elseif ($pagina === 'evento') {

        require_once __DIR__ . '/../app/DAO/EventoDAO.php';
        require_once __DIR__ . '/../app/DAO/FavoritoDAO.php';
        require_once __DIR__ . '/../app/DAO/HistoricoDAO.php';

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {

            http_response_code(400);

            echo "Identificador de evento inválido.";

        } else {

            // ADICIONAR OU REMOVER FAVORITO

            if ($acao === 'favoritar' && $_SERVER['REQUEST_METHOD'] === 'POST') {

                $token = (string) ($_POST['csrf_token'] ?? '');
                if (!hash_equals($_SESSION['csrf_token'], $token)) {
                    http_response_code(403);
                    echo 'Formulário expirado. Atualize a página e tente novamente.';
                    exit;
                }

                if (!isset($_SESSION['usuario'])) {
                    header('Location: index.php?pagina=login');
                    exit;
                }

                $favoritoDAO = new FavoritoDAO();
                $idUsuario = (int) ($_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_usuario']);
                $favoritoDAO->importarSessao($idUsuario, $_SESSION['favoritos'] ?? []);
                unset($_SESSION['favoritos']);
                $favoritoDAO->alternar($idUsuario, $id);

                header('Location: index.php?pagina=evento&id=' . $id);
                exit;
            }

            $eventoDAO = new EventoDAO();

            $evento = $eventoDAO->buscarPorId($id);

            if (!$evento) {

                http_response_code(404);

                echo "Evento não encontrado.";

            } else {

                if (isset($_SESSION['usuario'])) {
                    $idUsuario = (int) ($_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_usuario'] ?? 0);
                    $historicoDAO = new HistoricoDAO();
                    $historicoDAO->importarSessao(
                        $idUsuario,
                        $_SESSION['historico_eventos'] ?? []
                    );
                    unset($_SESSION['historico_eventos']);
                    $historicoDAO->registrarVisualizacao($idUsuario, $id);
                }

                $favoritado = false;
                if (isset($_SESSION['usuario']['id'])) {
                    $favoritoDAO = new FavoritoDAO();
                    $idUsuario = (int) $_SESSION['usuario']['id'];
                    $favoritoDAO->importarSessao($idUsuario, $_SESSION['favoritos'] ?? []);
                    unset($_SESSION['favoritos']);
                    $favoritado = $favoritoDAO->ehFavorito($idUsuario, $id);
                }

                require_once __DIR__ . '/../views/layouts/header.php';

                require_once __DIR__ . '/../views/eventos/detalhes.php';

                require_once __DIR__ . '/../views/layouts/footer.php';

            }
        }

    }

    // ============================================
    // PÁGINA NÃO ENCONTRADA
    // ============================================

    else {

        http_response_code(404);

        echo "Página não encontrada.";

    }

} catch (Throwable $e) {

    error_log($e->getMessage());
    http_response_code(500);
    echo "Não foi possível concluir esta solicitação.";

}
