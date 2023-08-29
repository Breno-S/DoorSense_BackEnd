-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/08/2023 às 21:58
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `doorsense`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin`
--

CREATE TABLE `admin` (
  `ID_ADMIN` int(11) NOT NULL,
  `EMAIL_ADMIN` varchar(100) NOT NULL,
  `SENHA_ADMIN` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admin`
--

INSERT INTO `admin` (`ID_ADMIN`, `EMAIL_ADMIN`, `SENHA_ADMIN`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Estrutura para tabela `arduino`
--

CREATE TABLE `arduino` (
  `ID_ARDUINO` int(11) NOT NULL,
  `UNIQUE_ID` varchar(100) NOT NULL,
  `STATUS_ARDUINO` enum('Ativo','Inativo') NOT NULL DEFAULT 'Ativo',
  `LAST_UPDATE` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `arduino`
--

INSERT INTO `arduino` (`ID_ARDUINO`, `UNIQUE_ID`, `STATUS_ARDUINO`, `LAST_UPDATE`) VALUES
(1, '00 11 22 33 44 55 66 77 88', 'Ativo', '2023-08-28 16:49:02'),
(2, 'FF EE DD CC BB AA 00 11 22', 'Ativo', '2023-08-28 16:49:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sala`
--

CREATE TABLE `sala` (
  `ID_SALA` int(11) NOT NULL,
  `NOME_SALA` varchar(100) NOT NULL,
  `NUMERO_SALA` varchar(4) DEFAULT NULL,
  `FK_ARDUINO` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sala`
--

INSERT INTO `sala` (`ID_SALA`, `NOME_SALA`, `NUMERO_SALA`, `FK_ARDUINO`) VALUES
(1, 'Laboratório de Informática', '1', 1),
(2, 'Laboratório de Informática', '2', 2),
(3, 'Laboratório de Informática', '3', NULL),
(4, 'Laboratório de Informática', '4', NULL),
(5, 'Biblioteca', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID_ADMIN`),
  ADD UNIQUE KEY `EMAIL_ADMIN` (`EMAIL_ADMIN`);

--
-- Índices de tabela `arduino`
--
ALTER TABLE `arduino`
  ADD PRIMARY KEY (`ID_ARDUINO`),
  ADD UNIQUE KEY `UNIQUE_ID` (`UNIQUE_ID`);

--
-- Índices de tabela `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`ID_SALA`),
  ADD UNIQUE KEY `FK_ARDUINO` (`FK_ARDUINO`),
  ADD UNIQUE KEY `UC_NOME_NUMERO` (`NOME_SALA`,`NUMERO_SALA`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `ID_ADMIN` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `arduino`
--
ALTER TABLE `arduino`
  MODIFY `ID_ARDUINO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `sala`
--
ALTER TABLE `sala`
  MODIFY `ID_SALA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `sala`
--
ALTER TABLE `sala`
  ADD CONSTRAINT `sala_ibfk_1` FOREIGN KEY (`FK_ARDUINO`) REFERENCES `arduino` (`ID_ARDUINO`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
