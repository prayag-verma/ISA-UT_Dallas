-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 26, 2024 at 01:08 AM
-- Server version: 10.3.39-MariaDB-cll-lve
-- PHP Version: 8.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prayag_db_isa`
--

-- --------------------------------------------------------

--
-- Table structure for table `degrees`
--

CREATE TABLE `degrees` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `degrees`
--

INSERT INTO `degrees` (`id`, `name`) VALUES
(2, 'Masters of Science'),
(8, 'PHD'),
(5, 'Under Grad');

-- --------------------------------------------------------

--
-- Table structure for table `email_categories`
--

CREATE TABLE `email_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_categories`
--

INSERT INTO `email_categories` (`id`, `name`) VALUES
(1, 'Birthday Wishes'),
(6, 'ISA Event'),
(2, 'New Officer Onboard'),
(3, 'Officer\'s Email Change'),
(4, 'Officer\'s Password Change'),
(5, 'Officer\'s Position Change'),
(30, 'Prayag\'s Birthday'),
(18, 'Test Category 10'),
(19, 'Test Category 11'),
(20, 'Test Category 12'),
(21, 'Test Category 13'),
(22, 'Test Category 14'),
(23, 'Test Category 15'),
(24, 'Test Category 16'),
(25, 'Test Category 17'),
(26, 'Test Category 18'),
(27, 'Test Category 19'),
(9, 'Test Category 2');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `receiver_position_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `trigger_auto_email` enum('Yes','No') DEFAULT 'No',
  `scheduled_email` enum('Daily','Weekly','Bi-Weekly','Monthly','Annually','Birthday Wishes') DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `category_id`, `receiver_position_id`, `subject`, `body`, `trigger_auto_email`, `scheduled_email`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, 'ISA Wishes You a Happy Birthday', 'Dear {first_name},\r\n\r\nWishing you a very happy birthday, from the whole team of Indian Student Association! Enjoy this day because you deserve nothing less than the best â€” thatâ€™s what you bring to work every day. Hereâ€™s to another wonderful year!', 'No', NULL, 30, '2024-10-02 11:14:45', '2024-10-02 11:14:45'),
