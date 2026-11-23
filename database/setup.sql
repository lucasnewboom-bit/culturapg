-- CulturaPG: estrutura sem dados de contas ou eventos.
CREATE DATABASE IF NOT EXISTS `culturapg` CHARACTER SET utf8mb4;
USE `culturapg`;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `cidade` (
  `id_cidade` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `uf` char(2) NOT NULL,
  PRIMARY KEY (`id_cidade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `local` (
  `id_local` int NOT NULL AUTO_INCREMENT,
  `cidade_id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_local`),
  KEY `cidade_id` (`cidade_id`),
  CONSTRAINT `local_ibfk_1` FOREIGN KEY (`cidade_id`) REFERENCES `cidade` (`id_cidade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `mapa` (
  `id_mapa` int NOT NULL AUTO_INCREMENT,
  `cidade_id` int NOT NULL,
  `latitude_central` decimal(10,7) NOT NULL,
  `longitude_central` decimal(10,7) NOT NULL,
  `nivel_zoom` int NOT NULL DEFAULT '13',
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id_mapa`),
  UNIQUE KEY `cidade_id` (`cidade_id`),
  CONSTRAINT `mapa_ibfk_1` FOREIGN KEY (`cidade_id`) REFERENCES `cidade` (`id_cidade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  `foto_perfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `cadastro` (
  `id_cadastro` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha_hash` varchar(255) DEFAULT NULL,
  `tipo_login` varchar(20) NOT NULL,
  `data_cadastro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cadastro`),
  UNIQUE KEY `usuario_id` (`usuario_id`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `cadastro_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `categoria` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `criado_por_usuario_id` int DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nome` (`nome`),
  KEY `idx_categoria_criado_por_usuario` (`criado_por_usuario_id`),
  CONSTRAINT `fk_categoria_criador_usuario` FOREIGN KEY (`criado_por_usuario_id`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `organizador` (
  `id_organizador` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `cnpj` char(14) DEFAULT NULL,
  `tipo_organizador` varchar(50) NOT NULL,
  `verificado` tinyint(1) NOT NULL DEFAULT '0',
  `data_verificacao` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id_organizador`),
  UNIQUE KEY `usuario_id` (`usuario_id`),
  UNIQUE KEY `cnpj` (`cnpj`),
  CONSTRAINT `organizador_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `evento` (
  `id_evento` int NOT NULL AUTO_INCREMENT,
  `organizador_id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `local_id` int NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` text,
  `valor` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gratuito` tinyint(1) NOT NULL DEFAULT '0',
  `tipo_contato` varchar(30) DEFAULT NULL,
  `link_contato` varchar(500) DEFAULT NULL,
  `classificacao_indicativa` varchar(10) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id_evento`),
  KEY `organizador_id` (`organizador_id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `local_id` (`local_id`),
  CONSTRAINT `evento_ibfk_1` FOREIGN KEY (`organizador_id`) REFERENCES `organizador` (`id_organizador`),
  CONSTRAINT `evento_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id_categoria`),
  CONSTRAINT `evento_ibfk_3` FOREIGN KEY (`local_id`) REFERENCES `local` (`id_local`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `atualizacao_evento` (
  `id_atualizacao` int NOT NULL AUTO_INCREMENT,
  `evento_id` int NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensagem` text NOT NULL,
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_atualizacao`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `atualizacao_evento_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `data_evento` (
  `id_data` int NOT NULL AUTO_INCREMENT,
  `evento_id` int NOT NULL,
  `data_evento` date NOT NULL,
  `horario_inicio` time NOT NULL,
  `horario_fim` time DEFAULT NULL,
  PRIMARY KEY (`id_data`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `data_evento_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `favorito` (
  `id_favorito` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `evento_id` int NOT NULL,
  `data_favorito` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_favorito`),
  UNIQUE KEY `usuario_id` (`usuario_id`,`evento_id`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `favorito_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `favorito_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `historico` (
  `id_historico` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `evento_id` int NOT NULL,
  `data_visualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historico`),
  KEY `usuario_id` (`usuario_id`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `historico_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `historico_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `link_evento` (
  `id_link` int NOT NULL AUTO_INCREMENT,
  `evento_id` int NOT NULL,
  `tipo_link` enum('whatsapp','instagram','sympla') NOT NULL,
  `url` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_link`),
  KEY `fk_link_evento` (`evento_id`),
  CONSTRAINT `fk_link_evento` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `notificacao` (
  `id_notificacao` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `atualizacao_id` int NOT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `data_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notificacao`),
  KEY `usuario_id` (`usuario_id`),
  KEY `atualizacao_id` (`atualizacao_id`),
  CONSTRAINT `notificacao_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `notificacao_ibfk_2` FOREIGN KEY (`atualizacao_id`) REFERENCES `atualizacao_evento` (`id_atualizacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `telefone` (
  `id_telefone` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `numero` varchar(20) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_telefone`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `telefone_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Registro fixo de Ponta Grossa usado pelos novos eventos.
INSERT IGNORE INTO `CIDADE` (`id_cidade`, `nome`, `estado`, `uf`) VALUES (1, 'Ponta Grossa', 'Parana', 'PR');

-- Categorias disponíveis no catálogo.
INSERT IGNORE INTO `CATEGORIA` (`id_categoria`, `nome`, `ativo`) VALUES (1, 'Danca', 1);
INSERT IGNORE INTO `CATEGORIA` (`id_categoria`, `nome`, `ativo`) VALUES (2, 'Musica', 1);
INSERT IGNORE INTO `CATEGORIA` (`id_categoria`, `nome`, `ativo`) VALUES (3, 'Teatro', 1);

SET FOREIGN_KEY_CHECKS = 1;
