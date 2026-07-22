-- ============================================
-- Hotel System Database Schema
-- Database: hotel_system_db
-- ============================================

CREATE DATABASE IF NOT EXISTS hotel_system_db;
USE hotel_system_db;

-- ============================================
-- 1. employees table
-- ============================================
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    position VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 2. hotel_bookings table
-- ============================================
CREATE TABLE IF NOT EXISTS hotel_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    room_number VARCHAR(10) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    status ENUM('confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 3. Seed sample employees
-- ============================================
INSERT INTO employees (name, email, position, phone) VALUES
('Juan Dela Cruz', 'juan.delacruz@hotel.com', 'Front Desk Manager', '09171234567'),
('Maria Santos', 'maria.santos@hotel.com', 'Housekeeping Supervisor', '09179876543'),
('Pedro Gonzales', 'pedro.gonzales@hotel.com', 'Concierge', '09175678901'),
('Ana Bautista', 'ana.bautista@hotel.com', 'Reservation Agent', '09173456789'),
('Jose Rizal', 'jose.rizal@hotel.com', 'General Manager', '09171239876');

