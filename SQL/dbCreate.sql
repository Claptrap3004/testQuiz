DROP DATABASE IF EXISTS abfrageprogramm;
CREATE DATABASE abfrageprogramm;
USE abfrageprogramm;

CREATE TABLE user
(
    id       INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255),
    email    VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(60)
);

CREATE TABLE category
(
    id   INT PRIMARY KEY AUTO_INCREMENT,
    text VARCHAR(255)
);

CREATE TABLE answer
(
    id   INT PRIMARY KEY AUTO_INCREMENT,
    text VARCHAR(1024)
);

CREATE TABLE question
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    user_id     int,
    text        VARCHAR(1024),
    explanation VARCHAR(4096),
    FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL
);

CREATE TABLE stats
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT,
    question_id INT,
    times_asked INT,
    times_right INT,
    FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL,
    FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE SET NULL
);


CREATE TABLE answerToQuestion
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT,
    answer_id   INT,
    is_right    BOOL,
    FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE,
    FOREIGN KEY (answer_id) REFERENCES answer (id) ON DELETE CASCADE
);
