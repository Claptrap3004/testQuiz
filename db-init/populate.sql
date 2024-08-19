USE quiz;
INSERT INTO user (username, email, password) VALUES ('admin', 'admin@mail.org','$2y$10$T412d/k3KtTJAqN2X4tZV.nuqBZXswqWe01CQ8YR4g5rSQCYt0PPa');

DROP TABLE IF EXISTS track_quiz_content_admin_mail_org;
DROP TABLE IF EXISTS quiz_content_admin_mail_org;
CREATE TABLE quiz_content_admin_mail_org (id INT PRIMARY KEY AUTO_INCREMENT,
                                          question_id INT,
                                          is_actual BOOL);
CREATE TABLE track_quiz_content_admin_mail_org(
                                                  id INT PRIMARY KEY AUTO_INCREMENT,
                                                  content_id INT,
                                                  answer_id INT
);


