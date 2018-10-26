-- THIS IS THE MAIN FILE FOR THE DATABASE SCHEMATIC
DROP DATABASE IF EXISTS stock_exchange;
CREATE DATABASE stock_exchange;

USE stock_exchange

-- STOCK
CREATE TABLE stocks (
       id int not null unique auto_increment,
       label varchar(5) not null unique,
       company_name varchar(64) not null,
       primary key (id)
);


-- TRANSACTION

-- BANK ACCOUNT

-- PORTFOLIA

-- USER, TRADER, ADMIN
