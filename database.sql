CREATE DATABASE logical_validator;

USE logical_validator;

CREATE TABLE expressions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expression VARCHAR(255) NOT NULL,
    truth_table TEXT NOT NULL,
    tautology VARCHAR(3) NOT NULL,
    validity VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
