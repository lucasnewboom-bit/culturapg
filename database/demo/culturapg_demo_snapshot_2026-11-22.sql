-- CulturaPG demo snapshot exported 2026-11-22. Contains test users and test data.
CREATE DATABASE IF NOT EXISTS `culturapg_demo` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `culturapg_demo`;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `atualizacao_evento`;
CREATE TABLE `atualizacao_evento` (
  `id_atualizacao` int NOT NULL AUTO_INCREMENT,
  `evento_id` int NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensagem` text NOT NULL,
  `data_atualizacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_atualizacao`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `atualizacao_evento_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id_evento`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `atualizacao_evento` (`id_atualizacao`, `evento_id`, `titulo`, `mensagem`, `data_atualizacao`) VALUES
('1', '1', 'Informacao sobre ingressos', 'Os ingressos estao disponiveis para compra pelos canais do organizador.', '2026-11-12 10:00:00'),
('2', '2', 'Confirmacao das apresentacoes', 'As apresentacoes musicais foram confirmadas. A entrada continua gratuita.', '2026-11-12 11:00:00'),
('3', '3', 'Aviso ao publico', 'Recomendamos chegar com pelo menos 30 minutos de antecedencia.', '2026-11-12 12:00:00'),
('5', '14', 'DEMO - Altera??o de hor?rio', 'A programa??o da demonstra??o foi atualizada.', '2026-11-22 04:55:24');

DROP TABLE IF EXISTS `cadastro`;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `cadastro` (`id_cadastro`, `usuario_id`, `email`, `senha_hash`, `tipo_login`, `data_cadastro`) VALUES
('1', '1', 'mariana@email.com', NULL, 'google', '2026-10-01 10:00:00'),
('2', '2', 'pedro@email.com', NULL, 'google', '2026-10-02 11:00:00'),
('3', '3', 'ana@email.com', NULL, 'apple', '2026-10-03 12:00:00'),
('4', '4', 'lucas@email.com', NULL, 'google', '2026-10-04 13:00:00'),
('5', '5', 'julia@email.com', NULL, 'google', '2026-10-05 14:00:00'),
('6', '6', 'teste01@exemplo.com', '$2y$10$I3QUenUPoRX16G5jbXcq7uBKqt4bEpN2xvYq/uccSPBbqhKp3a7pO', 'email_senha', '2026-11-19 20:15:51'),
('7', '7', 'testeusuario@gmail.com', '$2y$10$0JO3Bb2kF6YpxagqvsvhR.8TIOzWD/IBWcuBpsUMplwxCWetjDdVi', 'email_senha', '2026-11-21 14:52:24'),
('8', '8', 'testeorganizador@gmail.com', '$2y$10$J6.a6xVOtxC0B7eCL0aYlOhql3MTWa.1Ek4r4RBZcGw8Wf1UPEVZe', 'email_senha', '2026-11-21 14:53:10'),
('9', '9', 'demo.organizador.20261122@example.test', '$2y$10$n5SU3.gX0NxTsRXrOo41TOlV4bsUKJJZdiQfwoazHFHDsZ623UVry', 'email_senha', '2026-11-22 04:48:57'),
('10', '10', 'demo.visitante.20261122@example.test', '$2y$10$2JLG.IGp/zIShWoUuY4g9exFvZJTSIEcETGdOgxGWG3Y3aL1Laufy', 'email_senha', '2026-11-22 04:48:57');

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE `categoria` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categoria` (`id_categoria`, `nome`, `ativo`) VALUES
('1', 'Danca', '1'),
('2', 'Musica', '1'),
('3', 'Teatro', '1');

DROP TABLE IF EXISTS `cidade`;
CREATE TABLE `cidade` (
  `id_cidade` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `uf` char(2) NOT NULL,
  PRIMARY KEY (`id_cidade`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `cidade` (`id_cidade`, `nome`, `estado`, `uf`) VALUES
('1', 'Ponta Grossa', 'Parana', 'PR');

DROP TABLE IF EXISTS `data_evento`;
CREATE TABLE `data_evento` (
  `id_data` int NOT NULL AUTO_INCREMENT,
  `evento_id` int NOT NULL,
  `data_evento` date NOT NULL,
  `horario_inicio` time NOT NULL,
  `horario_fim` time DEFAULT NULL,
  PRIMARY KEY (`id_data`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `data_evento_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id_evento`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `data_evento` (`id_data`, `evento_id`, `data_evento`, `horario_inicio`, `horario_fim`) VALUES
('1', '1', '2026-11-21', '19:30:00', '21:00:00'),
('2', '2', '2026-11-27', '18:00:00', '20:00:00'),
('3', '3', '2026-12-04', '20:00:00', '21:30:00'),
('4', '4', '2026-12-12', '16:00:00', '17:30:00'),
('5', '5', '2026-12-19', '19:00:00', '21:00:00'),
('6', '6', '2026-11-30', '18:00:00', '21:00:00'),
('13', '11', '2026-12-12', '08:00:00', '19:00:00'),
('16', '12', '2026-12-01', '18:00:00', '21:00:00'),
('17', '13', '2026-11-27', '19:00:00', '20:30:00'),
('18', '13', '2026-12-04', '19:00:00', '20:30:00'),
('21', '14', '2026-11-27', '19:00:00', '20:30:00'),
('22', '14', '2026-12-04', '19:00:00', '20:30:00');

DROP TABLE IF EXISTS `evento`;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `evento` (`id_evento`, `organizador_id`, `categoria_id`, `local_id`, `titulo`, `descricao`, `valor`, `gratuito`, `tipo_contato`, `link_contato`, `classificacao_indicativa`, `imagem`, `status`) VALUES
('1', '1', '1', '2', 'Corpos que Falam', '\"Existe algo em n?s que precisa ser dito. algo que o corpo grita sem palavras. Voc? j? ouviu o que o seu corpo tem a dizer?\"\n\nO Studio Alan Oliveira apresenta o espet?culo \"Corpos que Falam\".\n\nA proposta do espet?culo convida o p?blico a refletir sobre a linguagem do corpo e o poder da dan?a como forma de express?o. Inspirado na frase de Martha Graham - \"A dan?a ? a linguagem oculta da alma\" - o espet?culo busca romper padr?es e dar voz a todos os corpos.\n\nEm \"Corpos que Falam\", a dan?a ? apresentada como uma celebra??o da diversidade, da inclus?o e da liberdade. Cada corpo tem sua pr?pria hist?ria e pot?ncia, mostrando que todos podem dan?ar, independentemente de idade, forma f?sica ou limita??es.', '25.00', '0', 'instagram', 'https://example.com/corpos-que-falam', 'Livre', NULL, 'ativo'),
('2', '2', '2', '4', 'Sexta as Seis', 'Come?a na pr?xima sexta-feira o Projeto Sexta ?s Seis 2026.\n\nA programa??o inicia, ?s 18 horas, com uma apresenta??o especial de abertura da Banda Escola Lyra dos Campos.\n\nO Projeto Sexta ?s Seis ? um dos eventos mais tradicionais e esperados pela comunidade, reunindo m?sicos e p?blico em uma programa??o voltada ? produ??o musical local.', '0.00', '1', 'instagram', 'https://example.com/sexta-as-seis', 'Livre', NULL, 'ativo'),
('3', '3', '3', '1', 'Aquele Que Diz Sim, Aquele Que Diz Nao', 'A montagem ? uma famosa obra do dramaturgo alem?o Bertolt Brecht, apresentada pelo Grupo de Teatro Universit?rio (GTU) da UEPG (Universidade Estadual de Ponta Grossa).\n\nA pe?a ? uma \"pe?a did?tica\" do teatro ?pico que debate a obedi?ncia cega ?s tradi??es, o livre-arb?trio e o peso das escolhas coletivas e individuais.', '30.00', '0', 'whatsapp', 'https://example.com/aquele-que-diz-sim', '12', NULL, 'ativo'),
('4', '1', '3', '3', 'Bugigangas', '\"Hist?ria cheia de bicho estranho, cara feia, riso alto e express?o de ogro que at? pedra vai rir.\"\n\nBugiganga ? uma com?dia teatral do Grupo de Teatro de Ponta Grossa (GTPG), encenada em espa?os tradicionais da cidade, como o Cine-Teatro ?pera.\n\nA obra explora o estilo nonsense, tanto na hist?ria quanto em suas caricaturas, incluindo caracter?sticas de buf?o e misturando linguagens c?nicas para divertir o p?blico com objetos inusitados e muito humor.', '20.00', '0', 'instagram', 'https://example.com/bugigangas', 'Livre', NULL, 'ativo'),
('5', '2', '2', '4', 'Sinfonia das Aguas', 'Formada por 22 musicistas, a orquestra Ladies Ensemble, com uma estreia nacional em Ponta Grossa, apresenta um concerto que mescla inspira??es do elemento ?gua com uma hist?ria de amor.\n\nEssa hist?ria, por sua vez, foi baseada em fatos reais e retrata a saga dos imigrantes europeus que participaram da constru??o do Brasil e deixaram marcas profundas e hist?rias ligadas ao Paran? e, em especial, a Ponta Grossa e ? Regi?o dos Campos Gerais.', '0.00', '1', 'instagram', 'https://example.com/sinfonia-das-aguas', 'Livre', NULL, 'ativo'),
('6', '3', '1', '1', 'Mostra de Talentos dos Campos Gerais', 'A arte ganha vida quando diferentes talentos encontram um espa?o para se expressar.\n\nA Mostra de Talentos dos Campos Gerais re?ne artistas e participantes em uma celebra??o da criatividade, da diversidade e da express?o cultural.\n\nO evento busca valorizar os talentos da regi?o, proporcionando ao p?blico a oportunidade de conhecer diferentes manifesta??es art?sticas e prestigiar a produ??o cultural dos Campos Gerais.\n\nMais do que uma apresenta??o, a mostra representa um espa?o de encontro, reconhecimento e incentivo ? arte local, dando visibilidade ?queles que encontram na express?o art?stica uma forma de compartilhar suas hist?rias e habilidades.', '15.00', '0', 'whatsapp', 'https://example.com/mostra-de-talentos', 'Livre', NULL, 'ativo'),
('11', '4', '1', '9', 'Teste evento', 'Esse é um teste para verificar se é possivel criar evento através das telas', '0.00', '0', 'whatsapp', NULL, NULL, NULL, 'inativo'),
('12', '4', '1', '9', 'Teste evento', 'vbnvbnvbnvbnvnvbnv', '0.00', '1', 'whatsapp', NULL, '10', 'img/demo-evento.png', 'ativo'),
('13', '5', '3', '10', 'DEMO - Mostra cultural de dezembro', 'Evento de demonstra??o para apresenta??o acad?mica.', '0.00', '1', 'site', 'https://example.test/culturapg', 'Livre', 'img/demo-evento.png', 'inativo'),
('14', '5', '3', '10', 'DEMO - Mostra cultural paga', 'Evento de demonstra??o para apresenta??o acad?mica.', '25.00', '0', 'site', 'https://example.test/culturapg', 'Livre', 'img/demo-evento.png', 'ativo');

DROP TABLE IF EXISTS `favorito`;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `favorito` (`id_favorito`, `usuario_id`, `evento_id`, `data_favorito`) VALUES
('1', '4', '1', '2026-11-10 14:00:00'),
('2', '4', '2', '2026-11-10 15:00:00'),
('3', '5', '3', '2026-11-11 10:00:00'),
('4', '5', '5', '2026-11-11 11:00:00'),
('5', '4', '6', '2026-11-11 12:00:00'),
('8', '6', '1', '2026-11-19 22:03:33'),
('10', '8', '2', '2026-11-22 03:12:28'),
('12', '10', '14', '2026-11-22 04:57:23');

DROP TABLE IF EXISTS `historico`;
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `historico` (`id_historico`, `usuario_id`, `evento_id`, `data_visualizacao`) VALUES
('1', '4', '1', '2026-11-11 14:00:00'),
('2', '4', '2', '2026-11-11 15:00:00'),
('3', '4', '6', '2026-11-11 16:00:00'),
('4', '5', '3', '2026-11-11 17:00:00'),
('5', '5', '5', '2026-11-11 18:00:00'),
('10', '8', '11', '2026-11-21 14:55:33'),
('11', '8', '5', '2026-11-22 03:08:32'),
('12', '8', '5', '2026-11-22 03:08:35'),
('13', '8', '6', '2026-11-22 03:08:40'),
('14', '8', '11', '2026-11-22 03:10:00'),
('15', '8', '12', '2026-11-22 03:10:03'),
('16', '8', '2', '2026-11-22 03:10:21'),
('17', '8', '12', '2026-11-22 03:11:39'),
('18', '8', '2', '2026-11-22 03:12:27'),
('19', '8', '2', '2026-11-22 03:12:28'),
('20', '8', '2', '2026-11-22 03:12:28'),
('21', '8', '12', '2026-11-22 03:12:35'),
('22', '8', '12', '2026-11-22 03:12:55'),
('23', '10', '13', '2026-11-22 04:52:47'),
('24', '10', '14', '2026-11-22 04:54:02'),
('25', '10', '14', '2026-11-22 04:55:24'),
('26', '10', '14', '2026-11-22 04:57:23');

DROP TABLE IF EXISTS `link_evento`;
CREATE TABLE `link_evento` (
  `id_link` int NOT NULL AUTO_INCREMENT,
  `evento_id` int NOT NULL,
  `tipo_link` enum('whatsapp','instagram','sympla') NOT NULL,
  `url` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_link`),
  KEY `fk_link_evento` (`evento_id`),
  CONSTRAINT `fk_link_evento` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `link_evento` (`id_link`, `evento_id`, `tipo_link`, `url`) VALUES
('1', '4', 'instagram', NULL),
('2', '1', 'whatsapp', NULL),
('3', '1', 'instagram', NULL),
('4', '6', 'sympla', NULL),
('5', '3', 'whatsapp', NULL),
('6', '3', 'instagram', NULL);

DROP TABLE IF EXISTS `local`;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `local` (`id_local`, `cidade_id`, `nome`, `endereco`, `latitude`, `longitude`, `ativo`) VALUES
('1', '1', 'Cine Teatro Opera', 'Rua XV de Novembro, 468 - Centro, Ponta Grossa - PR', '-25.0964720', '-50.1601390', '1'),
('2', '1', 'Faculdade Sant Ana', 'Rua Senador Pinheiro Machado, 189 - Centro, Ponta Grossa - PR', '-25.0914535', '-50.1605148', '1'),
('3', '1', 'Cine Marista - Teatro Marista Pio XII', 'Rua Rodrigues Alves, 701 - Ponta Grossa - PR', '-25.0825200', '-50.1615400', '1'),
('4', '1', 'Concha Acustica', 'Praca Barao do Rio Branco - Centro, Ponta Grossa - PR', '-25.0936330', '-50.1605670', '1'),
('9', '1', 'Cine-Teatro Ópera', 'R. Quinze de Novembro, 468 - Centro, Ponta Grossa - PR, 84010-020', '-25.0964327', '-50.1601531', '1'),
('10', '1', 'Concha Ac?stica - Demo', 'Rua General Carneiro, Centro, Ponta Grossa - PR', '-25.0936330', '-50.1605670', '1');

DROP TABLE IF EXISTS `mapa`;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mapa` (`id_mapa`, `cidade_id`, `latitude_central`, `longitude_central`, `nivel_zoom`, `status`) VALUES
('1', '1', '-25.0950000', '-50.1610000', '13', 'ativo');

DROP TABLE IF EXISTS `notificacao`;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `notificacao` (`id_notificacao`, `usuario_id`, `atualizacao_id`, `lida`, `data_envio`) VALUES
('1', '4', '1', '0', '2026-11-12 10:05:00'),
('2', '4', '2', '0', '2026-11-12 11:05:00'),
('3', '5', '3', '1', '2026-11-12 12:05:00'),
('5', '10', '5', '1', '2026-11-22 04:55:24');

DROP TABLE IF EXISTS `organizador`;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `organizador` (`id_organizador`, `usuario_id`, `cnpj`, `tipo_organizador`, `verificado`, `data_verificacao`, `status`) VALUES
('1', '1', NULL, 'Grupo cultural', '1', '2026-10-10 09:00:00', 'ativo'),
('2', '2', NULL, 'Produtor independente', '1', '2026-10-11 10:00:00', 'ativo'),
('3', '3', NULL, 'Instituicao cultural', '1', '2026-10-12 11:00:00', 'ativo'),
('4', '8', NULL, 'Produtor', '0', NULL, 'ativo'),
('5', '9', NULL, 'Coletivo cultural', '0', NULL, 'ativo');

DROP TABLE IF EXISTS `telefone`;
CREATE TABLE `telefone` (
  `id_telefone` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `numero` varchar(20) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_telefone`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `telefone_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `telefone` (`id_telefone`, `usuario_id`, `numero`, `tipo`, `principal`) VALUES
('1', '1', '(42) 99911-1111', 'celular', '1'),
('2', '2', '(42) 99922-2222', 'celular', '1'),
('3', '4', '(42) 99933-3333', 'celular', '1');

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  `foto_perfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `usuario` (`id_usuario`, `nome`, `username`, `status`, `foto_perfil`) VALUES
('1', 'Mariana Costa', 'marianacosta', 'ativo', NULL),
('2', 'Pedro Henrique', 'pedrohenrique', 'ativo', NULL),
('3', 'Ana Martins', 'anamartins', 'ativo', NULL),
('4', 'Lucas Ferreira', 'lucasf', 'ativo', NULL),
('5', 'Julia Almeida', 'juliaa', 'ativo', NULL),
('6', 'Usu?rio Teste luquinhas1234', 'usuario_teste01', 'ativo', 'img/perfis/usuario_6.png'),
('7', 'Lucas Spinassi', 'lucasspi', 'ativo', NULL),
('8', 'Lucas hm Spinassi', 'lucashms', 'ativo', NULL),
('9', 'Organizador Demonstra??o Atualizado', 'demo_org_20261122', 'ativo', 'img/demo-perfil.png'),
('10', 'Visitante Demonstração', 'demo_visit_20261122', 'ativo', NULL);

SET FOREIGN_KEY_CHECKS=1;
