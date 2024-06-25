# Prova-QT
Uma tela simples de Login e Cadastro, com CRUD dos usuários.


#Banco de dados

CREATE DATABASE sistema;

USE sistema;

CREATE TABLE perfils (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil_id INT,
    FOREIGN KEY (perfil_id) REFERENCES perfils(id)
);
