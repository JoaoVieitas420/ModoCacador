/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/ huntingdb /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE huntingdb;

DROP TABLE IF EXISTS categorias;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `categoria` varchar(255) DEFAULT NULL COMMENT 'Categoria',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Tabela de categorias';

DROP TABLE IF EXISTS artigos;
CREATE TABLE `artigos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `nome` varchar(255) DEFAULT NULL COMMENT 'Nome do artigo',
  `preco` decimal(10,2) DEFAULT NULL COMMENT 'Preco em euros',
  `avaliacao` varchar(255) DEFAULT NULL COMMENT 'Avaliacao',
  `ano` varchar(255) DEFAULT NULL COMMENT 'Ano do modelo',
  `imagem` varchar(4000) DEFAULT NULL COMMENT 'Imagem do artigo',
  `categoria_id` int(11) DEFAULT NULL COMMENT 'FK para categorias',
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `artigos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Tabela de artigos de caca';

DROP TABLE IF EXISTS utilizador;
CREATE TABLE `utilizador` (
  `id_utilizador` varchar(255) NOT NULL COMMENT 'primary key',
  `palavra_passe` varchar(400) DEFAULT NULL COMMENT 'Password',
  `nome` varchar(400) DEFAULT NULL COMMENT 'Name',
  `fotografia` varchar(4000) DEFAULT NULL COMMENT 'Photo',
  `ativo` bit(1) DEFAULT NULL,
  `admin` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Administrador (gere utilizadores)',
  `alterar_palavra_passe` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Obriga a alterar a palavra-passe no proximo acesso',
  PRIMARY KEY (`id_utilizador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='User table';

-- Utilizador inicial: admin / admin123 (hash bcrypt). Tem de alterar a palavra-passe no primeiro acesso.
INSERT INTO utilizador (id_utilizador, palavra_passe, nome, fotografia, ativo, admin, alterar_palavra_passe) VALUES
('admin', '$2y$10$3qoka1oxMOSo1cZaEASV3uk.ruq2tx/ap45lQv.1hGywgTj7K.VvG', 'Administrador', NULL, b'1', b'1', b'1');

DROP TABLE IF EXISTS variantes;
CREATE TABLE `variantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `artigo_id` int(11) NOT NULL COMMENT 'FK para artigos',
  `cor` varchar(100) DEFAULT NULL COMMENT 'Cor',
  `tamanho` varchar(50) DEFAULT NULL COMMENT 'Tamanho',
  `material` varchar(100) DEFAULT NULL COMMENT 'Material',
  `stock` int(11) NOT NULL DEFAULT 0 COMMENT 'Unidades em stock',
  PRIMARY KEY (`id`),
  KEY `artigo_id` (`artigo_id`),
  CONSTRAINT `variantes_ibfk_1` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Variantes dos artigos (cor, tamanho, material)';

DROP TABLE IF EXISTS imagens;
CREATE TABLE `imagens` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `artigo_id` int(11) NOT NULL COMMENT 'FK para artigos',
  `cor` varchar(100) DEFAULT NULL COMMENT 'Cor da variante a que a imagem corresponde (NULL = imagem geral do artigo)',
  `caminho` varchar(4000) NOT NULL COMMENT 'Caminho da imagem',
  `descricao` varchar(255) DEFAULT NULL COMMENT 'Descricao da imagem',
  `autor` varchar(255) DEFAULT NULL COMMENT 'Autor da imagem',
  `licenca` varchar(100) DEFAULT NULL COMMENT 'Licenca da imagem',
  `fonte` varchar(1000) DEFAULT NULL COMMENT 'URL de origem da imagem',
  `ordem` int(11) NOT NULL DEFAULT 1 COMMENT 'Ordem na galeria',
  PRIMARY KEY (`id`),
  KEY `artigo_id` (`artigo_id`),
  CONSTRAINT `imagens_ibfk_1` FOREIGN KEY (`artigo_id`) REFERENCES `artigos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Biblioteca de imagens dos artigos';

