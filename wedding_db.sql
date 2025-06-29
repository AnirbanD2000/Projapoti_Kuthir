-- Create the wedding website database
CREATE DATABASE IF NOT EXISTS wedding_website;
USE wedding_website;

-- Create contacts table for form submissions
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create subscribers table for newsletter
CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create biodata_images table
CREATE TABLE IF NOT EXISTS biodata_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    name VARCHAR(100) NULL,
    age VARCHAR(50) NULL,
    height VARCHAR(50) NULL,
    education VARCHAR(100) NULL,
    caste VARCHAR(100) NULL,
    address TEXT NULL,
    type ENUM('men', 'women') NULL,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create gallery_images table
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    tab_id INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create gallery_tabs table
CREATE TABLE IF NOT EXISTS gallery_tabs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tab_name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create gallery_settings table
CREATE TABLE IF NOT EXISTS gallery_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    common_heading VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default gallery tab
INSERT INTO gallery_tabs (tab_name, display_order)
SELECT 'Default Gallery', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM gallery_tabs WHERE tab_name = 'Default Gallery');

-- Insert default gallery settings
INSERT INTO gallery_settings (common_heading)
SELECT 'Gallery Images' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM gallery_settings);

-- Add indexes for better performance
ALTER TABLE biodata_images ADD INDEX idx_type (type);
ALTER TABLE biodata_images ADD INDEX idx_created_at (created_at);
ALTER TABLE gallery_images ADD INDEX idx_tab_id (tab_id);
ALTER TABLE gallery_images ADD INDEX idx_upload_date (upload_date);
ALTER TABLE gallery_tabs ADD INDEX idx_display_order (display_order); 