-- ============================================================
-- LoanUnlock Database Schema + Seed Data
-- Import: mysql -u root -p loanunlock < database/loanunlock.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `loanunlock`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `loanunlock`;

-- ----------------------------
-- Table: admins
-- ----------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','reviewer') NOT NULL DEFAULT 'reviewer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: users
-- ----------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `mobile` varchar(15) NOT NULL UNIQUE,
  `email` varchar(255) DEFAULT NULL UNIQUE,
  `pan_number` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `preferred_language` varchar(255) NOT NULL DEFAULT 'ENGLISH',
  `employment_type` enum('salaried','business') DEFAULT NULL,
  `monthly_income` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `permissions_granted` tinyint(1) NOT NULL DEFAULT 0,
  `profile_photo` varchar(255) DEFAULT NULL,
  `aadhar_number` varchar(16) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `pincode` varchar(6) DEFAULT NULL,
  `bank_account` varchar(255) DEFAULT NULL,
  `bank_ifsc` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: otps
-- ----------------------------
CREATE TABLE IF NOT EXISTS `otps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile` varchar(15) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `attempts` int NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `otps_mobile_index` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: loan_applications
-- ----------------------------
CREATE TABLE IF NOT EXISTS `loan_applications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id` varchar(255) NOT NULL UNIQUE,
  `user_id` bigint UNSIGNED NOT NULL,
  `requested_amount` decimal(10,2) DEFAULT NULL,
  `approved_amount` decimal(10,2) DEFAULT NULL,
  `tenure_months` int DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `emi_amount` decimal(10,2) DEFAULT NULL,
  `processing_fee` decimal(10,2) NOT NULL DEFAULT 299.00,
  `status` enum('draft','personal_filled','eligibility_checked','payment_pending','payment_done','under_review','approved','rejected','disbursed','closed') NOT NULL DEFAULT 'draft',
  `employment_type` enum('salaried','business') DEFAULT NULL,
  `loan_purpose` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL,
  `disbursed_at` timestamp NULL,
  `credit_score` varchar(255) DEFAULT NULL,
  `is_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `loan_applications_user_id_foreign` (`user_id`),
  KEY `loan_applications_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `loan_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loan_applications_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: payments
-- ----------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `loan_application_id` bigint UNSIGNED NOT NULL,
  `transaction_id` varchar(255) NOT NULL UNIQUE,
  `payment_gateway_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('upi','card','netbanking','wallet') DEFAULT NULL,
  `status` enum('pending','success','failed','refunded') NOT NULL DEFAULT 'pending',
  `gateway_response` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_loan_application_id_foreign` (`loan_application_id`),
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_loan_application_id_foreign` FOREIGN KEY (`loan_application_id`) REFERENCES `loan_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: documents
-- ----------------------------
CREATE TABLE IF NOT EXISTS `documents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `loan_application_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('pan','aadhar_front','aadhar_back','selfie','salary_slip','bank_statement','itr','business_proof','other') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `documents_user_id_foreign` (`user_id`),
  KEY `documents_loan_application_id_foreign` (`loan_application_id`),
  CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_loan_application_id_foreign` FOREIGN KEY (`loan_application_id`) REFERENCES `loan_applications` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: loan_notes
-- ----------------------------
CREATE TABLE IF NOT EXISTS `loan_notes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_application_id` bigint UNSIGNED NOT NULL,
  `admin_id` bigint UNSIGNED NOT NULL,
  `note` text NOT NULL,
  `type` enum('general','approval','rejection','query','disbursement') NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `loan_notes_loan_application_id_foreign` (`loan_application_id`),
  KEY `loan_notes_admin_id_foreign` (`admin_id`),
  CONSTRAINT `loan_notes_loan_application_id_foreign` FOREIGN KEY (`loan_application_id`) REFERENCES `loan_applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loan_notes_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: migrations (Laravel tracker)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Seed: Admin Accounts
-- Password for both = bcrypt('Admin@123')
-- ----------------------------
INSERT INTO `admins` (`name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
('Super Admin',  'admin@loanunlock.com',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 1, NOW(), NOW()),
('Loan Reviewer', 'reviewer@loanunlock.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'reviewer',    1, NOW(), NOW());

-- Note: The hash above is for 'password' (Laravel default test hash).
-- After import, run: php artisan db:seed  to regenerate with Admin@123

-- ----------------------------
-- Seed: Sample User + Application (optional demo data)
-- ----------------------------
INSERT INTO `users` (`name`, `mobile`, `email`, `pan_number`, `date_of_birth`, `state`, `employment_type`, `monthly_income`, `company_name`, `is_verified`, `permissions_granted`, `city`, `pincode`, `created_at`, `updated_at`) VALUES
('Pradip Kumar', '9876543210', 'pradip@example.com', 'ABCDE1234F', '1990-05-15', 'Gujarat', 'salaried', '45000', 'Tech Corp Ltd', 1, 1, 'Rajkot', '360001', NOW(), NOW());
