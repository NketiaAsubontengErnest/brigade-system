-- ========================================================
-- Brigade System Online Database Dump
-- Host: sql302.infinityfree.com | Database: if0_42804650_church
-- Generated: 2026-09-01 18:05:23
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Structure for table `activities`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `officer_in_charge` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Scheduled' CHECK (`status` in ('Scheduled','In Progress','Completed','Cancelled')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_activities_date` (`date`),
  KEY `idx_activities_section` (`section_id`),
  KEY `idx_activities_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `activities`
INSERT INTO `activities` (`id`, `name`, `description`, `date`, `start_time`, `end_time`, `location`, `section_id`, `officer_in_charge`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Weekly Drill', 'Regular drill practice session', '2026-09-01', '14:17:00', '17:00:00', 'Brigade Hall', NULL, NULL, 'Scheduled', '2026-08-31 22:29:16', '2026-09-01 14:16:32');
INSERT INTO `activities` (`id`, `name`, `description`, `date`, `start_time`, `end_time`, `location`, `section_id`, `officer_in_charge`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Bible Study', 'Weekly bible study', '2026-09-02', '16:00:00', '17:30:00', 'Church Hall', NULL, NULL, 'Scheduled', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `activities` (`id`, `name`, `description`, `date`, `start_time`, `end_time`, `location`, `section_id`, `officer_in_charge`, `status`, `created_at`, `updated_at`) VALUES ('3', 'Sports Day', 'Inter-section sports competition', '2026-09-05', '08:00:00', '16:00:00', 'Sports Field', NULL, NULL, 'Scheduled', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `activities` (`id`, `name`, `description`, `date`, `start_time`, `end_time`, `location`, `section_id`, `officer_in_charge`, `status`, `created_at`, `updated_at`) VALUES ('4', 'Weekly Drill', 'Regular drill practice session', '2026-09-07', '15:00:00', '17:00:00', 'Brigade Hall', '1', NULL, 'Scheduled', '2026-09-01 11:46:51', '2026-09-01 11:46:51');
INSERT INTO `activities` (`id`, `name`, `description`, `date`, `start_time`, `end_time`, `location`, `section_id`, `officer_in_charge`, `status`, `created_at`, `updated_at`) VALUES ('5', 'Bible Study', 'Weekly bible study', '2026-09-02', '16:00:00', '17:30:00', 'Church Hall', NULL, NULL, 'Scheduled', '2026-09-01 11:46:51', '2026-09-01 11:46:51');
INSERT INTO `activities` (`id`, `name`, `description`, `date`, `start_time`, `end_time`, `location`, `section_id`, `officer_in_charge`, `status`, `created_at`, `updated_at`) VALUES ('6', 'Sports Day', 'Inter-section sports competition', '2026-09-05', '08:00:00', '16:00:00', 'Sports Field', NULL, NULL, 'Scheduled', '2026-09-01 11:46:51', '2026-09-01 11:46:51');

-- --------------------------------------------------------
-- Structure for table `announcements`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `publish_date` date DEFAULT curdate(),
  `expiry_date` date DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'Published' CHECK (`status` in ('Draft','Published','Archived')),
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_announcements_status` (`status`),
  KEY `idx_announcements_date` (`publish_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `announcements`
INSERT INTO `announcements` (`id`, `title`, `content`, `publish_date`, `expiry_date`, `is_public`, `status`, `created_by`, `created_at`) VALUES ('1', 'Welcome to the New Brigade Year!', 'We are excited to begin a new year of activities. All members are encouraged to register and participate actively.', '2026-08-31', NULL, '1', 'Published', NULL, '2026-08-31 22:29:16');
INSERT INTO `announcements` (`id`, `title`, `content`, `publish_date`, `expiry_date`, `is_public`, `status`, `created_by`, `created_at`) VALUES ('2', 'Welcome to the New Brigade Year!', 'We are excited to begin a new year of activities. All members are encouraged to register and participate actively.', '2026-09-01', NULL, '1', 'Published', NULL, '2026-09-01 11:46:51');

-- --------------------------------------------------------
-- Structure for table `attendance`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Present' CHECK (`status` in ('Present','Absent','Excused','Late')),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`,`member_id`),
  KEY `idx_attendance_session` (`session_id`),
  KEY `idx_attendance_member` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `attendance_sessions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `attendance_sessions`;
CREATE TABLE `attendance_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_attendance_sessions_activity` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `audit_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity` varchar(100) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_audit_logs_user` (`user_id`),
  KEY `idx_audit_logs_entity` (`entity`),
  KEY `idx_audit_logs_date` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `audit_logs`
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('1', '1', 'login', 'user', '1', 'User logged in', '::1', '2026-08-31 22:54:21');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('2', '1', 'logout', 'user', '1', 'User logged out', '::1', '2026-08-31 23:23:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('3', '1', 'login', 'user', '1', 'User logged in', '::1', '2026-08-31 23:44:05');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('4', '1', 'section_deleted', 'section', '2', 'Deleted section', '::1', '2026-08-31 23:48:05');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('5', '1', 'section_deleted', 'section', '5', 'Deleted section', '::1', '2026-08-31 23:48:14');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('6', '1', 'logout', 'user', '1', 'User logged out', '::1', '2026-09-01 00:14:08');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('7', '2', 'login', 'user', '2', 'User logged in', '::1', '2026-09-01 03:25:25');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('8', '2', 'logout', 'user', '2', 'User logged out', '::1', '2026-09-01 03:26:24');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('9', '3', 'login', 'user', '3', 'User logged in', '::1', '2026-09-01 03:26:47');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('10', '3', 'logout', 'user', '3', 'User logged out', '::1', '2026-09-01 10:57:53');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('11', '1', 'login', 'user', '1', 'User logged in', '::1', '2026-09-01 12:12:45');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('12', '1', 'member_created', 'member', '56', 'Created member TestJunior GuardianCheck', '127.0.0.1', '2026-09-01 13:04:40');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('13', '1', 'member_created', 'member', '57', 'Created member TestJunior GuardianCheck', '127.0.0.1', '2026-09-01 13:05:16');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('14', '1', 'member_created', 'member', '58', 'Created member TestSenior NoGuardian', '127.0.0.1', '2026-09-01 13:05:35');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('15', '1', 'member_updated', 'member', '57', 'Updated member #57', '127.0.0.1', '2026-09-01 13:06:01');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('16', '1', 'member_updated', 'member', '57', 'Updated member #57', '127.0.0.1', '2026-09-01 13:06:02');
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `description`, `ip_address`, `created_at`) VALUES ('17', '1', 'member_updated', 'member', '57', 'Updated member #57', '127.0.0.1', '2026-09-01 13:06:02');

