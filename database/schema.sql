CREATE DATABASE IF NOT EXISTS `medconnect` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `medconnect`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `funcionarios`;
CREATE TABLE `funcionarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `perfil` ENUM('admin', 'medico', 'recepcao') NOT NULL DEFAULT 'medico',
  `cargo` VARCHAR(50) DEFAULT 'Atendente',
  `especialidade` VARCHAR(100) DEFAULT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_funcionarios_email` (`email`),
  KEY `idx_funcionarios_busca` (`perfil`, `ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pacientes`;
CREATE TABLE `pacientes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `cpf` VARCHAR(14) NOT NULL,
  `telefone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL,
  `data_nascimento` DATE DEFAULT NULL,
  `tipo_sanguineo` VARCHAR(5) DEFAULT NULL,
  `convenio` VARCHAR(50) DEFAULT NULL,
  `alergias` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pacientes_cpf` (`cpf`),
  KEY `idx_pacientes_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `agendamentos`;
CREATE TABLE `agendamentos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `paciente_id` INT UNSIGNED NOT NULL,
  `funcionario_id` INT UNSIGNED DEFAULT NULL,
  `especialidade` VARCHAR(100) DEFAULT NULL,
  `data` DATE NOT NULL,
  `hora` TIME NOT NULL,
  `status` ENUM('Aguardando', 'Confirmado', 'Cancelado', 'Concluido') NOT NULL DEFAULT 'Aguardando',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_agendamentos_agenda` (`data`, `hora`),
  CONSTRAINT `fk_agendamentos_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_agendamentos_funcionario` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `prontuarios`;
CREATE TABLE `prontuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `paciente_id` INT UNSIGNED NOT NULL,
  `funcionario_id` INT UNSIGNED DEFAULT NULL,
  `diagnostico` TEXT DEFAULT NULL,
  `prescricao` TEXT DEFAULT NULL,
  `status` ENUM('ativo', 'arquivado') NOT NULL DEFAULT 'ativo',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_prontuarios_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prontuarios_funcionario` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `prontuario_anexos`;
CREATE TABLE `prontuario_anexos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `prontuario_id` INT UNSIGNED NOT NULL,
  `nome_original` VARCHAR(255) NOT NULL,
  `caminho` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_anexos_prontuario` FOREIGN KEY (`prontuario_id`) REFERENCES `prontuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `funcionarios` (`id`, `nome`, `email`, `senha`, `perfil`, `cargo`, `especialidade`, `ativo`) VALUES


SET FOREIGN_KEY_CHECKS = 1;