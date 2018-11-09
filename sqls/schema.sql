-- THIS IS THE MAIN FILE FOR THE DATABASE SCHEMATIC
DROP DATABASE IF EXISTS stock_exchange;
CREATE DATABASE stock_exchange;

USE stock_exchange

-- STOCK
CREATE TABLE stocks (
       id INT NOT NULL UNIQUE auto_increment,
       label VARCHAR(5) NOT NULL UNIQUE,
       company_name VARCHAR(64) NOT NULL,
       PRIMARY KEY (id)
);


-- TRANSACTION
CREATE TABLE transactions (
       id INT NOT NULL UNIQUE auto_increment,
       stock_id INT NOT NULL,
       num_stock INT NOT NULL,
       price DECIMAL(10,4) NOT NULL,
       FOREIGN KEY (stock_id)
               REFERENCES stocks(id)
);

-- BANK ACCOUNT

-- PORTFOLIA

-- USER, TRADER, ADMIN