-- --------------------------------------------------------
-- Structure for table `awards`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `awards`;
CREATE TABLE `awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_awarded` date DEFAULT curdate(),
  `awarded_by` varchar(200) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_awards_member` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `badges`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `badges`;
CREATE TABLE `badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Inactive')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `badges`
INSERT INTO `badges` (`id`, `name`, `description`, `requirements`, `image`, `status`, `created_at`) VALUES ('1', 'First Aid Badge', 'Awarded for completing first aid training', 'Complete first aid training course', NULL, 'Active', '2026-08-31 22:29:16');
INSERT INTO `badges` (`id`, `name`, `description`, `requirements`, `image`, `status`, `created_at`) VALUES ('2', 'Drill Badge', 'Awarded for excellent drill performance', 'Pass drill assessment with distinction', NULL, 'Active', '2026-08-31 22:29:16');
INSERT INTO `badges` (`id`, `name`, `description`, `requirements`, `image`, `status`, `created_at`) VALUES ('3', 'Service Badge', 'Awarded for community service', 'Complete 20 hours of community service', NULL, 'Active', '2026-08-31 22:29:16');
INSERT INTO `badges` (`id`, `name`, `description`, `requirements`, `image`, `status`, `created_at`) VALUES ('4', 'Leadership Badge', 'Awarded for leadership development', 'Complete leadership training program', NULL, 'Active', '2026-08-31 22:29:16');
INSERT INTO `badges` (`id`, `name`, `description`, `requirements`, `image`, `status`, `created_at`) VALUES ('5', 'First Aid Badge', 'Awarded for completing first aid training', 'Complete first aid training course', NULL, 'Active', '2026-09-01 11:46:51');
INSERT INTO `badges` (`id`, `name`, `description`, `requirements`, `image`, `status`, `created_at`) VALUES ('6', 'Drill Badge', 'Awarded for excellent drill performance', 'Pass drill assessment with distinction', NULL, 'Active', '2026-09-01 11:46:51');
INSERT INTO `badges` (`id`, `name`, `description`, `requirements`, `image`, `status`, `created_at`) VALUES ('7', 'Service Badge', 'Awarded for community service', 'Complete 20 hours of community service', NULL, 'Active', '2026-09-01 11:46:51');
INSERT INTO `badges` (`id`, `name`, `description`, `requirements`, `image`, `status`, `created_at`) VALUES ('8', 'Leadership Badge', 'Awarded for leadership development', 'Complete leadership training program', NULL, 'Active', '2026-09-01 11:46:51');

-- --------------------------------------------------------
-- Structure for table `company_profile`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `company_profile`;
CREATE TABLE `company_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT 'Brigade Company',
  `church_name` varchar(255) DEFAULT NULL,
  `company_number` varchar(50) DEFAULT NULL,
  `motto` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `founded_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `member_number_prefix` varchar(10) DEFAULT 'BGB',
  `currency_symbol` varchar(5) DEFAULT 'GH₵',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `company_profile`
