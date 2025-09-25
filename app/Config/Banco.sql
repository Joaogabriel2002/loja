CREATE DATABASE loja


CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
); COMMENT='Tabela para armazenar os usuários do sistema.';

-- CREATE TABLE `fornecedores` (
--   `id` INT AUTO_INCREMENT PRIMARY KEY,
--   `nome_fantasia` VARCHAR(255) NOT NULL,
--   `razao_social` VARCHAR(255),
--   `cnpj` VARCHAR(18) UNIQUE,
--   `telefone` VARCHAR(20),
--   `email` VARCHAR(255)
-- ) COMMENT='Tabela de fornecedores de produtos.';

-- CREATE TABLE `clientes` (
--   `id` INT AUTO_INCREMENT PRIMARY KEY,
--   `nome` VARCHAR(255) NOT NULL,
--   `cpf` VARCHAR(14) UNIQUE,
--   `telefone` VARCHAR(20)
-- ) COMMENT='Tabela opcional para cadastro de clientes.';


-- Módulo de Estoque

CREATE TABLE `categorias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(240) NOT NULL UNIQUE
) COMMENT='Tabela para categorizar os produtos.';

CREATE TABLE `produtos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(240) NOT NULL,
  `descricao` TEXT,
  `preco_custo` DECIMAL(10, 2),
  `preco_venda` DECIMAL(10, 2) NOT NULL,
  `quantidade_estoque` INT NOT NULL DEFAULT 0,
  `id_categoria` INT,
  FOREIGN KEY (`id_categoria`) REFERENCES `categorias`(`id`),
) COMMENT='Tabela principal de produtos.';

CREATE TABLE `movimentacao_estoque` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_produto` INT NOT NULL,
  `data_hora` DATETIME NOT NULL,
  `tipo_movimentacao` VARCHAR(50) NOT NULL COMMENT 'Ex: ENTRADA_COMPRA, SAIDA_VENDA, AJUSTE_PERDA',
  `quantidade` INT NOT NULL COMMENT 'Positivo para entradas, negativo para saídas',
  `observacao` TEXT,
  FOREIGN KEY (`id_produto`) REFERENCES `produtos`(`id`)
) COMMENT='Histórico de todas as movimentações de estoque.';


-- Módulo de Vendas

CREATE TABLE `vendas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `data_hora` DATETIME NOT NULL,
  `valor_total` DECIMAL(10, 2) NOT NULL,
  `forma_pagamento` VARCHAR(240),
  `id_usuario` INT NOT NULL,
  `id_cliente` INT,
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`)
) COMMENT='Registra cada transação de venda (cabeçalho).';

CREATE TABLE `itens_venda` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_venda` INT NOT NULL,
  `id_produto` INT NOT NULL,
  `quantidade` INT NOT NULL,
  `preco_unitario_momento` DECIMAL(10, 2) NOT NULL COMMENT 'Preço do produto no momento da venda',
  FOREIGN KEY (`id_venda`) REFERENCES `vendas`(`id`),
  FOREIGN KEY (`id_produto`) REFERENCES `produtos`(`id`)
) COMMENT='Detalha os produtos de cada venda.';