(3, 6, NULL, 'ISA - Event Update', 'Dear {First_name},\r\n\r\nItâ€™s that time of year!\r\n\r\nAs we head into the holiday season, we want to thank each and every one of you for your tremendous work and effort over the year. \r\n\r\nTo celebrate ISAâ€™s achievements and kick off the holiday season in style, weâ€™ll be throwing our annual holiday bash on [date] at [location]. You can RSVP here [Embedded hyperlink] if you donâ€™t want to miss out on tasty treats, prizes, and a surprise or two! \r\n\r\nSeasonâ€™s Greetings to you, your families, and communities.\r\n\r\nSincerely,\r\nTeam ISA', 'No', NULL, 30, '2024-10-02 11:17:58', '2024-10-02 11:17:58'),
(4, 4, NULL, 'ISA Login Password changed', 'You ISA password has been changed, please report it to the ISA technology officer if you think itâ€™s not you!', 'No', NULL, 30, '2024-10-02 12:41:34', '2024-10-19 23:59:39'),
(5, 3, NULL, 'ISA Email Updated', 'Your email address is changed please report it to the technology officer at the earliest if you think itâ€™s not done by you!', 'Yes', NULL, 30, '2024-10-02 12:43:34', '2024-10-19 23:59:32'),
(6, 2, NULL, 'Greetings From Team ISA', 'Welcome to the team ISA', 'Yes', NULL, 30, '2024-10-02 12:45:09', '2024-10-03 06:36:52'),
(7, 5, NULL, 'ISA Designation Updated', 'Dear team, \r\n\r\nYour position has been updated!', 'Yes', NULL, 30, '2024-10-02 12:46:14', '2024-10-04 00:49:25'),
(38, 30, 12, 'happy Birthdat {first_name} ', 'Hi {first_name} ,\r\n\r\nISA wishes you a happiest birthday!\r\n\r\nRegards.\r\nISA \r\n{isa_logo}', 'Yes', 'Birthday Wishes', 30, '2024-10-07 04:10:24', '2024-10-07 04:12:21');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `category`, `image_path`, `description`, `date`) VALUES
(2, 'Cricket', 'Fall', '../uploads/events/Cricket.jpg', 'Cricket Tournament', '2024-09-25'),
(3, 'Fall Bash', 'Fall', '../uploads/events/FallBash.jpg', 'Music, Singing, Dance, Drama, etc.', '2024-09-17'),
(4, 'Ganesh Chaturthi', 'Fall', '../uploads/events/Ganesh_Chaturthi_2021.jpg', 'Ganesh Chaturthi Celebration', '2024-08-16'),
(5, 'Independence Day', 'Fall', '../uploads/events/IndependenceDay.jpg', 'Celebrating Indian Independence Day', '2024-08-15'),
(6, 'Airport PickUp', 'Spring', '../uploads/events/Isa-utd-airport-pickup (1).jpg', 'International Student\'s Airport PickUp', '2024-07-01'),
(7, 'ISA Orientation', 'Spring', '../uploads/events/ISAorientation22.png', 'ISA Orientation', '2024-08-19'),
(16, 'Test Events 6', 'Other', 'uploads/events/Prayag_Verma.jpg', 'Test Event desc 6', '2024-05-05');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `year` varchar(9) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `year`, `semester`, `image_path`, `order`) VALUES
(2, '2024-2025', 'Fall', '../uploads/gallery/DSC_7192.jpg', 1),
(5, '2024-2025', 'Fall', '../uploads/gallery/Screenshot 2024-07-25 121556.png', 2),
(6, '2024-2025', 'Other', 'uploads/gallery/1700791729916.jpeg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `FirstName` varchar(33) NOT NULL,
  `LastName` varchar(33) NOT NULL,
  `Email` varchar(55) NOT NULL,
  `Phone` varchar(15) NOT NULL,
  `Purpose` varchar(155) NOT NULL,
  `Message` varchar(1000) DEFAULT NULL,
  `Submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `name`) VALUES
(36, 'Community Outreach'),
(22, 'Content Officer'),
(35, 'Dance Officer'),
(38, 'Event Officer'),
(21, 'Events and Logistics Officer'),
(25, 'Events Officer (Anchoring)'),
(16, 'Events Officer (Music)'),
(10, 'General Secretary'),
(19, 'Innovation Officer'),
(39, 'ISA Advisor'),
(14, 'Marketing Officer'),
(8, 'President'),
(18, 'Public Relations Officer'),
(23, 'SOC Officer'),
(13, 'Social Media Officer'),
(27, 'Sports Officer'),
(12, 'Technology Officer'),
(11, 'Treasurer'),
(33, 'Undergrad Outreach Officer'),
(9, 'Vice President'),
(41, 'Volunteer');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `more_info_text` varchar(255) NOT NULL,
  `more_info_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `icon`, `description`, `more_info_text`, `more_info_url`) VALUES
