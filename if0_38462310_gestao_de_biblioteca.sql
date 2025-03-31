-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql308.infinityfree.com
-- Tempo de geração: 14/03/2025 às 05:28
-- Versão do servidor: 10.6.19-MariaDB
-- Versão do PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `if0_38462310_gestao_de_biblioteca`
--


CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `serie` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `professor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `alunos` (`id`, `nome`, `serie`, `email`, `senha`, `professor_id`) VALUES
(1, 'Alef de Souza Sobrinho', '3D', 'alefsouzasobrinho51@gmail.com', 'c024d8645565ab377d3fa52d54f543c4', NULL);


CREATE TABLE `emprestimos` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) DEFAULT NULL,
  `livro_id` int(11) DEFAULT NULL,
  `data_emprestimo` date NOT NULL,
  `data_devolucao` date DEFAULT NULL,
  `devolvido` varchar(50) NOT NULL,
  `professor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `emprestimos` (`id`, `aluno_id`, `livro_id`, `data_emprestimo`, `data_devolucao`, `devolvido`, `professor_id`) VALUES
(3, 1, 2, '2025-03-06', '2025-04-06', '0', NULL);


CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `capa_url` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `ano_publicacao` varchar(4) NOT NULL,
  `genero` varchar(100) NOT NULL,
  `quantidade` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `livros` (`id`, `titulo`, `autor`, `isbn`, `capa_url`, `descricao`, `categoria`, `ano_publicacao`, `genero`, `quantidade`) VALUES
(2, 'Heróis da fé', 'Orlando Boyer', '8526311956', NULL, 'Mais de 300.000 livros vendidos! Um dos maiores clássicos da literatura evangélica. Homens extraordinários que incendiaram o mundo. A cada capítulo uma história diferente, uma nova biografia. As verdadeiras histórias de alguns dos maiores vultos da Igreja de Cristo. Heróis como: Lutero, Finney, Wesley e Moody, dentre outros que resolveram viver uma vida de plenitude do evangelho. \"O soluço de um bilhão de almas na terra me soa aos ouvidos e comove o coração: esforço-me, pelo auxílio de Deus, para avaliar, ao menos em parte, as densas trevas, a extrema miséria e o indescritível desespero desses mil milhões de almas sem Cristo. Medita, irmão, sobre o amor do Mestre, amor profundo como o mar, contempla o horripilante espetáculo do desespero dos povos perdidos, até não poderes censurar, até não poderes descansar, até não poderes dormir.\" (Carlos Inwood). Esta obra contém as biografias de grandes servos de Jesus. Conheça a vida de pessoas verdadeiramente transformadas por Deus e que, por isso, servem-nos como exemplos de vida. Um estímulo para também buscarmos ser reconhecidos como verdadeiros Heróis da Fé. Um produto CPAD.', NULL, '', '', 0),
(3, 'Os Heróis da Fé De acordo com Hebreus 11', 'Domenico Barbera', '1507196539', NULL, 'SE assumimos a missão de escrever a respeito do décimo primeiro capítulo da carta aos Hebreus, não fizemos com o objetivo de fornecer ao leitor outro comentário, embora certas coisas vos escrevemos, além de não estar em comentários públicos, poderiam ser consideradas como tal, especialmente pelo conteúdo diferente que possui; mas apenas para enfatizar o valor da fé e sua eficácia, especialmente no exercício da vida cotidiana. Embora a respeito da fé, muitos livros foram escritos ao longo dos anos, acreditamos que é apropriado realizar uma pesquisa bastante completa sobre os homens e mulheres listados no capítulo 11 da Epístola aos Hebreus com o único propósito de descobrir os vários momentos e várias situações que caracterizaram a vida dessas pessoas. Sem dúvida, a maneira pela qual lidamos com este trabalho, embora nos envolveu muito, especialmente em termos do texto bíblico, acreditamos que vale a pena fazê-lo, pelo inevitável benefício que trará, a fim de compreender e avaliar os personagens tratados , especialmente no que diz respeito à sua fé.<br>', NULL, '', '', 1),
(4, 'Jesus', 'Charles Swindoll', '857325906X', NULL, 'Filho de Deus, o Salvador: o maior herói. Passados mais de 2.000 anos, a figura de Jesus continua em evidência. Se não bastassem os bilhões de seguidores enfileirados nos variados ramos do cristianismo que reconhecem sua santidade, pesquisadores nos diversos campos das ciências sociais continuam a discutir o verdadeiro papel de Jesus. Enquanto alguns exaltam sua liderança popular, há os que simplesmente o consideram uma farsa. Após desfilar alguns dos principais personagens da galeria de heróis bíblicos, Charles Swindoll encerra a série Heróis da fé com o ser mais importante da história. Distante de controvérsias, Swindoll ressalta a figura do Salvador da humanidade e sua história singular. Um carpinteiro, vindo das regiões mais desvalorizadas e esquecidas da Palestina, revela o amor de Deus e sua paixão pelos mais pobres, cidadãos de segunda classe alçados à condição de cidadãos do Reino de Deus. Acompanhe Charles Swindoll na inspiradora trajetória de Jesus de Nazaré e compreenda por que sua vida e seus ensinamentos são determinantes para quem deseja conhecer a Deus.', NULL, '', '', 1);

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `professores` (`id`, `nome`, `email`, `cpf`, `senha`) VALUES
(1, 'Fernando', 'franciolliProfessor@gmail.com', '991199922', '756d66730dc2220bfb275cc759311c91'),
(2, 'Marques', 'marquesteste1@gmail.com', '12345678901', '61a5470a80e29d48f6a48a18a6e3d6ee');

ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `professor_id` (`professor_id`);

ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `livro_id` (`livro_id`);

ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;



ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;



ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`) ON DELETE SET NULL;


ALTER TABLE `emprestimos`
  ADD CONSTRAINT `emprestimos_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emprestimos_ibfk_2` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
