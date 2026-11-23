<?php

// Gera um script de estrutura sem copiar dados pessoais ou de eventos.
require_once __DIR__ . '/../app/Database/Conexao.php';

$conexao = Conexao::conectar();
$tabelas = $conexao->query(
    "SELECT TABLE_NAME
     FROM information_schema.TABLES
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'
     ORDER BY TABLE_NAME"
)->fetchAll(PDO::FETCH_COLUMN);

$linhasDependencias = $conexao->query(
    'SELECT TABLE_NAME, REFERENCED_TABLE_NAME
     FROM information_schema.KEY_COLUMN_USAGE
     WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL'
)->fetchAll(PDO::FETCH_ASSOC);

$dependencias = [];
foreach ($linhasDependencias as $linha) {
    $dependencias[$linha['TABLE_NAME']][] = $linha['REFERENCED_TABLE_NAME'];
}

$pendentes = $tabelas;
$ordem = [];
while ($pendentes) {
    $progrediu = false;

    foreach ($pendentes as $indice => $tabela) {
        $pais = array_diff(array_unique($dependencias[$tabela] ?? []), [$tabela]);

        if (!array_intersect($pais, $pendentes)) {
            $ordem[] = $tabela;
            array_splice($pendentes, $indice, 1);
            $progrediu = true;
            break;
        }
    }

    // Tabelas com relações circulares são criadas com as verificações temporariamente suspensas.
    if (!$progrediu) {
        $ordem = array_merge($ordem, $pendentes);
        break;
    }
}

$sql = "-- CulturaPG: estrutura sem dados de contas ou eventos.\n";
$sql .= "CREATE DATABASE IF NOT EXISTS `culturapg` CHARACTER SET utf8mb4;\n";
$sql .= "USE `culturapg`;\nSET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\n\n";

foreach ($ordem as $tabela) {
    $tabelaSql = '`' . str_replace('`', '``', $tabela) . '`';
    $resultado = $conexao->query('SHOW CREATE TABLE ' . $tabelaSql)->fetch(PDO::FETCH_NUM);
    $ddl = preg_replace('/ AUTO_INCREMENT=\d+/i', '', $resultado[1]);
    $sql .= $ddl . ";\n\n";
}

$cidade = $conexao->query(
    'SELECT id_cidade, nome, estado, uf FROM CIDADE WHERE id_cidade = 1'
)->fetch(PDO::FETCH_ASSOC);

if (!$cidade) {
    throw new RuntimeException('A cidade fixa com id_cidade = 1 não foi encontrada.');
}

$sql .= "-- Registro fixo de Ponta Grossa usado pelos novos eventos.\n";
$sql .= 'INSERT IGNORE INTO `CIDADE` (`id_cidade`, `nome`, `estado`, `uf`) VALUES ('
    . (int) $cidade['id_cidade'] . ', '
    . $conexao->quote($cidade['nome']) . ', '
    . $conexao->quote($cidade['estado']) . ', '
    . $conexao->quote($cidade['uf']) . ");\n\n";

$categorias = $conexao->query(
    'SELECT id_categoria, nome, ativo FROM CATEGORIA WHERE id_categoria IN (1, 2, 3) AND criado_por_usuario_id IS NULL ORDER BY id_categoria'
)->fetchAll(PDO::FETCH_ASSOC);

$sql .= "-- Categorias disponíveis no catálogo.\n";
foreach ($categorias as $categoria) {
    $sql .= 'INSERT IGNORE INTO `CATEGORIA` (`id_categoria`, `nome`, `ativo`) VALUES ('
        . (int) $categoria['id_categoria'] . ', '
        . $conexao->quote($categoria['nome']) . ', '
        . (int) $categoria['ativo'] . ");\n";
}

$sql .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";
$destino = __DIR__ . '/setup.sql';
if (file_put_contents($destino, $sql) === false) {
    throw new RuntimeException('Não foi possível salvar database/setup.sql.');
}

echo 'Estrutura gerada (' . count($tabelas) . ' tabelas, ' . count($categorias) . " categorias).\n";