(2, 'fas fa-book', 'Incoming Students Handbook', 'More Info', 'https://www.isautd.org/resources/documents/ISA_Incoming_Handbook.pdf'),
(4, 'fas fa-file-pdf', 'Incoming Students Handbook', 'More Info', 'https://isautd.org/resources/documents/ISA_Incoming_Handbook.pdf'),
(5, 'fas fa-car', 'Pickup Request', 'More Info', 'https://docs.google.com/forms/d/e/1FAIpQLSciqWI632FiEVDNYLdNfzPqUJxzXeJq8zaajzhuRlihsk4NBQ/viewform'),
(6, 'fa fa-shopping-cart', 'Post Arrival', 'More Info', 'https://isautd.org/resources/postArrival.html'),
(7, 'fa fa-plane', 'Pre Departure', 'More Info', 'https://isautd.org/resources/preDeparture.html'),
(8, 'fas fa-plane-departure', 'Travel', 'More Info', 'https://isautd.org/resources/travelInfo.html'),
(9, 'fa fa-home', 'Housing', 'More info', 'https://isautd.org/resources/housing.html'),
(10, 'fas fa-landmark', 'Bank', 'More info', 'https://isautd.org/resources/bofA.html'),
(11, 'fas fa-book-open', 'Textbooks', 'More Info', 'https://isautd.org/resources/textBooks.html'),
(12, 'fas fa-house-user', 'Temporary Housing', 'More Info', 'https://cometmail-my.sharepoint.com/:x:/g/personal/mtp220000_utdallas_edu/EbRPgCCCk4BOu1SRiU_4IJoBpSKTBrvZ9gl1oi6UY42z8w'),
(13, 'fa fa-question', 'FAQ', 'More Info', 'https://isautd.org/resources/faq.html'),
(14, 'fas fa-test', 'Test Resource 1', 'Click Here', 'https://profile.aimtocode.com'),
(15, 'fas fa-user', 'Test Resource 2', 'See More', 'https://aimtocode.com'),
(16, 'New Resource', 'New resource', 'Know More', 'https://aimtocode.com'),
(17, 'New rs', 'New resource 4', 'click here', 'https://aimtocode.com');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'advisor'),
(3, 'employee'),
(4, 'volunteer'),
(5, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `logo_path`) VALUES
(1, 'uploads/logo/66fce15a4ed79_isa-fab.png');

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` int(11) NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `degree` varchar(155) DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `expected_grad_date` date DEFAULT NULL,
  `officer_ranking` int(11) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `status` enum('active','retired','disabled') DEFAULT 'active',
  `retirement_date` date DEFAULT NULL,
  `can_read_messages` tinyint(1) DEFAULT 0,
  `can_export_messages` tinyint(1) DEFAULT 0,
  `can_delete_messages` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `dob`, `email`, `mobile`, `degree`, `major`, `joining_date`, `expected_grad_date`, `officer_ranking`, `position`, `profile_picture`, `role`, `status`, `retirement_date`, `can_read_messages`, `can_export_messages`, `can_delete_messages`) VALUES
(30, 'prayag', 'e86f78a8a3caf0b60d8e74e5942aa6d86dc150cd3c03338aef25b7d2d7e3acc7', 'Prayag', 'Verma', '1998-10-04', 'ppv220002@utdallas.edu', '9452747200', 'Masters of Science', 'ITM', '2024-05-22', '2024-12-31', 10, 'Technology Officer', 'uploads/profile_pictures/66db46c7b47a2_profile_pic_.png', 'admin', 'active', NULL, 1, 1, 1),
(68, 'test', '$2y$10$VkYw9BU/pIpVsQR0HnkQwuraX6RxmIU37NOrV1/8RkI6Lxb/6vflm', 'Testing', 'Purpose', '2000-02-01', 'test@gmail.com', '1231231230', 'Under Grad', 'Arts', '2024-01-01', '2025-05-31', 5, 'Treasurer', NULL, 'employee', 'retired', '2024-10-03', 1, 1, 1),
(69, 'advisor', '$2y$10$MorbdIMPpGPrRb6PhIRq3.OMKaE3srX1H76OXamfpONhPFHVkauqq', 'Test', 'Advisor', '2024-10-05', 'advisor@utdallas.edu', '945-274-420', 'Masters of Science', 'ITM', '2024-09-29', '2025-12-31', 10, 'ISA Advisor', 'uploads/profile_pictures/66f9e0fe43ef1_advisor.png', 'advisor', 'retired', '2024-10-03', 1, 0, 0),
(70, 'testvolunteer', '$2y$10$Uw810rA4Qgps7IeVS5jDP.ttoasotbfEEj7x0eK3Wutn98fbPHrTW', 'Testing', 'Volunteer', '2024-10-03', 'v@isa.org', '9452747200', 'PHD', 'Nano Technology', '2024-10-03', '2025-02-28', 2, 'Volunteer', NULL, 'volunteer', 'active', NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `visitor_count`
--

CREATE TABLE `visitor_count` (
  `id` int(6) UNSIGNED NOT NULL,
  `count` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `visitor_count`
--

INSERT INTO `visitor_count` (`id`, `count`) VALUES
(1, 2752);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `degrees`
--
ALTER TABLE `degrees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `email_categories`
--
ALTER TABLE `email_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_email_template_receiver` (`receiver_position_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_role` (`role`);

--
-- Indexes for table `visitor_count`
--
ALTER TABLE `visitor_count`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `degrees`
--
ALTER TABLE `degrees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `email_categories`
--
ALTER TABLE `email_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `visitor_count`
--
ALTER TABLE `visitor_count`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD CONSTRAINT `fk_email_template_category` FOREIGN KEY (`category_id`) REFERENCES `email_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_email_template_receiver` FOREIGN KEY (`receiver_position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_email_template_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role`) REFERENCES `roles` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
