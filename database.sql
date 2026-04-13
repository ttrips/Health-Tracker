-- ============================================================
--  Health Tracker - Database Schema + Sample Data
-- ============================================================

CREATE DATABASE IF NOT EXISTS health_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE health_tracker;

CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    email       VARCHAR(150)        NOT NULL UNIQUE,
    password    VARCHAR(255)        NOT NULL,
    avatar      VARCHAR(10)         DEFAULT '🧑',
    created_at  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS mood_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT                 NOT NULL,
    mood        TINYINT             NOT NULL,
    note        TEXT,
    logged_at   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

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

CREATE TABLE IF NOT EXISTS workout_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT                 NOT NULL,
    exercise    VARCHAR(200)        NOT NULL,
    duration    INT                 NOT NULL,
    intensity   ENUM('low','medium','high') DEFAULT 'medium',
    calories_burned INT             DEFAULT 0,
    notes       TEXT,
    logged_at   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS water_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT                 NOT NULL,
    amount_ml   INT                 NOT NULL DEFAULT 250,
    logged_at   TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS goals (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT             NOT NULL UNIQUE,
    calorie_goal    INT             DEFAULT 2000,
    water_goal_ml   INT             DEFAULT 2500,
    workout_goal    INT             DEFAULT 30,
    updated_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- Sample Users (password for all accounts = "password")
-- ============================================================
INSERT INTO users (id, name, email, password, avatar) VALUES
(1, 'Abhideep Singh', 'demo@example.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '🧔'),
(2, 'Priya Sharma',   'priya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '👩'),
(3, 'Rahul Verma',    'rahul@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '🧑');

-- Goals
INSERT INTO goals (user_id, calorie_goal, water_goal_ml, workout_goal) VALUES
(1, 2200, 3000, 45),
(2, 1800, 2500, 30),
(3, 2500, 2000, 60);

-- Mood Logs
INSERT INTO mood_logs (user_id, mood, note, logged_at) VALUES
(1, 5, 'Crushed my workout today, feeling amazing!',   NOW() - INTERVAL 0 DAY),
(1, 4, 'Good day overall, productive at work.',        NOW() - INTERVAL 1 DAY),
(1, 3, 'A bit tired but managed to stay on track.',    NOW() - INTERVAL 2 DAY),
(1, 4, 'Went for a morning run, mood lifted.',         NOW() - INTERVAL 3 DAY),
(1, 2, 'Stressful day, skipped workout.',              NOW() - INTERVAL 4 DAY),
(1, 3, 'Average day, ate healthy at least.',           NOW() - INTERVAL 5 DAY),
(1, 5, 'Weekend vibes, great energy all day!',         NOW() - INTERVAL 6 DAY),
(2, 4, 'Yoga session in the morning helped a lot.',    NOW() - INTERVAL 0 DAY),
(2, 3, 'Felt okay, could have eaten better.',          NOW() - INTERVAL 1 DAY),
(3, 5, 'Hit a new personal record at the gym!',        NOW() - INTERVAL 0 DAY),
(3, 4, 'Good sleep last night made a big difference.', NOW() - INTERVAL 1 DAY);

-- Nutrition Logs
INSERT INTO nutrition_logs (user_id, meal_name, calories, protein, carbs, fats, meal_type, logged_at) VALUES
(1, 'Oats with banana and honey',         350,  8.0, 65.0,  5.0, 'breakfast', NOW() - INTERVAL 2 HOUR),
(1, 'Grilled chicken rice bowl',          520, 42.0, 48.0, 10.0, 'lunch',     NOW() - INTERVAL 5 HOUR),
(1, 'Protein shake',                      180, 25.0, 10.0,  3.0, 'snack',     NOW() - INTERVAL 7 HOUR),
(1, 'Idli sambar (3 pieces)',             280,  8.0, 52.0,  3.5, 'breakfast', NOW() - INTERVAL 1 DAY),
(1, 'Paneer butter masala with roti',     680, 22.0, 58.0, 28.0, 'lunch',     NOW() - INTERVAL 1 DAY),
(1, 'Mixed fruit bowl',                   120,  1.5, 28.0,  0.5, 'snack',     NOW() - INTERVAL 1 DAY),
(1, 'Dal tadka with brown rice',          490, 18.0, 72.0,  8.0, 'dinner',    NOW() - INTERVAL 1 DAY),
(1, 'Poha with peanuts',                  320,  9.0, 55.0,  7.0, 'breakfast', NOW() - INTERVAL 2 DAY),
(1, 'Subway veggie sandwich',             430, 16.0, 62.0, 10.0, 'lunch',     NOW() - INTERVAL 2 DAY),
(1, 'Boiled eggs (2)',                    140, 12.0,  1.0, 10.0, 'snack',     NOW() - INTERVAL 2 DAY),
(1, 'Vegetable khichdi',                  380, 14.0, 60.0,  7.0, 'dinner',    NOW() - INTERVAL 2 DAY),
(2, 'Greek yogurt with berries',          190, 15.0, 20.0,  4.0, 'breakfast', NOW() - INTERVAL 3 HOUR),
(2, 'Quinoa salad with chickpeas',        410, 18.0, 52.0, 12.0, 'lunch',     NOW() - INTERVAL 6 HOUR),
(3, 'Whey protein omelette',              320, 35.0,  5.0, 16.0, 'breakfast', NOW() - INTERVAL 2 HOUR),
(3, 'Chicken breast with sweet potato',   580, 48.0, 45.0,  8.0, 'lunch',     NOW() - INTERVAL 5 HOUR);

-- Workout Logs
INSERT INTO workout_logs (user_id, exercise, duration, intensity, calories_burned, notes, logged_at) VALUES
(1, 'Morning Run (5km)',         35, 'medium', 320, 'Felt great, maintained pace throughout',        NOW() - INTERVAL 0 DAY),
(1, 'Push-ups and Pull-ups',     20, 'high',   180, '4 sets of 15 push-ups, 3 sets of 8 pull-ups',  NOW() - INTERVAL 1 DAY),
(1, 'Cycling',                   45, 'medium', 380, 'Evening ride around the park',                  NOW() - INTERVAL 2 DAY),
(1, 'HIIT Workout',              25, 'high',   310, 'Tabata style, 8 rounds',                        NOW() - INTERVAL 3 DAY),
(1, 'Yoga and Stretching',       40, 'low',    140, 'Focus on flexibility and breathing',            NOW() - INTERVAL 5 DAY),
(1, 'Weight Training - Chest',   50, 'high',   290, 'Bench press, flyes, cable crossovers',          NOW() - INTERVAL 6 DAY),
(2, 'Yoga Flow',                 60, 'low',    200, 'Followed a YouTube vinyasa session',            NOW() - INTERVAL 0 DAY),
(2, 'Brisk Walk (3km)',          30, 'low',    150, 'Morning walk before breakfast',                 NOW() - INTERVAL 1 DAY),
(2, 'Zumba Dance',               45, 'medium', 350, 'So much fun, barely felt like exercise!',       NOW() - INTERVAL 3 DAY),
(3, 'Deadlifts and Squats',      55, 'high',   420, 'New PR on deadlift - 100kg!',                   NOW() - INTERVAL 0 DAY),
(3, 'Swimming (20 laps)',        40, 'medium', 360, 'Good cardio session at the pool',               NOW() - INTERVAL 2 DAY);

-- Water Logs
INSERT INTO water_logs (user_id, amount_ml, logged_at) VALUES
(1, 350, NOW() - INTERVAL 1 HOUR),
(1, 250, NOW() - INTERVAL 3 HOUR),
(1, 500, NOW() - INTERVAL 5 HOUR),
(1, 250, NOW() - INTERVAL 7 HOUR),
(1, 350, NOW() - INTERVAL 9 HOUR),
(2, 250, NOW() - INTERVAL 2 HOUR),
(2, 500, NOW() - INTERVAL 4 HOUR),
(2, 350, NOW() - INTERVAL 6 HOUR),
(3, 500, NOW() - INTERVAL 1 HOUR),
(3, 250, NOW() - INTERVAL 3 HOUR),
(3, 500, NOW() - INTERVAL 8 HOUR);
