-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: database
-- Generation Time: Nov 25, 2023 at 01:36 PM
-- Server version: 5.7.29
-- PHP Version: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `account_management`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`%` PROCEDURE `sp_createUser` (IN `first_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `email` VARCHAR(255), IN `password` VARCHAR(255))  BEGIN
    DECLARE IsValidEmail BIT DEFAULT 0;
    
    
    IF email REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+.[A-Za-z]{2,4}$' THEN
        SET IsValidEmail = 1;
    END IF;

    IF IsValidEmail = 1 THEN
        
        INSERT INTO users (first_name, last_name, email, password) VALUES (first_name, last_name, email, password);
    ELSE
        
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid email address';
    END IF;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_getUserById` (IN `userId` INT)  BEGIN

SELECT * FROM users WHERE id = userId;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_getUserInfo` (IN `userEmail` VARCHAR(255))  BEGIN

SELECT * FROM users WHERE email = userEmail;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_userLoginPost` (IN `p_email` VARCHAR(255), IN `p_password` VARCHAR(255))  BEGIN
    DECLARE v_user_id INT;
    DECLARE v_hashed_password VARCHAR(255);

    
    SELECT id, password INTO v_user_id, v_hashed_password
    FROM users
    WHERE email = p_email;

    
    IF v_user_id IS NOT NULL AND BINARY p_password = v_hashed_password THEN
        
        SELECT 'Login successful' AS result;
    ELSE
        
        SELECT 'Invalid email or password. Please try again.' AS result;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created`) VALUES
(1, 'administrator', '2023-11-20 03:21:58'),
(2, 'faculty', '2023-11-20 03:21:58'),
(3, 'student', '2023-11-20 03:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `created`) VALUES
(22, 'Diome Nike', 'Potot', 'diome.halcyonwebdesign@gmail.com', '$2y$10$V3zpGJIhBf7xUc/CWDiIZ.O646oNq21eTEVlHMjC3GvylXi2MfUKe', '2023-11-23 10:34:17'),
(23, 'Diome Nike', 'Potot', 'admin@gmail.com', '$2y$10$Sd8/DGkuCL9KXKOhQUY4luJhdDSWI1XtwoUa8C3I3xQlIJNtZSbbi', '2023-11-23 10:42:12'),
(24, 'Diome Nike', 'Potot', 'system.administrator@staging.saas.halcyondigitalhost.com', '$2y$10$gg/yJKpl2qSYa1j0jZj.ZuvOWjHCKg0f/u8at8IEzZTKWsIsN74G6', '2023-11-23 11:42:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_role_permissions` (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_roles` (`role_id`),
  ADD KEY `fk_users_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permissions` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_users_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
