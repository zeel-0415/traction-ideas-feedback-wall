-- Database: traction_ideas

CREATE DATABASE IF NOT EXISTS traction_ideas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE traction_ideas;

-- USERS
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- SUGGESTIONS
DROP TABLE IF EXISTS suggestions;
CREATE TABLE suggestions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  category ENUM('Feature','Design','Bug','Idea') NOT NULL,
  votes INT NOT NULL DEFAULT 0,
  status ENUM('Open','Resolved') NOT NULL DEFAULT 'Open',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  CONSTRAINT fk_sugg_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- VOTES LOG
DROP TABLE IF EXISTS votes_log;
CREATE TABLE votes_log (
  id INT PRIMARY KEY AUTO_INCREMENT,
  suggestion_id INT NOT NULL,
  voter_user_id INT NULL,
  voter_fingerprint VARCHAR(64) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_vote (suggestion_id, voter_user_id),
  UNIQUE KEY uniq_fp_vote (suggestion_id, voter_fingerprint),
  FOREIGN KEY (suggestion_id) REFERENCES suggestions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed users (password: "password")
INSERT INTO users (name, email, password_hash, role) VALUES
('Admin', 'admin@demo.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Demo User', 'user@demo.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Optional seed suggestions
INSERT INTO suggestions (user_id, title, description, category, votes, status)
VALUES
(1, 'Merge duplicate user accounts', 'Allow admins to merge duplicate accounts with audit log.', 'Feature', 3, 'Open'),
(2, 'Dark mode for the app', 'Add a theme toggle and persist preference.', 'Design', 5, 'Open'),
(2, 'Fix 500 on export', 'Export CSV sometimes fails on large datasets.', 'Bug', 2, 'Open'),
(1, 'Quarterly roadmap voting', 'Let users vote each quarter on roadmap items.', 'Idea', 1, 'Resolved');
