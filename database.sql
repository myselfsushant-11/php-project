-- ============================================================
-- CINEFILE — Movie Ticket Booking System
-- Database schema + seed data
-- Import this file in phpMyAdmin before running the project.
-- ============================================================

CREATE DATABASE IF NOT EXISTS movie_ticket_booking
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE movie_ticket_booking;

-- ----------------------------------------------------------
-- USERS
-- ----------------------------------------------------------
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20) NOT NULL,
  password VARCHAR(255) NOT NULL,
  profile_image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- ADMINS
-- ----------------------------------------------------------
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin -> email: admin@cinefile.com / password: Admin@123
INSERT INTO admins (name, email, password) VALUES
('Studio Admin', 'admin@cinefile.com', '$2b$10$ZFigAQtDdAS7LwneJxsKC.8Tt6BjBVzZDuBeoLMXLujcknxKxJW0u');

-- ----------------------------------------------------------
-- MOVIES
-- ----------------------------------------------------------
CREATE TABLE movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  genre VARCHAR(150) NOT NULL,
  language VARCHAR(80) NOT NULL,
  duration INT NOT NULL COMMENT 'minutes',
  rating DECIMAL(2,1) DEFAULT 0.0,
  poster VARCHAR(255) DEFAULT NULL,
  backdrop VARCHAR(255) DEFAULT NULL,
  description TEXT,
  trailer_url VARCHAR(255) DEFAULT NULL,
  release_date DATE DEFAULT NULL,
  status ENUM('now_showing','coming_soon') DEFAULT 'now_showing',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FULLTEXT KEY ft_search (title, genre, language)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- HALLS
