-- Database schema for simple voting app (MySQL)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'voter') NOT NULL DEFAULT 'voter',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE positions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE candidates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  position_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE
);

CREATE TABLE votes (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  position_id INT NOT NULL,
  candidate_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE,
  FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
  UNIQUE KEY user_position_unique (user_id, position_id) -- prevents double voting per position
);

-- Seed admin user (change password)
-- Replace 'admin_password_here' with a hashed password created using password_hash()
-- Example: php -r "echo password_hash('secret', PASSWORD_DEFAULT);"
INSERT INTO users (username, password_hash, role) VALUES
('admin', '$2y$10$REPLACE_WITH_HASH', 'admin');

-- Example seed data:
INSERT INTO positions (name) VALUES ('President'), ('Treasurer');
INSERT INTO candidates (position_id, name) VALUES (1, 'Alice'), (1, 'Bob'), (2, 'Charlie');