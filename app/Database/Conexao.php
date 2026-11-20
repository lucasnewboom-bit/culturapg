
<?php

class Conexao {

    public static function conectar() {

        try {

            $caminhoConfiguracao = __DIR__ . '/../../config/database.local.php';
            $configuracao = is_file($caminhoConfiguracao)
                ? require $caminhoConfiguracao
                : [];

            $host = $configuracao['host'] ?? (getenv('CULTURAPG_DB_HOST') ?: 'localhost');
            $porta = (int) ($configuracao['port'] ?? (getenv('CULTURAPG_DB_PORT') ?: 3306));
            $banco = $configuracao['dbname'] ?? (getenv('CULTURAPG_DB_NAME') ?: 'culturapg');
            $usuario = $configuracao['user'] ?? (getenv('CULTURAPG_DB_USER') ?: 'root');
            $senha = $configuracao['password'] ?? (getenv('CULTURAPG_DB_PASSWORD') ?: '');

            $conexao = new PDO(
                "mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4",
                $usuario,
                $senha
            );

            $conexao->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $conexao;

        } catch (PDOException $e) {

            die("Erro na conexao: " . $e->getMessage());

        }
    }
}
