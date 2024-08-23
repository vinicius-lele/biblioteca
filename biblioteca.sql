-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/08/2024 às 13:09
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `biblioteca`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `livro`
--

CREATE TABLE `livro` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `autor` varchar(150) NOT NULL,
  `editora` varchar(100) DEFAULT NULL,
  `edicao` varchar(3) DEFAULT NULL,
  `volume` varchar(30) DEFAULT NULL,
  `ano` varchar(4) DEFAULT NULL,
  `classificacao` varchar(5) DEFAULT NULL,
  `extra` varchar(100) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `locacao`
--

CREATE TABLE `locacao` (
  `id` int(11) NOT NULL,
  `id_locatario` varchar(10) NOT NULL,
  `id_livro` varchar(10) NOT NULL,
  `data_emprestimo` date NOT NULL,
  `data_devolucao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `locatario`
--

CREATE TABLE `locatario` (
  `id` int(11) NOT NULL,
  `documento` text NOT NULL,
  `nome_locatario` text NOT NULL,
  `responsavel_locatario` text DEFAULT NULL,
  `telefone` text DEFAULT NULL,
  `tipo_locatario` int(11) NOT NULL,
  `data_cadastro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estrutura para tabela `tipo_classificacao`
--

CREATE TABLE `tipo_classificacao` (
  `id` int(11) NOT NULL,
  `codigo_classificacao` text NOT NULL,
  `nome_classificacao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipo_classificacao`
--

INSERT INTO `tipo_classificacao` (`id`, `codigo_classificacao`, `nome_classificacao`) VALUES
(1, '81', 'POESIA'),
(2, '82', 'TEATRO'),
(3, '83', 'LITERATURA PARA JOVEM'),
(4, '84', 'LITERATURA PARA CRIANÇA'),
(5, '85', 'CLÁSSICOS INFANTIS'),
(6, '86', 'LIVRO DE IMAGEM'),
(7, '87', 'HISTÓRIAS EM QUADRINHOS'),
(8, '88', 'LIVRO BRINQUEDO'),
(9, '89', 'CURIOSIDADES (INFORMATIVOS)'),
(10, '398I', 'FOLCLORE'),
(11, '100', 'FILOSOFIA'),
(12, '133.9', 'ESPIRITISMO'),
(13, '150', 'PSICOLOGIA'),
(14, '158.1', 'AUTO-AJUDA'),
(15, '200', 'RELIGIÃO'),
(16, '292', 'MITOLOGIA'),
(17, '380', 'COMÉRCIO, COMUNICAÇÕES E TRANSPORTES'),
(18, '530', 'FÍSICA'),
(19, '641', 'CULINÁRIA'),
(20, '650', 'ADMINISTRAÇÃO'),
(21, '700', 'ARTES'),
(22, '740', 'HISTÓRIA EM QUADRINHOS'),
(23, '791', 'DIVERSÕES PÚBLICAS'),
(24, '813', 'ROMANCE; CONTO AMERICANO'),
(25, '822', 'TEATRO INGLÊS'),
(26, '823', 'ROMANCE; CONTO INGLÊS'),
(27, '833', 'ROMANCE; CONTO ALEMÃO'),
(28, '843', 'ROMANCE; CONTO FRANCÊS'),
(29, '853', 'ROMANCE; CONTO ITALIANO'),
(30, '863', 'ROMANCE; CONTO ESPANHOL'),
(31, 'B869', 'LITERATURA BRASILEIRA'),
(32, 'B869.1', 'POESIA BRASILEIRA'),
(33, 'B869.2', 'TEATRO BRASILEIRO'),
(34, 'B869.3', 'ROMANCE; CONTO BRASILEIRO'),
(35, 'B869.8', 'CRÔNICAS BRASILEIRAS'),
(36, 'B869.9', 'HISTÓRIA DA LITERATURA BRASILEIRA'),
(37, '920', 'BIOGRAFIA'),
(38, '796', 'FUTEBOL'),
(39, '900', 'HISTORIA'),
(40, '881', 'LITERATURA GREGA'),
(41, '981', 'HISTÓRIA DO BRASIL'),
(42, '610', 'MEDICINA E SAÚDE'),
(43, '577', 'ECOLOGIA'),
(44, '490', 'OUTRAS LÍNGUAS'),
(45, '469', 'LÍNGUA PORTUGUESA'),
(46, '300', 'CIÊNCIAS SOCIAIS');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_disponivel`
--

CREATE TABLE `tipo_disponivel` (
  `id` int(11) NOT NULL,
  `nome` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipo_disponivel`
--

INSERT INTO `tipo_disponivel` (`id`, `nome`) VALUES
(0, 'INDISPONÍVEL'),
(1, 'DISPONÍVEL');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_locatario`
--

CREATE TABLE `tipo_locatario` (
  `id` int(11) NOT NULL,
  `nome_tipo_locatario` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipo_locatario`
--

INSERT INTO `tipo_locatario` (`id`, `nome_tipo_locatario`) VALUES
(1, 'ALUNO'),
(2, 'SERVIDOR');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(5) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `senha` varchar(55) NOT NULL,
  `tipo_id` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `usuario`, `senha`, `tipo_id`) VALUES
(1, 'admin', '698dc19d489c4e4db73e28a713eab07b', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `livro`
--
ALTER TABLE `livro`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `locacao`
--
ALTER TABLE `locacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `locatario`
--
ALTER TABLE `locatario`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_classificacao`
--
ALTER TABLE `tipo_classificacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_disponivel`
--
ALTER TABLE `tipo_disponivel`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipo_locatario`
--
ALTER TABLE `tipo_locatario`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `livro`
--
ALTER TABLE `livro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `locacao`
--
ALTER TABLE `locacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `locatario`
--
ALTER TABLE `locatario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `tipo_classificacao`
--
ALTER TABLE `tipo_classificacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de tabela `tipo_locatario`
--
ALTER TABLE `tipo_locatario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