INSERT INTO `company_profile` (`id`, `company_name`, `church_name`, `company_number`, `motto`, `description`, `founded_date`, `address`, `location`, `phone`, `email`, `website`, `logo`, `cover_image`, `mission`, `vision`, `member_number_prefix`, `currency_symbol`, `created_at`, `updated_at`) VALUES ('1', '21st and 24th Accra Boys and Girls Brigade', NULL, NULL, 'Sure and Steadfast', 'A Christian youth organization dedicated to the spiritual, physical, and mental development of young people through activities, training, and community service.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'To develop young people through Christian training, discipline, and service to God and community.', 'To raise a generation of disciplined, God-fearing young people who are responsible citizens and future leaders.', 'BGB', 'GH₵', '2026-08-31 22:29:15', '2026-08-31 22:29:15');

-- --------------------------------------------------------
-- Structure for table `documents`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `dues`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `dues`;
CREATE TABLE `dues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dues_type_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `period` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `applicable_to` varchar(50) DEFAULT 'All Active Members',
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Closed','Cancelled')),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_dues_type` (`dues_type_id`),
  KEY `idx_dues_status` (`status`),
  KEY `idx_dues_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `dues_types`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `dues_types`;
CREATE TABLE `dues_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `default_amount` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Inactive')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `dues_types`
INSERT INTO `dues_types` (`id`, `name`, `description`, `default_amount`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Monthly Dues', 'Regular monthly contribution', '20.00', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `dues_types` (`id`, `name`, `description`, `default_amount`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Annual Dues', 'Annual membership fee', '100.00', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `dues_types` (`id`, `name`, `description`, `default_amount`, `status`, `created_at`, `updated_at`) VALUES ('3', 'Registration Dues', 'One-time registration fee', '50.00', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `dues_types` (`id`, `name`, `description`, `default_amount`, `status`, `created_at`, `updated_at`) VALUES ('4', 'Camp Dues', 'Annual camp contribution', '75.00', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `dues_types` (`id`, `name`, `description`, `default_amount`, `status`, `created_at`, `updated_at`) VALUES ('5', 'Uniform Dues', 'Uniform purchase/installment', '200.00', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `dues_types` (`id`, `name`, `description`, `default_amount`, `status`, `created_at`, `updated_at`) VALUES ('6', 'Training Dues', 'Training program fee', '30.00', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `dues_types` (`id`, `name`, `description`, `default_amount`, `status`, `created_at`, `updated_at`) VALUES ('7', 'Special Contribution', 'Special fundraising', '0.00', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `dues_types` (`id`, `name`, `description`, `default_amount`, `status`, `created_at`, `updated_at`) VALUES ('8', 'Event Dues', 'Event-specific fee', '0.00', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');

-- --------------------------------------------------------
-- Structure for table `event_registrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `event_registrations`;
CREATE TABLE `event_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Registered' CHECK (`status` in ('Registered','Attended','Cancelled')),
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`,`member_id`),
  KEY `idx_event_registrations_event` (`event_id`),
  KEY `idx_event_registrations_member` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `events`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `registration_deadline` date DEFAULT NULL,
  `maximum_participants` int(11) DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT 0.00,
  `cover_image` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Upcoming' CHECK (`status` in ('Upcoming','Ongoing','Completed','Cancelled')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_events_date` (`start_date`),
  KEY `idx_events_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `events`
INSERT INTO `events` (`id`, `name`, `description`, `start_date`, `end_date`, `start_time`, `end_time`, `location`, `registration_deadline`, `maximum_participants`, `fee`, `cover_image`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Annual Brigade Camp 2026', 'Annual camping and training event for all members.', '2026-11-01', '2026-11-04', NULL, NULL, ' Brigade Camp Grounds', NULL, NULL, '50.00', NULL, 'Upcoming', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `events` (`id`, `name`, `description`, `start_date`, `end_date`, `start_time`, `end_time`, `location`, `registration_deadline`, `maximum_participants`, `fee`, `cover_image`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Annual Brigade Camp 2026', 'Annual camping and training event for all members.', '2026-11-01', '2026-11-04', NULL, NULL, ' Brigade Camp Grounds', NULL, NULL, '50.00', NULL, 'Upcoming', '2026-09-01 11:46:51', '2026-09-01 11:46:51');

-- --------------------------------------------------------
-- Structure for table `expenses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL CHECK (`amount` > 0),
  `date` date NOT NULL DEFAULT curdate(),
  `payment_method` varchar(30) DEFAULT NULL CHECK (`payment_method` in ('Cash','Mobile Money','Bank Transfer','Other')),
  `reference` varchar(100) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Recorded' CHECK (`status` in ('Recorded','Approved','Void')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_expenses_date` (`date`),
  KEY `idx_expenses_category` (`category`),
  KEY `idx_expenses_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `gallery_albums`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `gallery_albums`;
CREATE TABLE `gallery_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Archived')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `gallery_images`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `gallery_images`;
CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `caption` varchar(500) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_gallery_images_album` (`album_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `guardians`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `guardians`;
CREATE TABLE `guardians` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `full_name` varchar(200) NOT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `emergency_contact` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_guardians_member` (`member_id`),
  KEY `idx_guardians_member_primary` (`member_id`,`is_primary`),
  KEY `idx_guardians_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `income`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `income`;
CREATE TABLE `income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL CHECK (`amount` > 0),
  `date` date NOT NULL DEFAULT curdate(),
  `payment_method` varchar(30) DEFAULT NULL CHECK (`payment_method` in ('Cash','Mobile Money','Bank Transfer','Other')),
  `reference` varchar(100) DEFAULT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Recorded' CHECK (`status` in ('Recorded','Void')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_income_date` (`date`),
  KEY `idx_income_category` (`category`),
  KEY `idx_income_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `member_badges`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `member_badges`;
CREATE TABLE `member_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `badge_id` int(11) DEFAULT NULL,
  `date_awarded` date DEFAULT curdate(),
  `awarded_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_id` (`member_id`,`badge_id`),
  KEY `idx_member_badges_member` (`member_id`),
  KEY `idx_member_badges_badge` (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `member_dues`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `member_dues`;
CREATE TABLE `member_dues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `dues_id` int(11) DEFAULT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Unpaid' CHECK (`status` in ('Unpaid','Partial','Paid','Overdue')),
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_id` (`member_id`,`dues_id`),
  KEY `idx_member_dues_member` (`member_id`),
  KEY `idx_member_dues_dues` (`dues_id`),
  KEY `idx_member_dues_status` (`status`),
  KEY `idx_member_dues_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `members`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_number` varchar(30) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL CHECK (`gender` in ('Male','Female')),
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_photo` varchar(500) DEFAULT NULL,
  `date_joined` date DEFAULT curdate(),
  `section_id` int(11) DEFAULT NULL,
  `rank` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending' CHECK (`status` in ('Active','Inactive','Suspended','Former','Pending')),
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `verification_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_officer` tinyint(1) DEFAULT 0,
  `position_id` int(11) DEFAULT NULL,
  `rank_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_number` (`member_number`),
  UNIQUE KEY `verification_token` (`verification_token`),
  KEY `idx_members_number` (`member_number`),
  KEY `idx_members_name` (`last_name`,`first_name`),
  KEY `idx_members_section` (`section_id`),
  KEY `idx_members_status` (`status`),
  KEY `idx_members_gender` (`gender`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `members`
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('1', 'BGB-2026-0001', 'Emmanuel', 'Kwame', 'Asante', '2008-03-15', 'Male', '+233240000001', 'emmanuel@example.com', NULL, NULL, '2026-08-31', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('2', 'BGB-2026-0002', 'Abena', 'Afia', 'Mensah', '2007-07-22', 'Female', '+233240000002', 'abena@example.com', NULL, NULL, '2026-08-31', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('3', 'BGB-2026-0003', 'Kwadwo', '', 'Boateng', '2009-01-10', 'Male', '+233240000003', 'kwadwo@example.com', NULL, NULL, '2026-08-31', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('4', 'BGB-2026-0004', 'Akua', 'Serwaa', 'Osei', '2006-11-05', 'Female', '+233240000004', 'akua@example.com', NULL, NULL, '2026-08-31', '3', NULL, 'Active', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('5', 'BGB-2026-0005', 'Kofi', '', 'Amoako', '2008-06-18', 'Male', '+233240000005', 'kofi@example.com', NULL, NULL, '2026-08-31', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('6', 'BGB-2026-0006', 'Esi', 'Abena', 'Darko', '2010-02-28', 'Female', '+233240000006', 'esi@example.com', NULL, NULL, '2026-08-31', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('7', 'BGB-2026-0007', 'Yaw', '', 'Frimpong', '2005-09-12', 'Male', '+233240000007', 'yaw@example.com', NULL, NULL, '2026-08-31', '3', NULL, 'Active', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('8', 'BGB-2026-0008', 'Adwoa', 'Pokuwa', 'Gyamfi', '2007-04-30', 'Female', '+233240000008', 'adwoa@example.com', NULL, NULL, '2026-08-31', NULL, NULL, 'Pending', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('9', 'BGB-2026-0009', 'Nana', 'Kwesi', 'Acheampong', '2009-08-08', 'Male', '+233240000009', 'nana@example.com', NULL, NULL, '2026-08-31', NULL, NULL, 'Active', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('10', 'BGB-2026-0010', 'Efua', '', 'Nyarko', '2006-12-25', 'Female', '+233240000010', 'efua@example.com', NULL, NULL, '2026-08-31', '3', NULL, 'Inactive', NULL, NULL, NULL, '2026-08-31 22:29:16', '2026-08-31 22:29:16', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('21', 'BGB-2026-0011', 'Ebenezer', '', 'Osei', '2009-12-01', 'Male', '+233240000021', 'ebenezer@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:50', '2026-09-01 11:46:50', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('22', 'BGB-2026-0012', 'Isaac', 'Kwesi', 'Tutu', '2006-04-15', 'Male', '+233240000023', 'isaac@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:50', '2026-09-01 11:46:50', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('23', 'BGB-2026-0013', 'Samuel', '', 'Owusu', '2007-08-28', 'Male', '+233240000025', 'samuel@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:50', '2026-09-01 11:46:50', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('24', 'BGB-2026-0014', 'Daniel', 'Kofi', 'Aidoo', '2008-01-07', 'Male', '+233240000027', 'daniel@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:50', '2026-09-01 11:46:50', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('25', 'BGB-2026-0015', 'Michael', '', 'Appiah', '2009-03-12', 'Male', '+233240000029', 'michael@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:50', '2026-09-01 11:46:50', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('26', 'BGB-2026-0016', 'Joseph', 'Kwame', 'Adjei', '2007-06-30', 'Male', '+233240000031', 'joseph@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('27', 'BGB-2026-0017', 'Stephen', '', 'Darko', '2008-09-18', 'Male', '+233240000033', 'stephen@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('28', 'BGB-2026-0018', 'Benjamin', 'Kofi', 'Nkrumah', '2009-02-25', 'Male', '+233240000035', 'benjamin@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('29', 'BGB-2026-0019', 'David', '', 'Agyemang', '2006-11-09', 'Male', '+233240000037', 'david@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('30', 'BGB-2026-0020', 'Patrick', 'Yaw', 'Quartey', '2007-05-14', 'Male', '+233240000039', 'patrick@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('31', 'BGB-2026-0021', 'Anthony', '', 'Cudjoe', '2008-08-22', 'Male', '+233240000041', 'anthony@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('32', 'BGB-2026-0022', 'Abena', 'Afia', 'Mensah', '2007-07-22', 'Female', '+233240000002', 'abena@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('33', 'BGB-2026-0023', 'Akua', 'Serwaa', 'Osei', '2006-11-05', 'Female', '+233240000004', 'akua@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('34', 'BGB-2026-0024', 'Esi', 'Abena', 'Darko', '2010-02-28', 'Female', '+233240000006', 'esi@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('35', 'BGB-2026-0025', 'Adwoa', 'Pokuwa', 'Gyamfi', '2007-04-30', 'Female', '+233240000008', 'adwoa@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Pending', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('36', 'BGB-2026-0026', 'Efua', '', 'Nyarko', '2006-12-25', 'Female', '+233240000010', 'efua@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('37', 'BGB-2026-0027', 'Ama', 'Akosua', 'Sarpong', '2008-04-12', 'Female', '+233240000012', 'ama@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('38', 'BGB-2026-0028', 'Naa', 'Adjeley', 'Oman', '2009-09-03', 'Female', '+233240000014', 'naa@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('39', 'BGB-2026-0029', 'Abena', 'Sampah', 'Adjei', '2007-02-18', 'Female', '+233240000016', 'abena.a@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('40', 'BGB-2026-0030', 'Aisha', '', 'Mohammed', '2008-06-25', 'Female', '+233240000018', 'aisha@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('41', 'BGB-2026-0031', 'Fati', 'Abdulai', 'Ibrahim', '2009-11-14', 'Female', '+233240000020', 'fati@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('42', 'BGB-2026-0032', 'Akosua', '', 'Boakye', '2006-08-07', 'Female', '+233240000022', 'akosua@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('43', 'BGB-2026-0033', 'Adeline', 'Kwakyewaa', 'Osei', '2007-03-29', 'Female', '+233240000024', 'adeline@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('44', 'BGB-2026-0034', 'Gifty', '', 'Ansah', '2008-12-11', 'Female', '+233240000026', 'gifty@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('45', 'BGB-2026-0035', 'Rose', 'Afia', 'Tetteh', '2009-07-06', 'Female', '+233240000028', 'rose@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('46', 'BGB-2026-0036', 'Grace', '', 'Boateng', '2007-10-20', 'Female', '+233240000030', 'grace@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('47', 'BGB-2026-0037', 'Mercy', 'Akua', 'Asare', '2008-05-16', 'Female', '+233240000032', 'mercy@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('48', 'BGB-2026-0038', 'Patricia', '', 'Annor', '2009-01-28', 'Female', '+233240000034', 'patricia@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('49', 'BGB-2026-0039', 'Diana', 'Serwaa', 'Asante', '2006-06-13', 'Female', '+233240000036', 'diana@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('50', 'BGB-2026-0040', 'Juliana', '', 'Mensah', '2007-09-09', 'Female', '+233240000038', 'juliana@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('51', 'BGB-2026-0041', 'Cynthia', 'Abena', 'Quarshie', '2008-02-21', 'Female', '+233240000040', 'cynthia@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('52', 'BGB-2026-0042', 'Priscilla', '', 'Oforiwaa', '2009-04-05', 'Female', '+233240000042', 'priscilla@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('53', 'BGB-2026-0043', 'Elizabeth', 'Kwakyewaa', 'Darkwah', '2007-12-17', 'Female', '+233240000044', 'elizabeth@example.com', NULL, NULL, '2026-09-01', '4', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('54', 'BGB-2026-0044', 'Theresa', '', 'Osei-Bonsu', '2008-07-31', 'Female', '+233240000046', 'theresa@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);
INSERT INTO `members` (`id`, `member_number`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `email`, `address`, `profile_photo`, `date_joined`, `section_id`, `rank`, `status`, `notes`, `user_id`, `verification_token`, `created_at`, `updated_at`, `is_officer`, `position_id`, `rank_id`) VALUES ('55', 'BGB-2026-0045', 'Beatrice', 'Afia', 'Adjei', '2009-10-23', 'Female', '+233240000048', 'beatrice@example.com', NULL, NULL, '2026-09-01', '3', NULL, 'Active', NULL, NULL, NULL, '2026-09-01 11:46:51', '2026-09-01 11:46:51', '0', NULL, NULL);

-- --------------------------------------------------------
-- Structure for table `migrations`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `migrations`
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('1', '001_create_roles_and_permissions.php', '2026-08-31 22:29:15');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('2', '002_create_company_profile.php', '2026-08-31 22:29:15');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('3', '003_create_sections.php', '2026-08-31 22:29:15');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('4', '004_create_members.php', '2026-08-31 22:29:15');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('5', '005_create_officers.php', '2026-08-31 22:29:15');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('6', '006_create_activities_and_attendance.php', '2026-08-31 22:29:16');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('7', '007_create_events.php', '2026-08-31 22:29:16');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('8', '008_create_dues.php', '2026-08-31 22:29:16');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('9', '009_create_finance.php', '2026-08-31 22:29:16');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('10', '010_create_training_badges_awards.php', '2026-08-31 22:29:16');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('11', '011_create_content_tables.php', '2026-08-31 22:29:16');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('12', '012_create_ranks_table.php', '2026-09-01 11:46:40');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('13', '013_add_rank_permissions.php', '2026-09-01 13:00:26');
INSERT INTO `migrations` (`id`, `name`, `executed_at`) VALUES ('14', '014_create_password_resets_and_guardian_role.php', '2026-09-01 13:52:42');

-- --------------------------------------------------------
-- Structure for table `news`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `published_date` date DEFAULT curdate(),
  `status` varchar(20) DEFAULT 'Draft' CHECK (`status` in ('Draft','Published','Archived')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_news_slug` (`slug`),
  KEY `idx_news_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `notifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `officers`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `officers`;
CREATE TABLE `officers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT curdate(),
  `end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Inactive')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_officers_member` (`member_id`),
  KEY `idx_officers_position` (`position_id`),
  KEY `idx_officers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `password_resets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_password_resets_email` (`email`),
  KEY `idx_password_resets_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `payments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `member_dues_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL CHECK (`amount` > 0),
  `payment_method` varchar(30) DEFAULT NULL CHECK (`payment_method` in ('Cash','Mobile Money','Bank Transfer','Other')),
  `reference_number` varchar(100) DEFAULT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL DEFAULT curdate(),
  `recorded_by` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Completed' CHECK (`status` in ('Completed','Pending','Void','Refunded')),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_number` (`receipt_number`),
  KEY `idx_payments_member` (`member_id`),
  KEY `idx_payments_dues` (`member_dues_id`),
  KEY `idx_payments_receipt` (`receipt_number`),
  KEY `idx_payments_date` (`payment_date`),
  KEY `idx_payments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `permissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `permissions`
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('1', 'members.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('2', 'members.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('3', 'members.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('4', 'members.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('5', 'members.approve', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('6', 'attendance.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('7', 'attendance.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('8', 'attendance.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('9', 'activities.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('10', 'activities.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('11', 'activities.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('12', 'activities.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('13', 'events.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('14', 'events.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('15', 'events.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('16', 'events.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('17', 'dues.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('18', 'dues.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('19', 'dues.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('20', 'dues.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('21', 'payments.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('22', 'payments.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('23', 'payments.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('24', 'payments.void', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('25', 'finance.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('26', 'finance.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('27', 'finance.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('28', 'finance.approve', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('29', 'training.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('30', 'training.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('31', 'training.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('32', 'badges.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('33', 'badges.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('34', 'badges.award', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('35', 'awards.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('36', 'awards.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('37', 'announcements.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('38', 'announcements.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('39', 'announcements.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('40', 'announcements.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('41', 'news.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('42', 'news.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('43', 'news.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('44', 'news.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('45', 'gallery.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('46', 'gallery.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('47', 'gallery.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('48', 'documents.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('49', 'documents.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('50', 'documents.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('51', 'reports.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('52', 'reports.export', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('53', 'users.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('54', 'users.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('55', 'users.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('56', 'users.delete', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('57', 'settings.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('58', 'settings.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('59', 'officers.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('60', 'officers.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('61', 'officers.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('62', 'sections.view', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('63', 'sections.create', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('64', 'sections.edit', NULL, '2026-08-31 22:29:15');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('65', 'ranks.view', 'View the list of member ranks', '2026-09-01 13:00:26');
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES ('66', 'ranks.manage', 'Create, edit and remove member ranks', '2026-09-01 13:00:26');

-- --------------------------------------------------------
-- Structure for table `positions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `positions`;
CREATE TABLE `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Inactive')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `positions`
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('1', 'Captain', 'Company commander', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('2', 'Vice Captain', 'Deputy company commander', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('3', 'Secretary', 'Administrative officer', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('4', 'Assistant Secretary', 'Deputy administrative officer', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('5', 'Treasurer', 'Financial officer', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('6', 'Assistant Treasurer', 'Deputy financial officer', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('7', 'Chaplain', 'Spiritual leader', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('8', 'Training Officer', 'Training coordinator', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('9', 'Assistant Training Officer', 'Deputy training coordinator', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('10', 'Welfare Officer', 'Member welfare', 'Active', '2026-08-31 22:29:15');
INSERT INTO `positions` (`id`, `name`, `description`, `status`, `created_at`) VALUES ('11', 'Public Relations Officer', 'External communications', 'Active', '2026-08-31 22:29:15');

-- --------------------------------------------------------
-- Structure for table `ranks`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `ranks`;
CREATE TABLE `ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `level` int(11) DEFAULT 1,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Inactive')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `ranks`
INSERT INTO `ranks` (`id`, `name`, `description`, `level`, `status`, `created_at`, `updated_at`) VALUES ('1', 'Rifleman', 'Basic rank', '1', 'Active', '2026-09-01 11:46:40', '2026-09-01 11:46:40');
INSERT INTO `ranks` (`id`, `name`, `description`, `level`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Lance Corporal', 'First promotion', '2', 'Active', '2026-09-01 11:46:40', '2026-09-01 11:46:40');
INSERT INTO `ranks` (`id`, `name`, `description`, `level`, `status`, `created_at`, `updated_at`) VALUES ('3', 'Corporal', 'Non-commissioned officer', '3', 'Active', '2026-09-01 11:46:40', '2026-09-01 11:46:40');
INSERT INTO `ranks` (`id`, `name`, `description`, `level`, `status`, `created_at`, `updated_at`) VALUES ('4', 'Sergeant', 'Senior NCO', '4', 'Active', '2026-09-01 11:46:40', '2026-09-01 11:46:40');
INSERT INTO `ranks` (`id`, `name`, `description`, `level`, `status`, `created_at`, `updated_at`) VALUES ('5', 'Staff Sergeant', 'Senior staff NCO', '5', 'Active', '2026-09-01 11:46:40', '2026-09-01 11:46:40');
INSERT INTO `ranks` (`id`, `name`, `description`, `level`, `status`, `created_at`, `updated_at`) VALUES ('6', 'Warrant Officer', 'Warrant officer rank', '6', 'Active', '2026-09-01 11:46:40', '2026-09-01 11:46:40');

-- --------------------------------------------------------
-- Structure for table `role_permissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `role_permissions`
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '2');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '4');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '5');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '7');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '11');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '12');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '13');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '15');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '16');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '20');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '22');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '23');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '24');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '25');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '26');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '27');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '28');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '29');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '30');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '31');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '32');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '33');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '34');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '35');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '36');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '37');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '38');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '39');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '40');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '41');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '42');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '43');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '44');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '45');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '46');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '47');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '48');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '49');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '50');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '51');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '52');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '53');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '54');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '55');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '56');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '57');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '58');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '59');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '60');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '61');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '62');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '63');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '64');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '65');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '66');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '2');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '3');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '5');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '7');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '11');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '13');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '15');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '22');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '25');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '29');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '30');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '31');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '32');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '33');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '34');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '35');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '36');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '37');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '38');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '39');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '51');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '52');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '59');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '62');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '65');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '66');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '17');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '18');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '19');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '21');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '22');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '24');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '25');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '26');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '27');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '28');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '51');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '52');