-- ----------------------------------------------------------
CREATE TABLE halls (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  rows_count INT NOT NULL DEFAULT 5,
  seats_per_row INT NOT NULL DEFAULT 8,
  total_seats INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- SHOWTIMES
-- ----------------------------------------------------------
CREATE TABLE showtimes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  movie_id INT NOT NULL,
  hall_id INT NOT NULL,
  show_date DATE NOT NULL,
  show_time TIME NOT NULL,
  ticket_price DECIMAL(10,2) NOT NULL DEFAULT 350.00,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
  FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE,
  INDEX idx_movie_date (movie_id, show_date)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- SEATS (generated per showtime)
-- ----------------------------------------------------------
CREATE TABLE seats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  showtime_id INT NOT NULL,
  seat_row VARCHAR(2) NOT NULL,
  seat_number INT NOT NULL,
  seat_label VARCHAR(6) NOT NULL,
  status ENUM('available','booked') DEFAULT 'available',
  FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_seat (showtime_id, seat_label)
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- BOOKINGS
-- ----------------------------------------------------------
CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_code VARCHAR(30) NOT NULL UNIQUE,
  user_id INT NOT NULL,
  showtime_id INT NOT NULL,
  customer_name VARCHAR(120) NOT NULL,
  customer_email VARCHAR(150) NOT NULL,
  customer_phone VARCHAR(20) NOT NULL,
  total_seats INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('confirmed','cancelled') DEFAULT 'confirmed',
  payment_status ENUM('unpaid','confirmed') DEFAULT 'confirmed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- BOOKING_SEATS
-- ----------------------------------------------------------
CREATE TABLE booking_seats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  seat_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- SEED DATA: Halls
-- ----------------------------------------------------------
INSERT INTO halls (name, rows_count, seats_per_row, total_seats) VALUES
('Hall 1 — Marquee', 5, 8, 40),
('Hall 2 — Auteur', 6, 10, 60),
('Hall 3 — Vérité', 4, 8, 32);

-- ----------------------------------------------------------
-- SEED DATA: Movies
-- ----------------------------------------------------------
INSERT INTO movies (title, genre, language, duration, rating, poster, backdrop, description, trailer_url, release_date, status) VALUES
('Nocturne Drift', 'Sci-Fi, Thriller', 'English', 132, 8.6, 'assets/images/movies/poster1.jpg', 'assets/images/movies/backdrop1.jpg', 'A salvage pilot uncovers a signal that rewrites everything she believed about her sister''s disappearance among the outer rings.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '2026-07-10', 'now_showing'),
('The Last Reel', 'Drama', 'English', 118, 8.2, 'assets/images/movies/poster2.jpg', 'assets/images/movies/backdrop2.jpg', 'An aging projectionist fights to save the last analog theater in the city, one final showing at a time.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '2026-06-20', 'now_showing'),
('Crimson Monsoon', 'Action, Romance', 'Nepali', 145, 7.9, 'assets/images/movies/poster3.jpg', 'assets/images/movies/backdrop3.jpg', 'Two rival street racers find themselves entangled in a monsoon-soaked love story across Kathmandu''s neon streets.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '2026-05-01', 'now_showing'),
('Glasswing', 'Mystery', 'English', 109, 8.0, 'assets/images/movies/poster4.jpg', 'assets/images/movies/backdrop4.jpg', 'A detective with synesthesia solves crimes by the colors of sound — until a case turns silent.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '2026-04-15', 'now_showing'),
('Paper Moons', 'Animation, Family', 'English', 98, 8.4, 'assets/images/movies/poster5.jpg', 'assets/images/movies/backdrop5.jpg', 'A folded-paper world comes alive when a child origami-maker discovers her creations can dream.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '2026-09-12', 'coming_soon'),
('Iron Meridian', 'Action, Sci-Fi', 'English', 140, 0.0, 'assets/images/movies/poster6.jpg', 'assets/images/movies/backdrop6.jpg', 'A stranded engineer must reboot a dying space station before its orbit collapses.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '2026-10-03', 'coming_soon');

-- ----------------------------------------------------------
-- SEED DATA: Showtimes (today + next 2 days for the first 4 movies)
-- ----------------------------------------------------------
INSERT INTO showtimes (movie_id, hall_id, show_date, show_time, ticket_price) VALUES
(1, 1, CURDATE(), '10:00:00', 350.00),
(1, 1, CURDATE(), '13:30:00', 350.00),
(1, 2, CURDATE(), '18:30:00', 400.00),
(1, 1, CURDATE() + INTERVAL 1 DAY, '11:00:00', 350.00),
(1, 2, CURDATE() + INTERVAL 1 DAY, '16:00:00', 400.00),

(2, 2, CURDATE(), '11:00:00', 350.00),
(2, 2, CURDATE(), '16:00:00', 350.00),
(2, 3, CURDATE() + INTERVAL 1 DAY, '19:00:00', 380.00),

(3, 3, CURDATE(), '12:00:00', 320.00),
(3, 3, CURDATE(), '20:00:00', 320.00),
(3, 1, CURDATE() + INTERVAL 2 DAY, '17:30:00', 320.00),

(4, 1, CURDATE(), '15:00:00', 350.00),
(4, 2, CURDATE() + INTERVAL 1 DAY, '21:00:00', 350.00);

-- ----------------------------------------------------------
-- Auto-generate seats for every showtime above based on hall layout
-- ----------------------------------------------------------
-- (Handled by PHP includes/functions.php::generateSeatsForShowtime()
--  when an admin creates a showtime through the app. For the seed
--  showtimes above, run the following procedure once after import.)

DELIMITER $$
CREATE PROCEDURE seed_generate_seats()
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE v_showtime_id INT;
  DECLARE v_hall_id INT;
  DECLARE v_rows INT;
  DECLARE v_cols INT;
  DECLARE cur CURSOR FOR SELECT id, hall_id FROM showtimes;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO v_showtime_id, v_hall_id;
    IF done THEN
      LEAVE read_loop;
    END IF;

    SELECT rows_count, seats_per_row INTO v_rows, v_cols FROM halls WHERE id = v_hall_id;

    SET @r = 1;
    WHILE @r <= v_rows DO
      SET @row_letter = CHAR(64 + @r);
      SET @c = 1;
      WHILE @c <= v_cols DO
        INSERT IGNORE INTO seats (showtime_id, seat_row, seat_number, seat_label, status)
        VALUES (v_showtime_id, @row_letter, @c, CONCAT(@row_letter, @c), 'available');
        SET @c = @c + 1;
      END WHILE;
      SET @r = @r + 1;
    END WHILE;
  END LOOP;
  CLOSE cur;
END$$
DELIMITER ;

CALL seed_generate_seats();
DROP PROCEDURE seed_generate_seats;

-- Mark a couple of seats as booked for demo purposes
UPDATE seats SET status = 'booked' WHERE showtime_id = 1 AND seat_label IN ('A1','A2','B5');
