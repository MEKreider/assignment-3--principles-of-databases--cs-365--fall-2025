DROP DATABASE IF EXISTS student_passwords;
CREATE DATABASE student_passwords DEFAULT CHARACTER SET utf8mb4;

DROP USER IF EXISTS 'passwords_user'@'localhost';
CREATE USER 'passwords_user'@'localhost' IDENTIFIED BY '';
GRANT ALL ON student_passwords.* TO 'passwords_user'@'localhost';

USE student_passwords;

SET block_encryption_mode = 'aes-256-cbc';
SET @key_str = UNHEX('A7E845B0854294DA9AA743B807CB67B19647C1195EA8120369F3D12C70468F29');
SET @init_vector = UNHEX('A55B9BD9476FBF3F137C1D9E28205D94');


CREATE TABLE IF NOT EXISTS users (
  user_id          MEDIUMINT        NOT NULL         AUTO_INCREMENT,
  first_name       VARCHAR(128)     NOT NULL,
  last_name        VARCHAR(128)     NOT NULL,
  username         VARCHAR(128)     NOT NULL,
  email            VARCHAR(128)     NOT NULL,

  PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS websites (
  site_id           MEDIUMINT       NOT NULL         AUTO_INCREMENT,
  site_name         VARCHAR(128)    NOT NULL,
  site_url          VARCHAR(256)    NOT NULL,

  PRIMARY KEY (site_id)
);

CREATE TABLE IF NOT EXISTS registers_for (
  user_id            MEDIUMINT       NOT NULL,
  site_id            MEDIUMINT       NOT NULL,
  encrypted_password VARBINARY(512)  NOT NULL,
  comment            VARCHAR(500),
  created_at         TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (user_id, site_id),

  FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

  FOREIGN KEY (site_id)
    REFERENCES websites(site_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

INSERT INTO users (first_name, last_name, username, email)
  VALUES ("Charlie", "Parker", "CharlieParker", "cparker@gmail.com");
INSERT INTO users (first_name, last_name, username, email)
  VALUES ("Yardbird", "Parker", "Yardbird20", "bird@bebop.com");
INSERT INTO users (first_name, last_name, username, email)
  VALUES ("Dizzy", "Gillespie", "DizzyG1917", "dizz@bebop.com");
INSERT INTO users (first_name, last_name, username, email)
  VALUES ("Miles", "Davis", "xXPrinceOfDarknessXx", "mdavis@bebop.com");

INSERT INTO websites (site_name, site_url)
  VALUES ("Facebook", "https://www.facebook.com");
INSERT INTO websites (site_name, site_url)
  VALUES ("Twitter", "https://twitter.com");
INSERT INTO websites (site_name, site_url)
  VALUES ("Instagram", "http://www.instagram.com");
INSERT INTO websites (site_name, site_url)
  VALUES ("Discord", "http://discord.com");


INSERT INTO registers_for (user_id, site_id, encrypted_password, comment)
  VALUES (1,1,AES_ENCRYPT('Ornithologyyy123', @key_str, @init_vector), 'Work Facebook');
INSERT INTO registers_for (user_id, site_id, encrypted_password, comment)
  VALUES (1,2,AES_ENCRYPT('yardBirdSuite946', @key_str, @init_vector), 'Public Account');
INSERT INTO registers_for (user_id, site_id, encrypted_password, comment)
  VALUES (2,1,AES_ENCRYPT('PlayDonnaLee$!', @key_str, @init_vector), 'Personal Facebook');
INSERT INTO registers_for (user_id, site_id, encrypted_password, comment)
  VALUES (2,2,AES_ENCRYPT('ConfirmationStnd4', @key_str, @init_vector), 'Private Account');
INSERT INTO registers_for (user_id, site_id, encrypted_password)
  VALUES (2,3,AES_ENCRYPT('Europetour4567', @key_str, @init_vector));
INSERT INTO registers_for (user_id, site_id, encrypted_password)
  VALUES (3,1,AES_ENCRYPT('TunisiaNights555', @key_str, @init_vector));
INSERT INTO registers_for (user_id, site_id, encrypted_password)
  VALUES (3,3,AES_ENCRYPT('SaLtPeAnUtS', @key_str, @init_vector));
INSERT INTO registers_for (user_id, site_id, encrypted_password)
  VALUES (4,2,AES_ENCRYPT('4WonderfulThings404', @key_str, @init_vector));
INSERT INTO registers_for (user_id, site_id, encrypted_password)
  VALUES (4,3,AES_ENCRYPT('Fr3ddi3Fr33load3r', @key_str, @init_vector));
INSERT INTO registers_for (user_id, site_id, encrypted_password)
  VALUES (4,4,AES_ENCRYPT('BirthOfCool5719', @key_str, @init_vector));
