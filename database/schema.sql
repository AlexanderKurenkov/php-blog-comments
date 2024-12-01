-- mysql -u <username> -p < schema.sql
DROP DATABASE IF EXISTS blog;
CREATE DATABASE blog;

USE blog;

DROP TABLE IF EXISTS users;
CREATE TABLE users
(
	id   SERIAL PRIMARY KEY,
	name VARCHAR(20)
);

DROP TABLE IF EXISTS posts;
CREATE TABLE posts
(
	id      SERIAL PRIMARY KEY,
	title   VARCHAR(255)    NOT NULL,
	body    TEXT            NOT NULL,
	user_id BIGINT UNSIGNED NOT NULL,
	FOREIGN KEY (user_id) REFERENCES users (id)
);

DROP TABLE IF EXISTS comments;
CREATE TABLE comments
(
	id      SERIAL PRIMARY KEY,
	name   	VARCHAR(255)    NOT NULL,
	email   VARCHAR(255)    NOT NULL,
	body    TEXT            NOT NULL,
	post_id BIGINT UNSIGNED NOT NULL,
	FOREIGN KEY (post_id) REFERENCES posts (id)
);

INSERT INTO users(id) VALUES (1), (2), (3), (4), (5), (6), (7), (8), (9), (10);
