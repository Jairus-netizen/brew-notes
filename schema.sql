CREATE DATABASE IF NOT EXISTS coffee_notepad CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE coffee_notepad;

CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL DEFAULT 'Untitled Note',
    content MEDIUMTEXT,
    is_pinned TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pinned_updated (is_pinned DESC, updated_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO notes (title, content, is_pinned) VALUES 
('Morning Pour-Over Notes', 'Welcome to your distraction-free coffee notepad.\n\n- Changes save automatically.\n- Pin important notes to keep them at the top.\n- Use the search bar to filter your thoughts.', 1);