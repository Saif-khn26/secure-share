CREATE DATABASE IF NOT EXISTS secure_share;
USE secure_share;

CREATE TABLE files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255),
    filepath VARCHAR(255),
    otp VARCHAR(6),
    expiry DATETIME,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
