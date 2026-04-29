CREATE TABLE IF NOT EXISTS queue (
  id INT AUTO_INCREMENT PRIMARY KEY,
  queue_number VARCHAR(10) NOT NULL,
  status ENUM('waiting', 'serving', 'done') DEFAULT 'waiting',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS current_serving (
  id INT PRIMARY KEY DEFAULT 1,
  queue_number VARCHAR(10) DEFAULT '---',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT IGNORE INTO current_serving (id, queue_number) VALUES (1, '---');
