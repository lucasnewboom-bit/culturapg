
<?php

require_once __DIR__ . '/../DAO/AutenticacaoDAO.PHP';

class AutenticacaoController
{
    private AutenticacaoDAO $autenticacaoDAO;

    public function __construct()
    {
        $this->autenticacaoDAO = new AutenticacaoDAO();
    }

    // Processa o cadastro
  
public function cadastrar(array $dados): array
{
    $nome = trim($dados['nome'] ?? '');
    $username = trim($dados['username'] ?? '');
    $email = trim($dados['email'] ?? '');
    $senha = $dados['senha'] ?? '';
    $confirmarSenha = $dados['confirmar_senha'] ?? '';

    // Verificar campos obrigatórios
    if (
        $nome === '' ||
        $username === '' ||
        $email === '' ||
        $senha === '' ||
        $confirmarSenha === ''
    ) {
        return [
            'sucesso' => false,
            'mensagem' => 'Preencha todos os campos.'
        ];
    }

    // Validar tamanhos
    if (
        mb_strlen($nome) > 100 ||
        mb_strlen($username) > 50 ||
        mb_strlen($email) > 150
    ) {
        return [
            'sucesso' => false,
            'mensagem' => 'Um ou mais campos ultrapassaram o limite permitido.'
        ];
    }

    // Validar e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'sucesso' => false,
            'mensagem' => 'Informe um endereço de e-mail válido.'
        ];
    }

    // Validar senha
    if (strlen($senha) < 8) {
        return [
            'sucesso' => false,
            'mensagem' => 'A senha deve possuir pelo menos 8 caracteres.'
        ];
    }

    // Confirmar senha
    if ($senha !== $confirmarSenha) {
        return [
            'sucesso' => false,
            'mensagem' => 'As senhas não coincidem.'
        ];
    }

    try {

        $this->autenticacaoDAO->cadastrar(
            $nome,
            $username,
            $email,
            $senha
        );

        return [
            'sucesso' => true,
            'mensagem' => 'Conta criada com sucesso!'
        ];

    } catch (PDOException $e) {

        // Identifica duplicidade de e-mail ou username
        if ($e->getCode() === '23000') {
            return [
                'sucesso' => false,
                'mensagem' => 'Este e-mail ou nome de usuário já está cadastrado.'
            ];
        }

        error_log($e->getMessage());

        return [
            'sucesso' => false,
            'mensagem' => 'Não foi possível realizar o cadastro.'
        ];
    }
}


    // Processa o login
    
public function login(array $dados): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $email = trim($dados['email'] ?? '');
    $senha = $dados['senha'] ?? '';

    // Verificar campos obrigatórios
    if ($email === '' || $senha === '') {
        return [
            'sucesso' => false,
            'mensagem' => 'Informe seu e-mail e sua senha.'
        ];
    }

    // Validar formato do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'sucesso' => false,
            'mensagem' => 'Informe um e-mail válido.'
        ];
    }

    try {

        // Buscar usuário no banco
        $usuario = $this->autenticacaoDAO->autenticar($email);

        // Verificar existência e senha
        if (
            !$usuario ||
            !password_verify($senha, $usuario['senha_hash'])
        ) {
            return [
                'sucesso' => false,
                'mensagem' => 'E-mail ou senha incorretos.'
            ];
        }

        // Verificar se a conta está ativa
        if ($usuario['status'] !== 'ativo') {
            return [
                'sucesso' => false,
                'mensagem' => 'Esta conta não está ativa.'
            ];
        }

        // Criar nova sessão autenticada
        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id' => (int) $usuario['id_usuario'],
            'id_usuario' => (int) $usuario['id_usuario'],
            'nome' => $usuario['nome'],
            'username' => $usuario['username'],
            'email' => $usuario['email'],
            'foto_perfil' => $usuario['foto_perfil'] ?? ''
        ];

        return [
            'sucesso' => true,
            'mensagem' => 'Login realizado com sucesso!'
        ];

    } catch (Throwable $e) {

        error_log($e->getMessage());

        return [
            'sucesso' => false,
            'mensagem' => 'Não foi possível realizar o login.'
        ];
    }
}


    // Atualiza os dados do perfil
   
