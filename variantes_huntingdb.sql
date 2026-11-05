USE huntingdb;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Variantes dos artigos (cor, tamanho, material)';

INSERT INTO variantes(artigo_id,cor,tamanho,material,stock) VALUES
(1,'Verde','S','Softshell',5),(1,'Verde','M','Softshell',8),(1,'Verde','L','Softshell',8),(1,'Verde','XL','Softshell',4),
(2,'Verde','38','Poliéster reforçado',6),(2,'Verde','40','Poliéster reforçado',7),(2,'Verde','42','Poliéster reforçado',7),(2,'Castanho','42','Poliéster reforçado',3),(2,'Castanho','44','Poliéster reforçado',3),
(3,'Laranja','Único','Poliéster',20),
(4,'Verde','M','Lã merino',6),(4,'Cinzento','M','Lã merino',4),(4,'Verde','L','Lã merino',6),(4,'Cinzento','L','Lã merino',4),
(5,'Camuflado','M','Nylon impermeável',3),(5,'Camuflado','L','Nylon impermeável',5),(5,'Camuflado','XL','Nylon impermeável',3),
(6,'Preto','M','Neoprene',10),(6,'Preto','L','Neoprene',10),(6,'Verde','L','Neoprene',5),
(7,'Castanho','40','Couro',4),(7,'Castanho','42','Couro',5),(7,'Castanho','44','Couro',4),
(8,'Verde','41','Borracha e neoprene',5),(8,'Verde','43','Borracha e neoprene',5),
(9,'Preto','39','Borracha',6),(9,'Preto','42','Borracha',6),
(10,'Verde','39-42','Lã',15),(10,'Verde','43-46','Lã',15),
(11,'Verde','Único','Cordura',8),
(18,'Laranja','S','Kevlar',4),(18,'Laranja','M','Kevlar',6),(18,'Laranja','L','Kevlar',4),
(19,'Laranja','M',NULL,10),(19,'Amarelo','M',NULL,10),
(34,'Verde','30L','Cordura',6),(34,'Camuflado','30L','Cordura',4),
(35,'Preto','Único','Nylon',10),(35,'Camuflado','Único','Nylon',8),
(37,'Verde','2L',NULL,7),
(39,'Inox','500 ml','Aço inox',12),(39,'Verde','750 ml','Aço inox',8);