-- --------------------------------------------------------
-- Structure for table `roles`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `roles`
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('1', 'Super Admin', 'Full system access', '2026-08-31 22:29:15', '2026-08-31 22:29:15');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('2', 'Captain', 'Company leader with full operational access', '2026-08-31 22:29:15', '2026-08-31 22:29:15');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('3', 'Secretary', 'Administrative officer', '2026-08-31 22:29:15', '2026-08-31 22:29:15');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('4', 'Treasurer', 'Financial officer', '2026-08-31 22:29:15', '2026-08-31 22:29:15');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('5', 'Training Officer', 'Training and development officer', '2026-08-31 22:29:15', '2026-08-31 22:29:15');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('6', 'Welfare Officer', 'Member welfare officer', '2026-08-31 22:29:15', '2026-08-31 22:29:15');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('7', 'Officer', 'General officer', '2026-08-31 22:29:15', '2026-08-31 22:29:15');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('8', 'Member', 'Regular brigade member', '2026-08-31 22:29:15', '2026-08-31 22:29:15');
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES ('9', 'Guardian', 'Parent/Guardian of brigade members', '2026-09-01 13:52:42', '2026-09-01 13:52:42');

-- --------------------------------------------------------
-- Structure for table `sections`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `age_range` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Inactive')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(50) DEFAULT 'Section',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `sections`
INSERT INTO `sections` (`id`, `name`, `description`, `age_range`, `status`, `created_at`, `updated_at`, `type`) VALUES ('3', 'Junior Brigade', 'Middle section', '12-14', 'Active', '2026-08-31 22:29:15', '2026-08-31 22:29:15', 'Junior');
INSERT INTO `sections` (`id`, `name`, `description`, `age_range`, `status`, `created_at`, `updated_at`, `type`) VALUES ('4', 'Senior Brigade', 'Senior section', '15-18', 'Active', '2026-08-31 22:29:15', '2026-08-31 22:29:15', 'Section');