public function atualizarPerfil(array $dados, array $arquivo = []): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['usuario']['id'])) {
        return [
            'sucesso' => false,
            'mensagem' => 'Você precisa estar conectado.'
        ];
    }

    $idUsuario = (int) $_SESSION['usuario']['id'];

    $nome = trim($dados['nome'] ?? '');
    $username = trim($dados['username'] ?? '');
    $email = trim($dados['email'] ?? '');

    if ($nome === '' || $username === '' || $email === '') {
        return [
            'sucesso' => false,
            'mensagem' => 'Preencha todos os campos.'
        ];
    }

    if (
        mb_strlen($nome) > 100 ||
        mb_strlen($username) > 50 ||
        mb_strlen($email) > 150
    ) {
        return [
            'sucesso' => false,
            'mensagem' => 'Um ou mais campos ultrapassaram o limite permitido.'
        ];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'sucesso' => false,
            'mensagem' => 'Informe um e-mail válido.'
        ];
    }

    $fotoPerfil = null;
    $caminhoFotoAntiga = $_SESSION['usuario']['foto_perfil'] ?? '';

    try {

        // Processar nova foto, caso tenha sido enviada
        if (
            !empty($arquivo['foto_perfil']) &&
            $arquivo['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            if ($arquivo['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Erro ao enviar a foto.'
                ];
            }

            if ($arquivo['foto_perfil']['size'] > 5 * 1024 * 1024) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'A foto deve ter no máximo 5 MB.'
                ];
            }

            $arquivoTemporario = $arquivo['foto_perfil']['tmp_name'];

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $tipoArquivo = $finfo->file($arquivoTemporario);

            $extensoesPermitidas = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($extensoesPermitidas[$tipoArquivo])) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Use uma imagem JPG, PNG ou WEBP.'
                ];
            }

            $pastaDestino = __DIR__ . '/../../public/img/perfis/';

            if (!is_dir($pastaDestino)) {
                mkdir($pastaDestino, 0755, true);
            }

            $extensao = $extensoesPermitidas[$tipoArquivo];
            $nomeArquivo = 'usuario_' . $idUsuario . '.' . $extensao;

            if (!move_uploaded_file(
                $arquivoTemporario,
                $pastaDestino . $nomeArquivo
            )) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Não foi possível salvar a foto.'
                ];
            }

            $fotoPerfil = 'img/perfis/' . $nomeArquivo;
        }

        // Salvar alterações no MySQL
        $this->autenticacaoDAO->atualizarPerfil(
            $idUsuario,
            $nome,
            $username,
            $email,
            $fotoPerfil
        );

        // Atualizar os dados da sessão
        $_SESSION['usuario']['nome'] = $nome;
        $_SESSION['usuario']['username'] = $username;
        $_SESSION['usuario']['email'] = $email;

        if ($fotoPerfil !== null) {
            $_SESSION['usuario']['foto_perfil'] = $fotoPerfil;
        }

        // Remover foto antiga somente após salvar a nova
        if (
            $fotoPerfil !== null &&
            $caminhoFotoAntiga !== '' &&
            $caminhoFotoAntiga !== $fotoPerfil
        ) {
            $arquivoAntigo = __DIR__ . '/../../public/' . $caminhoFotoAntiga;

            if (is_file($arquivoAntigo)) {
                unlink($arquivoAntigo);
            }
        }

        return [
            'sucesso' => true,
            'mensagem' => 'Perfil atualizado com sucesso!'
        ];

    } catch (PDOException $e) {

        if ($e->getCode() === '23000') {
            return [
                'sucesso' => false,
                'mensagem' => 'Este e-mail ou nome de usuário já está em uso.'
            ];
        }

        error_log($e->getMessage());

        return [
            'sucesso' => false,
            'mensagem' => 'Não foi possível atualizar o perfil.'
        ];

    } catch (Throwable $e) {

        error_log($e->getMessage());

        return [
            'sucesso' => false,
            'mensagem' => 'Ocorreu um erro ao atualizar o perfil.'
        ];
    }
}


    // Encerra a sessão
    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();
    }
}
