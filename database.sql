CREATE DATABASE IF NOT EXISTS apilaravel;
USE apilaravel;

CREATE TABLE users(
    id  int(255) auto_increment NOT NULL,
    role varchar(20),
    name varchar(255),
    surname varchar(255),
    email varchar(255),
    password varchar(255),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    remember_token varchar(255),
    CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE cars(
    id  int(255) auto_increment NOT NULL,
    user_id int(255) NOT NULL,
    title varchar(255),
    description varchar(255),
    price varchar(30),
    status varchar(30),
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    CONSTRAINT pk_cars PRIMARY KEY(id),
    CONSTRAINT fk_cars_users FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;