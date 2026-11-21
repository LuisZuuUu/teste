CREATE DATABASE IF NOT EXISTS voltauto CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voltauto;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    telefone VARCHAR(25) NOT NULL,
    email VARCHAR(150),
    observacoes TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS veiculos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT UNSIGNED NOT NULL,
    marca VARCHAR(60) NOT NULL,
    modelo VARCHAR(80) NOT NULL,
    ano SMALLINT UNSIGNED,
    placa VARCHAR(7) NOT NULL UNIQUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_veiculos_clientes FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS servicos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT UNSIGNED NOT NULL,
    descricao VARCHAR(160) NOT NULL,
    data_servico DATE NOT NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('Pendente','Em andamento','Concluído','Cancelado') NOT NULL DEFAULT 'Pendente',
    detalhes TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_servicos_veiculos FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT IGNORE INTO clientes (id,nome,telefone,email,observacoes) VALUES
(1,'Marcos Oliveira','(41) 99912-3401','marcos@email.com','Cliente recorrente'),
(2,'Ana Martins','(41) 99876-1200','ana@email.com','Prefere contato por WhatsApp'),
(3,'Rafael Souza','(41) 99711-8822','rafael@email.com','');

INSERT IGNORE INTO veiculos (id,cliente_id,marca,modelo,ano,placa) VALUES
(1,1,'Volkswagen','Gol 1.6',2018,'ABC1D23'),
(2,2,'Chevrolet','Onix LT',2021,'DEF4G56'),
(3,3,'Fiat','Argo Drive',2020,'GHI7J89');

INSERT IGNORE INTO servicos (id,veiculo_id,descricao,data_servico,valor,status,detalhes) VALUES
(1,1,'Diagnóstico do sistema de partida',CURDATE(),180.00,'Concluído','Revisão do motor de partida e conexões.'),
(2,2,'Revisão do alternador',CURDATE(),'320.00','Em andamento','Verificação de carga e regulador de tensão.'),
(3,3,'Instalação de iluminação auxiliar',DATE_SUB(CURDATE(),INTERVAL 2 DAY),250.00,'Pendente','Aguardando aprovação do orçamento.');
