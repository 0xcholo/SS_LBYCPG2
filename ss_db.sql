CREATE DATABASE ss_db;
USE ss_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL, 
    email VARCHAR(100) NOT NULL
);

CREATE TABLE budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    budget_type VARCHAR(50) NOT NULL UNIQUE,
    budget_amount DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    budget_id INT, 
    expense_amount DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (budget_id) REFERENCES budgets(id)
);


CREATE TABLE wallet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    balance_amount DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id)
);
