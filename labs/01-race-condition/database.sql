CREATE DATABASE IF NOT EXISTS security_labs;

USE security_labs;

CREATE TABLE accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    owner VARCHAR(100) NOT NULL,
    balance_cents BIGINT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE transfers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    amount_cents BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES accounts(id)
) ENGINE=InnoDB;

INSERT INTO accounts (id, owner, balance_cents)
VALUES (1, 'Alice', 10000);