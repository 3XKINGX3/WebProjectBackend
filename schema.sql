-- schema.sql — структура БД анкеты.
--
-- НА СЕРВЕРЕ ВУЗА эти таблицы УЖЕ существуют (база u82373) — выполнять не нужно.
-- Этот файл нужен для ЛОКАЛЬНОГО запуска (XAMPP), чтобы создать те же таблицы.

CREATE DATABASE IF NOT EXISTS u82373 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE u82373;

CREATE TABLE IF NOT EXISTS applications (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fio           VARCHAR(150)            NOT NULL,
  phone         VARCHAR(30)             NOT NULL,
  email         VARCHAR(100)            NOT NULL,
  birth_date    DATE                    NOT NULL,
  gender        ENUM('male','female')   NOT NULL,
  biography     TEXT                    NOT NULL,
  login         VARCHAR(100)            DEFAULT NULL,
  password_hash VARCHAR(255)            DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS languages (
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application_languages (
  application_id INT UNSIGNED NOT NULL,
  language_id    INT UNSIGNED NOT NULL,
  PRIMARY KEY (application_id, language_id),
  KEY (application_id),
  KEY (language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO languages (id, name) VALUES
(1,'Pascal'),(2,'C'),(3,'C++'),(4,'JavaScript'),(5,'PHP'),(6,'Python'),
(7,'Java'),(8,'Haskell'),(9,'Clojure'),(10,'Prolog'),(11,'Scala'),(12,'Go');
