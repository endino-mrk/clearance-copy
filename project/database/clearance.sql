/*
 Navicat Premium Dump SQL

 Source Server         : Pepito
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : clearance

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 15/05/2025 14:45:26
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for clearance_records
-- ----------------------------
DROP TABLE IF EXISTS `clearance_records`;
CREATE TABLE `clearance_records`  (
  `clearance_id` int NOT NULL AUTO_INCREMENT,
  `resident_id` int NULL DEFAULT NULL,
  `occupancy_id` int NOT NULL,
  `status` enum('Pending','Cleared') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rental_fee_status` enum('Pending','Cleared') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fine_status` enum('Pending','Cleared') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `room_status` enum('Pending','Cleared') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `document_status` enum('Pending','Cleared') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_cleared` datetime NULL DEFAULT NULL,
  `cleared_by` int NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`clearance_id`) USING BTREE,
  INDEX `fk_clearance_users`(`resident_id` ASC) USING BTREE,
  INDEX `clearance_records_ibfk_2`(`occupancy_id` ASC) USING BTREE,
  INDEX `cr_ibfk_3`(`cleared_by` ASC) USING BTREE,
  CONSTRAINT `clearance_records_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`resident_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `clearance_records_ibfk_2` FOREIGN KEY (`occupancy_id`) REFERENCES `resident_occupancy` (`occupancy_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cr_ibfk_3` FOREIGN KEY (`cleared_by`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of clearance_records
-- ----------------------------
INSERT INTO `clearance_records` VALUES (1, 2, 1, 'Pending', 'Pending', 'Pending', 'Pending', 'Pending', NULL, NULL, '2025-05-14 20:52:45');
INSERT INTO `clearance_records` VALUES (2, 1, 3, 'Pending', 'Pending', 'Pending', 'Pending', 'Pending', NULL, NULL, '2025-05-15 14:12:13');

-- ----------------------------
-- Table structure for document_submissions
-- ----------------------------
DROP TABLE IF EXISTS `document_submissions`;
CREATE TABLE `document_submissions`  (
  `document_submission_id` int NOT NULL AUTO_INCREMENT,
  `occupancy_id` int NOT NULL,
  `document_id` int NOT NULL,
  `submitted` enum('True','False') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'False',
  `submission_date` datetime NULL DEFAULT NULL,
  `received_by` int NULL DEFAULT NULL,
  PRIMARY KEY (`document_submission_id`) USING BTREE,
  INDEX `ds_ibfk1`(`occupancy_id` ASC) USING BTREE,
  INDEX `ds_ibfk2`(`document_id` ASC) USING BTREE,
  CONSTRAINT `ds_ibfk1` FOREIGN KEY (`occupancy_id`) REFERENCES `resident_occupancy` (`occupancy_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `ds_ibfk2` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of document_submissions
-- ----------------------------
INSERT INTO `document_submissions` VALUES (1, 1, 1, 'False', NULL, NULL);
INSERT INTO `document_submissions` VALUES (2, 1, 2, 'False', NULL, NULL);

-- ----------------------------
-- Table structure for documents
-- ----------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents`  (
  `document_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`document_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of documents
-- ----------------------------
INSERT INTO `documents` VALUES (1, 'Resident Information Sheet', 'A summary of the resident’s personal, academic, and dormitory details used to support clearance processing, including student ID, contact info, room assignment, and emergency contact.');
INSERT INTO `documents` VALUES (2, 'Waiver and Liability Form', 'A document confirming the resident’s acknowledgment of dorm policies and release of the dormitory from liability for personal loss or injury.');

-- ----------------------------
-- Table structure for fines
-- ----------------------------
DROP TABLE IF EXISTS `fines`;
CREATE TABLE `fines`  (
  `fine_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`fine_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of fines
-- ----------------------------
INSERT INTO `fines` VALUES (1, 'Noise Violation', 'Disturbance reported due to excessive noise.', 300.00);
INSERT INTO `fines` VALUES (2, 'Damage to Property', 'Resident caused damage to dorm property.', 1500.00);
INSERT INTO `fines` VALUES (3, 'Unauthorized Guest', 'Resident brought in guest without approval.', 400.00);
INSERT INTO `fines` VALUES (4, 'Improper Trash Disposal', 'Resident did not follow proper garbage disposal rules.', 200.00);
INSERT INTO `fines` VALUES (5, 'Unreturned Key', 'Resident failed to return room key upon checkout.', 250.00);
INSERT INTO `fines` VALUES (6, 'Violation of Curfew', 'Resident violated dormitory curfew hours.', 350.00);
INSERT INTO `fines` VALUES (7, 'Smoking in Room', 'Resident smoked inside dorm room, violating policy.', 1000.00);
INSERT INTO `fines` VALUES (8, 'Absent During Event', 'Resident was absent without prior notice during a scheduled dormitory event.', 200.00);
INSERT INTO `fines` VALUES (9, 'Did Not Complete Day Duties', 'Resident failed to complete assigned day duties as per dormitory policy.', 150.00);

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `permission_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`permission_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of permissions
-- ----------------------------

-- ----------------------------
-- Table structure for rental_fee_payments
-- ----------------------------
DROP TABLE IF EXISTS `rental_fee_payments`;
CREATE TABLE `rental_fee_payments`  (
  `rental_fee_payment_id` int NOT NULL AUTO_INCREMENT,
  `occupancy_id` int NOT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `receipt_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_paid` date NOT NULL,
  `recorded_by` int NOT NULL,
  `date_recorded` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rental_fee_payment_id`) USING BTREE,
  INDEX `rfp_ibfk1`(`occupancy_id` ASC) USING BTREE,
  INDEX `rfp_ibfk2`(`recorded_by` ASC) USING BTREE,
  CONSTRAINT `rfp_ibfk1` FOREIGN KEY (`occupancy_id`) REFERENCES `resident_occupancy` (`occupancy_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `rfp_ibfk2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of rental_fee_payments
-- ----------------------------

-- ----------------------------
-- Table structure for resident_fines
-- ----------------------------
DROP TABLE IF EXISTS `resident_fines`;
CREATE TABLE `resident_fines`  (
  `resident_fine_id` int NOT NULL AUTO_INCREMENT,
  `occupancy_id` int NULL DEFAULT NULL,
  `fine_id` int NOT NULL,
  `status` enum('Paid','Unpaid','Waived','Partially Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `violation_date` date NOT NULL,
  `date_issued` datetime NOT NULL,
  `issued_by` int NOT NULL,
  `amount_paid` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `date_paid` datetime NULL DEFAULT NULL,
  `updated_by` int NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`resident_fine_id`) USING BTREE,
  INDEX `fk_rf_fines`(`fine_id` ASC) USING BTREE,
  INDEX `fk_rf_users`(`issued_by` ASC) USING BTREE,
  INDEX `rf_ibfk3`(`occupancy_id` ASC) USING BTREE,
  INDEX `rf_ibfk5`(`updated_by` ASC) USING BTREE,
  CONSTRAINT `resident_fines_ibfk_1` FOREIGN KEY (`fine_id`) REFERENCES `fines` (`fine_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `rf_ibfk3` FOREIGN KEY (`occupancy_id`) REFERENCES `resident_occupancy` (`occupancy_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `rf_ibfk5` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of resident_fines
-- ----------------------------
INSERT INTO `resident_fines` VALUES (1, 1, 2, 'Unpaid', '2025-05-12', '2025-05-12 16:04:24', 4, 0.00, NULL, NULL, NULL);
INSERT INTO `resident_fines` VALUES (2, 1, 8, 'Unpaid', '2025-05-12', '2025-05-12 16:04:51', 4, 0.00, NULL, NULL, NULL);
INSERT INTO `resident_fines` VALUES (13, 3, 9, 'Unpaid', '2025-05-14', '2025-05-14 14:39:00', 1, 0.00, NULL, NULL, NULL);
INSERT INTO `resident_fines` VALUES (15, 3, 2, 'Unpaid', '2025-05-14', '2025-05-14 15:32:33', 1, 0.00, NULL, NULL, NULL);
INSERT INTO `resident_fines` VALUES (16, 3, 2, 'Unpaid', '2025-05-14', '2025-05-14 15:32:49', 1, 0.00, NULL, NULL, NULL);
INSERT INTO `resident_fines` VALUES (19, 3, 2, 'Unpaid', '2025-05-15', '2025-05-15 08:12:13', 1, 0.00, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for resident_occupancy
-- ----------------------------
DROP TABLE IF EXISTS `resident_occupancy`;
CREATE TABLE `resident_occupancy`  (
  `occupancy_id` int NOT NULL AUTO_INCREMENT,
  `semester_id` int NOT NULL,
  `resident_id` int NOT NULL,
  `room_id` int NOT NULL,
  `room_status` enum('Not Vacated','Vacated') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rental_balance` decimal(10, 2) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`occupancy_id`) USING BTREE,
  INDEX `fk_occupancy_semesters`(`semester_id` ASC) USING BTREE,
  INDEX `fk_occupancy_residents`(`resident_id` ASC) USING BTREE,
  INDEX `fk_occupancy_rooms`(`room_id` ASC) USING BTREE,
  CONSTRAINT `fk_occupancy_residents` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`resident_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_occupancy_rooms` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_occupancy_semesters` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of resident_occupancy
-- ----------------------------
INSERT INTO `resident_occupancy` VALUES (1, 2, 2, 11, 'Not Vacated', 7500.00, 1, '2025-05-14 20:53:49');
INSERT INTO `resident_occupancy` VALUES (3, 2, 1, 10, 'Not Vacated', 0.00, 1, '2025-05-12 16:09:36');

-- ----------------------------
-- Table structure for residents
-- ----------------------------
DROP TABLE IF EXISTS `residents`;
CREATE TABLE `residents`  (
  `resident_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `student_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`resident_id`) USING BTREE,
  UNIQUE INDEX `unique_student_id`(`student_id` ASC) USING BTREE,
  INDEX `fk_users_residents`(`user_id` ASC) USING BTREE,
  CONSTRAINT `residents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of residents
-- ----------------------------
INSERT INTO `residents` VALUES (1, 2, '2023300000', 1, NULL, NULL);
INSERT INTO `residents` VALUES (2, 9, '2023301792', 1, NULL, NULL);

-- ----------------------------
-- Table structure for role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions`  (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`) USING BTREE,
  INDEX `fk_permissions`(`permission_id` ASC) USING BTREE,
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of role_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `name` enum('Resident','Treasurer','Assistant','Manager') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`role_id`) USING BTREE,
  UNIQUE INDEX `unique_role_name`(`name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (2, 'Resident');
INSERT INTO `roles` VALUES (3, 'Treasurer');
INSERT INTO `roles` VALUES (4, 'Assistant');
INSERT INTO `roles` VALUES (1, 'Manager');

-- ----------------------------
-- Table structure for rooms
-- ----------------------------
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms`  (
  `room_id` int NOT NULL AUTO_INCREMENT,
  `number` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `monthly_rental` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`room_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of rooms
-- ----------------------------
INSERT INTO `rooms` VALUES (10, 'A1', 1500.00);
INSERT INTO `rooms` VALUES (11, 'A2', 2500.00);
INSERT INTO `rooms` VALUES (12, 'A3', 3500.00);

-- ----------------------------
-- Table structure for semesters
-- ----------------------------
DROP TABLE IF EXISTS `semesters`;
CREATE TABLE `semesters`  (
  `semester_id` int NOT NULL AUTO_INCREMENT,
  `academic_year` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `term` enum('First','Second','Summer') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`semester_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of semesters
-- ----------------------------
INSERT INTO `semesters` VALUES (1, '2024-2025', 'First', 0, '2024-08-01', '2024-12-15');
INSERT INTO `semesters` VALUES (2, '2024-2025', 'Second', 1, '2025-01-05', '2025-05-23');
INSERT INTO `semesters` VALUES (3, '2025-2026', 'First', 0, '2025-08-11', '2025-12-15');

-- ----------------------------
-- Table structure for user_roles
-- ----------------------------
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles`  (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`) USING BTREE,
  INDEX `fk_roles`(`role_id` ASC) USING BTREE,
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of user_roles
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Maria', 'Clara', '', 'manager@gmail.com', '', '123password', 1, '2025-05-03 10:37:30', NULL, NULL);
INSERT INTO `users` VALUES (2, 'Juanita', 'Dela Cruz', '', 'juanita.delacruz@gmail.com', '', '123password', 1, '2025-05-03 11:27:46', NULL, NULL);
INSERT INTO `users` VALUES (3, 'Michaela', 'Endino', '', 'endino@gmail.com', '', '$2y$10$O4Z61omO8hV0wbbkvFADlO6P55uXMC2c2KzoQx0A4J8i3eiM.oJoC', 1, '2025-05-04 05:12:47', '2025-05-04 05:12:47', NULL);
INSERT INTO `users` VALUES (4, 'John', 'Wick', 'Smith', 'jw@gmail.com', '09501254647', '12345678', 1, '2025-05-11 17:16:30', '2025-05-11 17:16:30', NULL);
INSERT INTO `users` VALUES (9, 'Juan', 'Dela Cruz', 'Santiago', 'jc@gmail.com', '09878799797', '12345678', 1, '2025-05-11 18:38:17', '2025-05-11 18:38:17', NULL);
INSERT INTO `users` VALUES (10, 'Jose', 'Rizal', 'Mercado', 'jrizal@gmail.com', '03501852034', '12345678', 1, '2025-05-11 18:41:48', '2025-05-11 18:41:48', NULL);
INSERT INTO `users` VALUES (11, 'Jose', 'Rizal', 'Mercado', 'jrizal@gmail.com', '03501852034', '12345678', 1, '2025-05-11 18:41:52', '2025-05-11 18:41:52', NULL);
INSERT INTO `users` VALUES (12, 'Jose', 'Rizal', 'Mercado', 'jrizal@gmail.com', '03501852034', '12345678', 1, '2025-05-11 18:47:05', '2025-05-11 18:47:05', NULL);

SET FOREIGN_KEY_CHECKS = 1;