-- --------------------------------------------------------
-- Structure for table `training_courses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `training_courses`;
CREATE TABLE `training_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `instructor` varchar(200) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Completed','Inactive')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `training_courses`
INSERT INTO `training_courses` (`id`, `name`, `description`, `duration`, `instructor`, `status`, `created_at`, `updated_at`) VALUES ('1', 'First Aid Training', 'Basic first aid certification course', '2 weeks', 'Dr. Mensah', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `training_courses` (`id`, `name`, `description`, `duration`, `instructor`, `status`, `created_at`, `updated_at`) VALUES ('2', 'Drill Leadership', 'Advanced drill command and leadership', '4 weeks', 'Capt. Asante', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `training_courses` (`id`, `name`, `description`, `duration`, `instructor`, `status`, `created_at`, `updated_at`) VALUES ('3', 'Public Speaking', 'Communication and public speaking skills', '3 weeks', 'Mrs. Adjei', 'Active', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `training_courses` (`id`, `name`, `description`, `duration`, `instructor`, `status`, `created_at`, `updated_at`) VALUES ('4', 'First Aid Training', 'Basic first aid certification course', '2 weeks', 'Dr. Mensah', 'Active', '2026-09-01 11:46:51', '2026-09-01 11:46:51');
INSERT INTO `training_courses` (`id`, `name`, `description`, `duration`, `instructor`, `status`, `created_at`, `updated_at`) VALUES ('5', 'Drill Leadership', 'Advanced drill command and leadership', '4 weeks', 'Capt. Asante', 'Active', '2026-09-01 11:46:51', '2026-09-01 11:46:51');
INSERT INTO `training_courses` (`id`, `name`, `description`, `duration`, `instructor`, `status`, `created_at`, `updated_at`) VALUES ('6', 'Public Speaking', 'Communication and public speaking skills', '3 weeks', 'Mrs. Adjei', 'Active', '2026-09-01 11:46:51', '2026-09-01 11:46:51');

-- --------------------------------------------------------
-- Structure for table `training_enrollments`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `training_enrollments`;
CREATE TABLE `training_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT curdate(),
  `completion_date` date DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Enrolled' CHECK (`status` in ('Enrolled','In Progress','Completed','Failed')),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_id` (`member_id`,`course_id`),
  KEY `idx_training_enrollments_member` (`member_id`),
  KEY `idx_training_enrollments_course` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active' CHECK (`status` in ('Active','Inactive','Suspended')),
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_role` (`role_id`),
  KEY `idx_users_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `users`
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role_id`, `status`, `last_login`, `created_at`, `updated_at`) VALUES ('1', 'Super Admin', 'admin@brigade.com', '+233200000000', '$2y$10$vvjrnGsd.SD1dAq9VHGLMeSiLmEJ2/m4fEKjpwkvNC/QlaqD17guK', '1', 'Active', '2026-09-01 12:12:45', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role_id`, `status`, `last_login`, `created_at`, `updated_at`) VALUES ('2', 'Captain James Mensah', 'captain@brigade.com', '+233200000001', '$2y$10$7d3s9AAWc3jPBcWygebaxuMoODnAfx/uo4weoDpU0ysh2NU9YYlqu', '2', 'Active', '2026-09-01 03:25:24', '2026-08-31 22:29:16', '2026-08-31 22:29:16');
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role_id`, `status`, `last_login`, `created_at`, `updated_at`) VALUES ('3', 'Treasurer Grace Adjei', 'treasurer@brigade.com', '+233200000002', '$2y$10$VhKK0MqPJPSBGNV1KFtwtut8oDvlwA7.7U/0wOjag74w0BSYZvhkC', '4', 'Active', '2026-09-01 03:26:47', '2026-08-31 22:29:16', '2026-08-31 22:29:16');

SET FOREIGN_KEY_CHECKS = 1;
