CREATE DATABASE IF NOT EXISTS tasks CHARSET utf8mb4;
USE tasks;

-- tables
CREATE TABLE IF NOT EXISTS users(
id_users INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
firstname VARCHAR(50) NOT NULL,
lastname VARCHAR(50) NOT NULL,
email VARCHAR(50) UNIQUE NOT NULL,
`password` VARCHAR(100) NOT NULL
)ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS task(
id_task INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
title VARCHAR(50) NOT NULL,
`description` VARCHAR(255),
created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
end_date DATETIME NOT NULL,
`status` BOOL DEFAULT 0 NOT NULL,
id_users INT NOT NULL 
)ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS category(
id_category INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`name` VARCHAR(50) UNIQUE NOT NULL
)ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS task_category(
id_task INT, 
id_category INT,
PRIMARY KEY(id_task, id_category)
)ENGINE = InnoDB;

-- contraintes
ALTER TABLE task
ADD CONSTRAINT fk_possess_users
FOREIGN KEY(id_users)
REFERENCES users(id_users)
ON DELETE CASCADE;

ALTER TABLE task_category
ADD CONSTRAINT fk_complete_task
FOREIGN KEY(id_task)
REFERENCES task(id_task);

ALTER TABLE task_category
ADD CONSTRAINT fk_complete_category
FOREIGN KEY(id_category)
REFERENCES category(id_category);

-- ajout de la colonne image du profil
ALTER TABLE users
ADD COLUMN img VARCHAR(50);

-- ajout de la colonne grants
ALTER TABLE users
ADD COLUMN grants VARCHAR(255)
