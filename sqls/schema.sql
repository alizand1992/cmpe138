-- THIS IS THE MAIN FILE FOR THE DATABASE SCHEMATIC
DROP DATABASE IF EXISTS stock_exchange;
CREATE DATABASE stock_exchange;

USE stock_exchange

-- STOCKS
CREATE TABLE stocks (
       id INT NOT NULL UNIQUE auto_increment,
       label VARCHAR(5) NOT NULL UNIQUE,
       company_name VARCHAR(64) NOT NULL,
       PRIMARY KEY (id)
);

-- USERS
CREATE TABLE users (
        id INT NOT NULL UNIQUE auto_increment,
        username VARCHAR(64) NOT NULL UNIQUE,
        password VARCHAR(128) NOT NULL,
        screen_name VARCHAR(64),
        f_name VARCHAR(64) NOT NULL,
        l_name VARCHAR(64) NOT NULL,
        bday DATETIME NOT NULL,
        PRIMARY KEY (id)
);

-- PORTFOLIOS
CREATE TABLE portfolios (
        id INT NOT NULL UNIQUE auto_increment,
        funds DECIMAL(10,4) NOT NULL,
        PRIMARY KEY (id)
);

-- TRADER
CREATE TABLE traders (
        id INT NOT NULL UNIQUE auto_increment,
        user_id INT NOT NULL,
        port_id INT NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id)
                REFERENCES users(id),
        FOREIGN KEY (port_id)
                REFERENCES portfolios(id)
);

-- ADMIN
CREATE TABLE admins (
        id INT NOT NULL UNIQUE auto_increment,
        user_id INT NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id)
                REFERENCES users(id)
);

-- PORTFOLIO STOCKS
CREATE TABLE portfolio_stocks (
       id INT NOT NULL UNIQUE auto_increment,
       stock_id INT NOT NULL,
       port_id INT NOT NULL,
       price DECIMAL(10,4) DEFAULT 0.0,
       quantity INT NOT NULL DEFAULT 1,
       PRIMARY KEY (id),
       FOREIGN KEY (stock_id)
               REFERENCES stocks(id),
       FOREIGN KEY (port_id)
               REFERENCES portfolios(id)
);

-- STOCK TO SELL
CREATE TABLE stocks_to_sell (
       id INT NOT NULL UNIQUE auto_increment,
       stock_id INT NOT NULL,
       port_id INT NOT NULL,
       quantity INT DEFAULT 1,
       price DECIMAL(10, 4) NOT NULL,
       PRIMARY KEY (id),
       FOREIGN KEY (stock_id)
               REFERENCES stocks(id),
       FOREIGN KEY (port_id)
               REFERENCES portfolios(id)
);

-- STOCK TO BUY
CREATE TABLE stocks_to_buy (
       id INT NOT NULL UNIQUE auto_increment,
       stock_id INT NOT NULL,
       port_id INT NOT NULL,
       quantity INT DEFAULT 1,
       price DECIMAL(10, 4) NOT NULL,
       PRIMARY KEY (id),
       FOREIGN KEY (stock_id)
               REFERENCES stocks(id),
       FOREIGN KEY (port_id)
               REFERENCES portfolios(id)
);

-- TRANSACTION
CREATE TABLE transactions (
       id INT NOT NULL UNIQUE auto_increment,
       stock_id INT NOT NULL,
       port_id INT NOT NULL,
       num_stock INT NOT NULL,
       price DECIMAL(10,4) NOT NULL,
       FOREIGN KEY (stock_id)
               REFERENCES stocks(id),
       FOREIGN KEY (port_id)
               REFERENCES portfolios(id)
);

-- BANK ACCOUNTS
CREATE TABLE bank_accounts (
        id INT NOT NULL UNIQUE auto_increment,
        account_no INT NOT NULL UNIQUE,
        routing_no INT NOT NULL,
        port_id INT NOT NULL,
        FOREIGN KEY (port_id)
                REFERENCES portfolios(id)
);

-- ADMIN PORTFOLIOS
CREATE TABLE admin_portfolios (
        id INT NOT NULL UNIQUE auto_increment,
        admin_id INT NOT NULL,
        port_id INT NOT NULL,
        FOREIGN KEY (admin_id)
                REFERENCES admins(id),
        FOREIGN KEY (port_id)
                REFERENCES portfolios(id)
);


-- INSERT FAKE STOCK
INSERT INTO stocks (label, company_name)
VALUES
    ('AMD', 'Advance Micro Devices'),
    ('INTL', 'Intel'),
    ('AAPL', 'Apple'),
    ('BAC', 'Bank of America'),
    ('MSFT', 'Microsoft'),
    ('F', 'Ford'),
    ('T', 'At&T'),
    ('FB', 'Facebook'),
    ('GE', 'General Electric');


-- ENTER TRADER USER
INSERT INTO users (username, password, f_name, l_name, bday)
VALUES ('ali.zand@sjsu.edu', '$2y$10$Yx.ZsL02GTawhM9MGJ0yu.PkSGUqARnDggCOkevfelCiB.IMXZsq6', 'Ali', 'Zand', '1890-01-01 00:00:00');

-- Portfolio
INSERT INTO portfolios (funds)
VALUES ('1000.00');

-- TRADER
INSERT INTO traders (user_id, port_id)
VALUES (1, 1);

-- portfolio stocks
INSERT INTO portfolio_stocks (stock_id, port_id, price, quantity)
VALUES
    ('1', '1', '100', '1'),
    ('2', '1', '20', '10'),
    ('3', '1', '140', '15'),
    ('4', '1', '700.50', '100');
