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
CREATE TABLE bank_acct (
        id INT NOT NULL UNIQUE auto_increment,
        account_no INT NOT NULL,
        routing_no INT NOT NULL,
        FOREIGN KEY (account_no)
                REFERENCES user(id)
);

-- PORTFOLIO
CREATE TABLE portfolio (
        id INT NOT NULL UNIQUE auto_increment,
        funds INT NOT NULL,
);

-- USER
CREATE TABLE users (
        id INT NOT NULL UNIQUE auto_increment,
        username VARCHAR(64) NOT NULL,
        screen_name VARCHAR(64),
        password VARCHAR(64) NOT NULL,
        f_name VARCHAR(64) NOT NULL,
        l_name VARCHAR(64) NOT NULL,
        bday VARCHAR(64) NOT NULL,
        PRIMARY KEY (id)
);

-- TRADER
CREATE TABLE traders (
        id INT NOT NULL UNIQUE auto_increment,
        user_id INT NOT NULL,
        port_id INT NOT NULL,
        FOREIGN KEY (user_id)
                REFERENCES user(id)
);

-- ADMIN
CREATE TABLE admins (
        id INT NOT NULL UNIQUE auto_increment,
        user_id INT NOT NULL,
        FOREIGN KEY (user_id)
                REFERENCES user(id)
);
