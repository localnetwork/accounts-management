-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: database
-- Generation Time: Nov 27, 2023 at 12:54 PM
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

CREATE DEFINER=`root`@`%` PROCEDURE `sp_createUserProfile` (IN `first_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `address` VARCHAR(255), IN `birthday` VARCHAR(255), IN `user_id` INT)  BEGIN

INSERT INTO profile_data (first_name, last_name, address, birthday, user_id) VALUES (first_name, last_name, address, birthday, user_id);

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_getAllUsers` ()  BEGIN

SELECT id, first_name, last_name, email FROM users;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_getRoles` ()  BEGIN

SELECT * FROM roles;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_getUserById` (IN `userId` INT)  BEGIN

SELECT first_name, last_name, email FROM users WHERE id = userId;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_getUserInfo` (IN `userEmail` VARCHAR(255))  BEGIN

SELECT * FROM users WHERE email = userEmail;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_getUserRole` (IN `uid` INT)  BEGIN

SELECT role FROM users WHERE uid = id;

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

CREATE DEFINER=`root`@`%` PROCEDURE `sp_userWithEmailExist` (IN `userEmail` VARCHAR(255))  BEGIN

SELECT COUNT(*) FROM users WHERE userEmail = email; 

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
-- Table structure for table `profile_data`
--

CREATE TABLE `profile_data` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `birthday` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profile_data`
--

INSERT INTO `profile_data` (`id`, `first_name`, `last_name`, `birthday`, `address`, `user_id`) VALUES
(1, 'asd', 'asd', 'test', 'test', 5);

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
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` int(11) NOT NULL DEFAULT '3',
  `status` int(11) DEFAULT NULL,
  `user_status` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `created`, `role`, `status`, `user_status`) VALUES
(5, 'Diome Nike', 'Potot', 'diome.halcyonwebdesign@gmail.com', '$2y$10$MAWmtKBqUpbAbo8hrP34MO9zu7fyWmw4cjPz/wnoqFW0i01of1wf2', '2023-11-27 12:50:39', 3, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_status`
--

CREATE TABLE `user_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_status`
--

INSERT INTO `user_status` (`id`, `name`) VALUES
(0, 'disabled'),
(1, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile_data`
--
ALTER TABLE `profile_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_profile_data_user` (`user_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_role_id` (`role`),
  ADD KEY `fk_users_status` (`status`),
  ADD KEY `fk_user_status` (`user_status`);

--
-- Indexes for table `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile_data`
--
ALTER TABLE `profile_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_status`
--
ALTER TABLE `user_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `profile_data`
--
ALTER TABLE `profile_data`
  ADD CONSTRAINT `fk_profile_data_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permissions` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_role_id` FOREIGN KEY (`role`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `fk_user_status` FOREIGN KEY (`user_status`) REFERENCES `user_status` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
