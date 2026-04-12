-- ============================================================
--  Health Tracker — Database Schema
--  Run this file once to set up your MySQL database
-- ============================================================

CREATE DATABASE IF NOT EXISTS health_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE health_tracker;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    email       VARCHAR(150)        NOT NULL UNIQUE,
    password    VARCHAR(255)        NOT NULL,
    avatar      VARCHAR(10)         DEFAULT '🧑',
    created_at  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
);

-- Mood logs
CREATE TABLE IF NOT EXISTS mood_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT                 NOT NULL,
    mood        TINYINT             NOT NULL COMMENT '1=Terrible 2=Bad 3=Okay 4=Good 5=Great',
    note        TEXT,
    logged_at   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Nutrition logs
CREATE TABLE IF NOT EXISTS nutrition_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT                 NOT NULL,
    meal_name   VARCHAR(200)        NOT NULL,
    calories    INT                 NOT NULL DEFAULT 0,
    protein     DECIMAL(6,2)        DEFAULT 0,
    carbs       DECIMAL(6,2)        DEFAULT 0,
    fats        DECIMAL(6,2)        DEFAULT 0,
    meal_type   ENUM('breakfast','lunch','dinner','snack') DEFAULT 'snack',
    logged_at   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Workout logs
CREATE TABLE IF NOT EXISTS workout_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT                 NOT NULL,
    exercise    VARCHAR(200)        NOT NULL,
    duration    INT                 NOT NULL COMMENT 'in minutes',
    intensity   ENUM('low','medium','high') DEFAULT 'medium',
    calories_burned INT             DEFAULT 0,
    notes       TEXT,
    logged_at   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Water intake logs
CREATE TABLE IF NOT EXISTS water_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT                 NOT NULL,
    amount_ml   INT                 NOT NULL DEFAULT 250,
    logged_at   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Daily goals
CREATE TABLE IF NOT EXISTS goals (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT             NOT NULL UNIQUE,
    calorie_goal    INT             DEFAULT 2000,
    water_goal_ml   INT             DEFAULT 2500,
    workout_goal    INT             DEFAULT 30 COMMENT 'minutes per day',
    updated_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
--  Sample data (optional — remove in production)
-- ============================================================
INSERT INTO users (name, email, password, avatar) VALUES
('Demo User', 'demo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '🧑');
-- password: password

INSERT INTO goals (user_id, calorie_goal, water_goal_ml, workout_goal) VALUES (1, 2000, 2500, 30);
