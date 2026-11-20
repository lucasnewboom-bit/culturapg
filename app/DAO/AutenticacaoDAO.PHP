
<?php

require_once __DIR__ . '/../Database/Conexao.php';

class AutenticacaoDAO
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    // Cadastra um novo usuário e suas credenciais
    public function cadastrar(
        string $nome,
        string $username,
        string $email,
        string $senha
    ): int {

        try {
            $this->conexao->beginTransaction();

            $sqlUsuario = "
                INSERT INTO USUARIO (nome, username)
                VALUES (:nome, :username)
            ";

            $stmtUsuario = $this->conexao->prepare($sqlUsuario);

            $stmtUsuario->execute([
                ':nome' => $nome,
                ':username' => $username
            ]);

            $usuarioId = (int) $this->conexao->lastInsertId();

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sqlCadastro = "
                INSERT INTO CADASTRO
                    (usuario_id, email, senha_hash, tipo_login)
                VALUES
                    (:usuario_id, :email, :senha_hash, :tipo_login)
            ";

            $stmtCadastro = $this->conexao->prepare($sqlCadastro);

            $stmtCadastro->execute([
                ':usuario_id' => $usuarioId,
                ':email' => $email,
                ':senha_hash' => $senhaHash,
                ':tipo_login' => 'email_senha'
            ]);

            $this->conexao->commit();

            return $usuarioId;

        } catch (Throwable $e) {

            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw $e;
        }
    }

    // Busca usuário para autenticação
    public function autenticar(string $email): ?array
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.nome,
                u.username,
                u.status,
                u.foto_perfil,
                c.email,
                c.senha_hash
            FROM USUARIO u
            INNER JOIN CADASTRO c
                ON c.usuario_id = u.id_usuario
            WHERE c.email = :email
            LIMIT 1
        ";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    // Atualiza os dados do usuário e suas credenciais
    public function atualizarPerfil(
        int $idUsuario,
        string $nome,
        string $username,
        string $email,
        ?string $fotoPerfil = null
    ): void {

        try {

            $this->conexao->beginTransaction();

            // Atualizar dados do usuário
            if ($fotoPerfil !== null) {

                $sqlUsuario = "
                    UPDATE USUARIO
                    SET nome = :nome,
                        username = :username,
                        foto_perfil = :foto
                    WHERE id_usuario = :id
                ";

            } else {

                $sqlUsuario = "
                    UPDATE USUARIO
                    SET nome = :nome,
                        username = :username
                    WHERE id_usuario = :id
                ";

            }

            $stmtUsuario = $this->conexao->prepare($sqlUsuario);

            $parametrosUsuario = [
                ':nome' => $nome,
                ':username' => $username,
                ':id' => $idUsuario
            ];

            if ($fotoPerfil !== null) {
                $parametrosUsuario[':foto'] = $fotoPerfil;
            }

            $stmtUsuario->execute($parametrosUsuario);

            // Atualizar e-mail
            $sqlCadastro = "
                UPDATE CADASTRO
                SET email = :email
                WHERE usuario_id = :id
            ";

            $stmtCadastro = $this->conexao->prepare($sqlCadastro);

            $stmtCadastro->execute([
                ':email' => $email,
                ':id' => $idUsuario
            ]);

            $this->conexao->commit();

        } catch (Throwable $e) {

            if ($this->conexao->inTransaction()) {
                $this->conexao->rollBack();
            }

            throw $e;
        }
    }

}
