-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: u743529483_tcm
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(60) DEFAULT NULL,
  `audience` enum('college','beginners','working','general') NOT NULL DEFAULT 'general',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_categories_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Programming Foundations','programming-foundations','Core programming logic, languages and fundamentals','bi-code-square','beginners',1,'active','2026-10-16 01:43:24'),(2,'Web & Mobile Development','web-mobile-development','Modern full stack, frontend, backend and mobile apps','bi-window-stack','college',2,'active','2026-10-16 01:43:24'),(3,'Data & Analytics','data-analytics','Data analysis, SQL, Power BI, Python and ML foundations','bi-graph-up','college',3,'active','2026-10-16 01:43:24'),(4,'AI & Automation','ai-automation','AI tools, Generative AI, AI agents and workflow automation','bi-robot','working',4,'active','2026-10-16 01:43:24'),(5,'Cloud & DevOps','cloud-devops','Cloud infrastructure, Docker, CI/CD, AWS and security','bi-cloud-check','working',5,'active','2026-10-16 01:43:24'),(6,'Career & DSA','career-dsa','Data structures, algorithms, problem solving and career','bi-briefcase','college',6,'active','2026-10-16 01:43:24'),(7,'Design & Business','design-business','UI/UX design with Figma, digital marketing and growth','bi-palette','beginners',7,'active','2026-10-16 01:43:24');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificates`
--

DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `certificate_number` varchar(60) NOT NULL,
  `title` varchar(190) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `issued_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cert_number` (`certificate_number`),
  KEY `idx_cert_user` (`user_id`),
  KEY `fk_cert_course` (`course_id`),
  CONSTRAINT `fk_cert_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cert_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificates`
--

LOCK TABLES `certificates` WRITE;
/*!40000 ALTER TABLE `certificates` DISABLE KEYS */;
/*!40000 ALTER TABLE `certificates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_group_members`
--

DROP TABLE IF EXISTS `chat_group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_group_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `joined_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_gm` (`group_id`,`user_id`),
  KEY `idx_gm_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_group_members`
--

LOCK TABLES `chat_group_members` WRITE;
/*!40000 ALTER TABLE `chat_group_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_group_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_groups`
--

DROP TABLE IF EXISTS `chat_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `type` enum('help','group') NOT NULL DEFAULT 'group',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_groups`
--

LOCK TABLES `chat_groups` WRITE;
/*!40000 ALTER TABLE `chat_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `body` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  `file_type` enum('image','file') DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cm_group` (`group_id`),
  KEY `idx_cm_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_messages`
--

LOCK TABLES `chat_messages` WRITE;
/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(190) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') NOT NULL DEFAULT 'new',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_batches`
--

DROP TABLE IF EXISTS `course_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_type` enum('course','workshop') NOT NULL DEFAULT 'course',
  `item_id` bigint(20) unsigned NOT NULL,
  `batch_code` varchar(60) NOT NULL,
  `batch_title` varchar(190) NOT NULL,
  `registration_opens_at` datetime DEFAULT NULL,
  `registration_closes_at` datetime DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 30,
  `seats_left` int(11) NOT NULL DEFAULT 30,
  `status` enum('upcoming','registration_open','registration_closed','in_progress','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `timezone` varchar(40) NOT NULL DEFAULT 'Asia/Kolkata',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_batch_code` (`batch_code`),
  KEY `idx_batch_item` (`item_type`,`item_id`),
  KEY `idx_batch_status` (`status`),
  KEY `idx_batch_starts` (`starts_at`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_batches`
--

LOCK TABLES `course_batches` WRITE;
/*!40000 ALTER TABLE `course_batches` DISABLE KEYS */;
INSERT INTO `course_batches` VALUES (1,'course',13,'FULL-STA-B1','Full Stack Web Development (MERN) — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(2,'course',14,'FRONTEND-B1','Frontend Development with React — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(3,'course',15,'PYTHON-P-B1','Python Programming & Automation — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(4,'course',16,'SQL-DATA-B1','SQL & Database Fundamentals — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(5,'course',17,'DSA-PROB-B1','DSA & Problem Solving with Java — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(6,'course',18,'AWS-CLOU-B1','AWS Cloud & Deployment Fundamentals — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(7,'course',19,'DIGITAL--B1','Digital Skills & Computer Fundamentals — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(8,'course',20,'BUILD-YO-B1','Build Your First Website — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(9,'course',21,'AI-TOOLS-B1','AI Tools for Study & Work — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(10,'course',22,'TECH-CAR-B1','Tech Career Launchpad — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(11,'course',23,'AI-AUTOM-B1','AI Automation with No-Code Tools — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(12,'course',24,'DATA-ANA-B1','Data Analytics with Excel, SQL & Power BI — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(13,'course',25,'REACT-NE-B1','React & Next.js Frontend Engineering — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(14,'course',26,'BACKEND--B1','Backend Development with Node.js & APIs — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(15,'course',27,'MOBILE-A-B1','Mobile App Development with React Native — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(16,'course',28,'GENERATI-B1','Generative AI & AI Agent Engineering — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(17,'course',29,'DOCKER-D-B1','Docker, DevOps & CI/CD Essentials — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(18,'course',30,'UI-UX-DE-B1','UI/UX Design with Figma — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(19,'course',31,'SOFTWARE-B1','Software Testing & QA with Playwright — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(20,'course',33,'CORE-JAV-B1','Core Java & Object-Oriented Programming — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(21,'course',34,'DATA-SCI-B1','Data Science & Machine Learning Foundations — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(22,'course',35,'SYSTEM-D-B1','System Design & Microservices Foundations — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(23,'course',36,'CYBERSEC-B1','Cybersecurity & Ethical Security Foundations — Batch 1','2026-10-13 15:14:40','2026-10-17 23:59:59','2026-10-18 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-13 15:14:41','2026-10-13 15:14:41'),(24,'workshop',10,'WS-PYTHON-S','Python Starter Workshop — Cohort 1','2026-10-13 15:15:37','2026-10-17 13:00:00','2026-10-17 19:00:00',NULL,50,24,'registration_open',1,'Asia/Kolkata','2026-10-13 15:15:38','2026-10-13 15:15:38'),(25,'workshop',11,'WS-FRONTEND','7-Day Frontend Website Challenge — Cohort 1','2026-10-13 15:15:37','2026-10-17 13:00:00','2026-10-17 19:00:00',NULL,50,24,'registration_open',1,'Asia/Kolkata','2026-10-13 15:15:38','2026-10-13 15:15:38'),(26,'workshop',12,'WS-AI-TOOLS','AI Tools for Everyday Work — Cohort 1','2026-10-13 15:15:37','2026-10-17 13:00:00','2026-10-17 19:00:00',NULL,50,24,'registration_open',1,'Asia/Kolkata','2026-10-13 15:15:38','2026-10-13 15:15:38'),(27,'workshop',13,'WS-FIND-YOU','Find Your Tech Career Path — Cohort 1','2026-10-13 15:15:37','2026-10-17 13:00:00','2026-10-17 19:00:00',NULL,50,24,'registration_open',1,'Asia/Kolkata','2026-10-13 15:15:38','2026-10-13 15:15:38'),(28,'course',37,'ARTIFICI-B1','Artificial Intelligence & Machine Learning (AI & ML) Masterclass — Batch 1','2026-10-14 10:49:22','2026-10-18 23:59:59','2026-10-19 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-14 10:49:23','2026-10-14 10:49:23'),(29,'course',38,'AI-MACHI-B1','AI & Machine Learning Fundamentals — Batch 1','2026-10-14 11:01:55','2026-10-18 23:59:59','2026-10-19 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-14 11:01:56','2026-10-14 11:01:56'),(30,'course',39,'PROMPT-E-B1','Prompt Engineering & Generative AI — Batch 1','2026-10-14 11:01:55','2026-10-18 23:59:59','2026-10-19 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-14 11:01:56','2026-10-14 11:01:56'),(31,'course',40,'PYTHON-F-B1','Python for AI & ML — Batch 1','2026-10-14 11:01:55','2026-10-18 23:59:59','2026-10-19 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-14 11:01:56','2026-10-14 11:01:56'),(32,'course',41,'AI-AGENT-B1','AI Agents & Workflow Automation — Batch 1','2026-10-14 11:01:55','2026-10-18 23:59:59','2026-10-19 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-14 11:01:56','2026-10-14 11:01:56'),(33,'course',42,'CHATGPT--B1','ChatGPT, Gemini & AI Productivity Tools — Batch 1','2026-10-14 11:01:55','2026-10-18 23:59:59','2026-10-19 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-14 11:01:56','2026-10-14 11:01:56'),(34,'course',43,'MACHINE--B1','Machine Learning with Python Projects — Batch 1','2026-10-14 11:01:55','2026-10-18 23:59:59','2026-10-19 19:00:00',NULL,30,14,'registration_open',1,'Asia/Kolkata','2026-10-14 11:01:56','2026-10-14 11:01:56');
/*!40000 ALTER TABLE `course_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_lessons`
--

DROP TABLE IF EXISTS `course_lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_lessons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint(20) unsigned NOT NULL,
  `title` varchar(220) NOT NULL,
  `type` enum('live','video','project','reading','quiz') NOT NULL DEFAULT 'live',
  `duration_minutes` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `is_preview` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_lessons_module` (`module_id`),
  CONSTRAINT `fk_lesson_module` FOREIGN KEY (`module_id`) REFERENCES `course_modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1591 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_lessons`
--

LOCK TABLES `course_lessons` WRITE;
/*!40000 ALTER TABLE `course_lessons` DISABLE KEYS */;
INSERT INTO `course_lessons` VALUES (1,1,'How the web works – Browsers, Servers, HTTP','live',NULL,1,1),(2,1,'Setting up your dev environment (VS Code)','live',NULL,2,0),(3,1,'Introduction to HTML – Structure & Semantics','live',NULL,3,0),(4,1,'Project: Build a Personal Profile Page','project',NULL,4,0),(5,3,'Variables, Data Types & Operators','live',NULL,1,1),(6,3,'Functions, Scope & Closures','live',NULL,2,0),(7,3,'DOM Manipulation & Events','live',NULL,3,0),(8,3,'Project: Interactive Quiz App','project',NULL,4,0),(9,4,'Introduction to React & JSX','live',NULL,1,0),(10,4,'React Hooks – useState, useEffect','live',NULL,2,0),(11,4,'Project: E-Commerce Product Listing App','project',NULL,3,0),(1083,353,'Web Fundamentals & Semantic Markup','live',60,1,1),(1084,353,'Flexbox & CSS Grid Mastering','live',60,2,0),(1085,353,'Tailwind CSS Component Architecture','live',60,3,0),(1086,353,'Project: Responsive Landing Page','live',60,4,0),(1087,354,'ES6+ Syntax, Scope & Closures','live',60,1,1),(1088,354,'Promises, Async/Await & Fetch API','live',60,2,0),(1089,354,'DOM Manipulation & Event Loop','live',60,3,0),(1090,354,'Project: Real-time Weather App','live',60,4,0),(1091,355,'Components, Props & Lifecycle','live',60,1,1),(1092,355,'React Hooks (useState, useEffect, useMemo)','live',60,2,0),(1093,355,'React Router v6 Navigation','live',60,3,0),(1094,355,'Context API & State Management','live',60,4,0),(1095,355,'Project: E-Commerce Storefront','live',60,5,0),(1096,356,'Node.js Runtime & Architecture','live',60,1,1),(1097,356,'Express Server Setup & Middleware','live',60,2,0),(1098,356,'JWT Authentication & Security','live',60,3,0),(1099,356,'API Rate Limiting & Validation','live',60,4,0),(1100,357,'NoSQL Data Modeling with Mongoose','live',60,1,1),(1101,357,'Aggregation Pipelines & Indexing','live',60,2,0),(1102,357,'CRUD Operations & Relationships','live',60,3,0),(1103,357,'Project: Collaborative Task Manager API','live',60,4,0),(1104,358,'Connecting React with Node Backend','live',60,1,1),(1105,358,'Stateful Checkout & Payment Webhooks','live',60,2,0),(1106,358,'Docker Containerization & Render/Vercel Deploy','live',60,3,0),(1107,358,'Final Capstone Project Presentation','live',60,4,0),(1108,359,'Semantic HTML5 & Modern CSS3','live',60,1,1),(1109,359,'Mobile-First Layouts with Flexbox & Grid','live',60,2,0),(1110,359,'Responsive Breakpoints & Media Queries','live',60,3,0),(1111,360,'Core JS, Array Methods & Destructuring','live',60,1,1),(1112,360,'DOM Manipulation & Event Listeners','live',60,2,0),(1113,360,'Async JavaScript & REST API Integration','live',60,3,0),(1114,361,'JSX & Component Hierarchy','live',60,1,1),(1115,361,'State & Props Handling','live',60,2,0),(1116,361,'React Hooks: useState, useEffect, useRef','live',60,3,0),(1117,362,'Building Reusable Design Systems','live',60,1,1),(1118,362,'Performance Tuning & Lazy Loading','live',60,2,0),(1119,362,'Deploying to Vercel/Netlify','live',60,3,0),(1120,363,'Data Types, Variables & Conditionals','live',60,1,1),(1121,363,'Loops, Functions & Scope','live',60,2,0),(1122,363,'Data Structures: Lists, Dictionaries, Sets','live',60,3,0),(1123,364,'Classes, Objects & Methods','live',60,1,1),(1124,364,'Inheritance & Encapsulation','live',60,2,0),(1125,364,'Error Handling & Custom Exceptions','live',60,3,0),(1126,365,'File I/O & OS Scripting','live',60,1,1),(1127,365,'Excel & CSV Automation with Pandas','live',60,2,0),(1128,365,'Web Scraping with BeautifulSoup & Requests','live',60,3,0),(1129,366,'Building an Automated Email/Notification Bot','live',60,1,1),(1130,366,'API Interaction & JSON Parsing','live',60,2,0),(1131,366,'Final Project Review','live',60,3,0),(1132,367,'Relational DB Architecture','live',60,1,1),(1133,367,'Tables, Primary Keys & Constraints','live',60,2,0),(1134,367,'Data Types & Schema Creation','live',60,3,0),(1135,368,'SELECT, Filtering & Sorting','live',60,1,1),(1136,368,'Aggregate Functions & GROUP BY','live',60,2,0),(1137,368,'INNER, LEFT, RIGHT & FULL Joins','live',60,3,0),(1138,369,'Subqueries & Common Table Expressions (CTEs)','live',60,1,1),(1139,369,'Window Functions (RANK, ROW_NUMBER)','live',60,2,0),(1140,369,'Indexes & Query Execution Plans','live',60,3,0),(1141,370,'Time & Space Complexity (Big-O)','live',60,1,1),(1142,370,'Two Pointers & Sliding Window Techniques','live',60,2,0),(1143,370,'Binary Search & Sorting Algorithms','live',60,3,0),(1144,371,'Singly & Doubly Linked Lists','live',60,1,1),(1145,371,'Stacks, Queues & Monotonic Stacks','live',60,2,0),(1146,371,'Hashing & Hash Tables','live',60,3,0),(1147,372,'Binary Trees & Binary Search Trees','live',60,1,1),(1148,372,'Tree Traversals (DFS, BFS)','live',60,2,0),(1149,372,'Graph Representations & Dijkstra Algorithm','live',60,3,0),(1150,373,'Recursion & Backtracking','live',60,1,1),(1151,373,'Dynamic Programming Fundamentals','live',60,2,0),(1152,373,'Mock Coding Interview Practice','live',60,3,0),(1153,374,'Cloud Computing Models & Regions','live',60,1,1),(1154,374,'Identity & Access Management (IAM)','live',60,2,0),(1155,374,'EC2 Instances & Security Groups','live',60,3,0),(1156,375,'S3 Buckets & CloudFront CDN','live',60,1,1),(1157,375,'Virtual Private Cloud (VPC) & Subnets','live',60,2,0),(1158,375,'Elastic Load Balancing (ELB)','live',60,3,0),(1159,376,'AWS Lambda & API Gateway','live',60,1,1),(1160,376,'RDS Databases & Backups','live',60,2,0),(1161,376,'Live Web App Deployment Project','live',60,3,0),(1162,377,'Hardware, OS & File Systems','live',60,1,1),(1163,377,'Keyboard Shortcuts & Productivity','live',60,2,0),(1164,377,'Command Prompt / Terminal Basics','live',60,3,0),(1165,378,'How the Internet Works & DNS','live',60,1,1),(1166,378,'Cloud Drives & Collaboration Tools','live',60,2,0),(1167,378,'Cyber Safety & Password Security','live',60,3,0),(1168,379,'Tags, Attributes, Headings & Images','live',60,1,1),(1169,379,'Forms, Inputs & Links','live',60,2,0),(1170,379,'Structuring a Clean Portfolio Page','live',60,3,0),(1171,380,'Colors, Typography & Box Model','live',60,1,1),(1172,380,'Flexbox Alignment & Layouts','live',60,2,0),(1173,380,'Free Web Hosting & Going Live','live',60,3,0),(1174,381,'LLM Mechanics & Effective Prompt Frameworks','live',60,1,1),(1175,381,'Role-Based Prompting & Few-Shot Learning','live',60,2,0),(1176,381,'Iterative Prompt Refinement','live',60,3,0),(1177,382,'Synthesizing Research Papers & Books','live',60,1,1),(1178,382,'Creating Presentations & Reports with AI','live',60,2,0),(1179,382,'Coding Assistance & Debugging Workflows','live',60,3,0),(1180,383,'ATS Resume Optimization & Action Verbs','live',60,1,1),(1181,383,'LinkedIn Profile Crafting & Networking Strategy','live',60,2,0),(1182,383,'GitHub Profile & Project Showcase','live',60,3,0),(1183,384,'Behavioral STAR Method Answers','live',60,1,1),(1184,384,'Technical Screening Walkthroughs','live',60,2,0),(1185,384,'Salary Negotiation & Offer Assessment','live',60,3,0),(1186,385,'APIs, Webhooks & Triggers Explained','live',60,1,1),(1187,385,'Make.com & Zapier Flow Design','live',60,2,0),(1188,385,'Data Routing, Filtering & Transforms','live',60,3,0),(1189,386,'OpenAI API Automation for Emails & Reports','live',60,1,1),(1190,386,'Building an Automated Social Media Agent','live',60,2,0),(1191,386,'CRM & Payment Notification Pipelines','live',60,3,0),(1192,387,'XLOOKUP, INDEX/MATCH & Data Cleaning','live',60,1,1),(1193,387,'Pivot Tables, Slicers & Dynamic Charts','live',60,2,0),(1194,387,'Power Query Data Transformation','live',60,3,0),(1195,388,'Relational Data Extraction & Filtering','live',60,1,1),(1196,388,'Multi-table Joins & Grouped Metrics','live',60,2,0),(1197,388,'Window Functions for Cohort Analysis','live',60,3,0),(1198,389,'Data Modeling (Star Schema)','live',60,1,1),(1199,389,'DAX Measures & Time Intelligence','live',60,2,0),(1200,389,'Publishing Interactive Business Dashboards','live',60,3,0),(1201,390,'Server vs Client Components','live',60,1,1),(1202,390,'File-based Routing & Layouts','live',60,2,0),(1203,390,'Server Actions & Form Handling','live',60,3,0),(1204,391,'Static vs Dynamic Rendering (ISR, SSR)','live',60,1,1),(1205,391,'Optimistic UI & Suspense','live',60,2,0),(1206,391,'SEO, OpenGraph & Core Web Vitals','live',60,3,0),(1207,392,'Authentication Integration with NextAuth','live',60,1,1),(1208,392,'Payment Integration & State Management','live',60,2,0),(1209,392,'Production Deployment on Vercel','live',60,3,0),(1210,393,'Event Loop, Streams & Buffer Internals','live',60,1,1),(1211,393,'Express Clean Architecture & Error Handlers','live',60,2,0),(1212,393,'Validation, Logging & Environment Config','live',60,3,0),(1213,394,'PostgreSQL Schema & Prisma ORM','live',60,1,1),(1214,394,'Redis In-Memory Caching & Session Storage','live',60,2,0),(1215,394,'Database Transactions & Concurrency','live',60,3,0),(1216,395,'Rate Limiting, Helmet & Security Headers','live',60,1,1),(1217,395,'Unit & Integration Testing with Jest','live',60,2,0),(1218,395,'Dockerizing & Hosting on AWS EC2','live',60,3,0),(1219,396,'Expo Tooling & Project Setup','live',60,1,1),(1220,396,'Native Components & Flexbox Layouts','live',60,2,0),(1221,396,'Lists, ScrollViews & FlatList Performance','live',60,3,0),(1222,397,'React Navigation (Stack & Tabs)','live',60,1,1),(1223,397,'Camera, Geolocation & Local Storage','live',60,2,0),(1224,397,'Push Notifications Integration','live',60,3,0),(1225,398,'Global State Management (Zustand)','live',60,1,1),(1226,398,'Offline Synchronization & Cache','live',60,2,0),(1227,398,'Building APKs & Play Store Preparation','live',60,3,0),(1228,399,'Embeddings, Tokenization & API Architectures','live',60,1,1),(1229,399,'Chunking Strategies & Context Windows','live',60,2,0),(1230,399,'OpenAI & Anthropic Function Calling','live',60,3,0),(1231,400,'Vector Databases & Similarity Search','live',60,1,1),(1232,400,'HyDE & Re-Ranking Retrievers','live',60,2,0),(1233,400,'Evaluating RAG with Ragas Framework','live',60,3,0),(1234,401,'Tool Calling & Planning Loops','live',60,1,1),(1235,401,'LangGraph Multi-Agent Workflows','live',60,2,0),(1236,401,'Deploying Agent APIs to Cloud','live',60,3,0),(1237,402,'Linux File System, Permissions & SSH','live',60,1,1),(1238,402,'Bash Scripting for Automation','live',60,2,0),(1239,402,'Process & Package Management','live',60,3,0),(1240,403,'Dockerfile Optimization & Multi-Stage Builds','live',60,1,1),(1241,403,'Docker Networking & Persistent Volumes','live',60,2,0),(1242,403,'Docker Compose Multi-Container Stacks','live',60,3,0),(1243,404,'GitHub Actions Workflows & Secrets','live',60,1,1),(1244,404,'Automated Testing & Build Verification','live',60,2,0),(1245,404,'Zero-Downtime Deployment with NGINX','live',60,3,0),(1246,405,'User Personas, Journey Maps & Information Architecture','live',60,1,1),(1247,405,'Low-Fidelity Wireframing & Usability Testing','live',60,2,0),(1248,406,'Auto-Layout & Responsive Components','live',60,1,1),(1249,406,'Design Systems (Tokens, Colors, Typography)','live',60,2,0),(1250,406,'Interactive Micro-interactions & Prototypes','live',60,3,0),(1251,407,'Test Cases, Bug Life Cycle & Test Plans','live',60,1,1),(1252,407,'Unit vs Integration vs E2E Testing','live',60,2,0),(1253,408,'Locators, Assertions & Page Object Model (POM)','live',60,1,1),(1254,408,'API Testing & Mocking Responses','live',60,2,0),(1255,408,'Running Headless Tests in CI/CD','live',60,3,0),(1256,409,'Keyword Research & Search Intent','live',60,1,1),(1257,409,'On-Page Optimization & Technical SEO Audits','live',60,2,0),(1258,409,'Link Building & Content Strategy','live',60,3,0),(1259,410,'GA4 Event Tracking & Funnels','live',60,1,1),(1260,410,'Meta Ads Campaign Architecture & Targeting','live',60,2,0),(1261,410,'Conversion Rate Optimization (CRO)','live',60,3,0),(1262,411,'JVM Architecture & Syntax Basics','live',60,1,1),(1263,411,'Conditionals, Loops & Methods','live',60,2,0),(1264,411,'Arrays & String Manipulation','live',60,3,0),(1265,412,'Encapsulation, Polymorphism & Abstraction','live',60,1,1),(1266,412,'Java Collections (List, Set, Map)','live',60,2,0),(1267,412,'File I/O & Exception Handling','live',60,3,0),(1268,413,'Linear Algebra & Statistics Essentials','live',60,1,1),(1269,413,'NumPy Numerical Arrays & Vectorization','live',60,2,0),(1270,413,'Pandas Data Wrangling & Exploratory Data Analysis','live',60,3,0),(1271,414,'Linear & Logistic Regression','live',60,1,1),(1272,414,'Decision Trees & Random Forests','live',60,2,0),(1273,414,'Feature Engineering & Cross Validation','live',60,3,0),(1274,415,'K-Means Clustering & PCA','live',60,1,1),(1275,415,'Deploying ML Models with Streamlit & FastAPI','live',60,2,0),(1276,415,'Capstone Predictive Modeling Project','live',60,3,0),(1277,416,'Vertical vs Horizontal Scaling','live',60,1,1),(1278,416,'Load Balancing & Consistent Hashing','live',60,2,0),(1279,416,'Caching Strategies (Write-Through, Cache-Aside)','live',60,3,0),(1280,417,'Database Replication & Sharding','live',60,1,1),(1281,417,'CAP Theorem & Eventual Consistency','live',60,2,0),(1282,417,'Message Queues (Kafka, RabbitMQ)','live',60,3,0),(1283,418,'Designing a URL Shortener (TinyURL)','live',60,1,1),(1284,418,'Designing a Rate Limiter','live',60,2,0),(1285,418,'Designing a Live Chat System (WhatsApp)','live',60,3,0),(1286,419,'TCP/IP, OSI Model & Port Scanning (Nmap)','live',60,1,1),(1287,419,'Wireshark Packet Analysis','live',60,2,0),(1288,419,'Authentication & Cryptography Basics','live',60,3,0),(1289,420,'SQL Injection & Broken Access Control','live',60,1,1),(1290,420,'Cross-Site Scripting (XSS) & CSRF','live',60,2,0),(1291,420,'Security Hardening & Remediation','live',60,3,0),(1292,421,'Python for High-Performance Computing: NumPy, Vectorization & Matrix Math','live',60,1,1),(1293,421,'Data Wrangling & Feature Exploration: Pandas, Seaborn & Matplotlib','live',60,2,0),(1294,421,'Essential Mathematics for Machine Learning: Linear Algebra & Vector Calculus','live',60,3,0),(1295,421,'Probability Distributions, Hypothesis Testing & Inferential Statistics','live',60,4,0),(1296,421,'Project: Exploratory Data Analysis (EDA) on Real-World Financial & Health Datasets','live',60,5,0),(1297,422,'Linear Regression, Gradient Descent Optimization & Regularization (Lasso/Ridge)','live',60,1,1),(1298,422,'Classification Algorithms: Logistic Regression, Decision Trees & SVMs','live',60,2,0),(1299,422,'Ensemble Learning: Random Forests, AdaBoost & Extreme Gradient Boosting (XGBoost)','live',60,3,0),(1300,422,'Distance-Based & Probabilistic Models: K-Nearest Neighbors & Naive Bayes','live',60,4,0),(1301,422,'Project: Customer Churn & Loan Default Prediction Engine','live',60,5,0),(1302,423,'Clustering Architectures: K-Means, Hierarchical & DBSCAN Algorithms','live',60,1,1),(1303,423,'Dimensionality Reduction: Principal Component Analysis (PCA) & t-SNE','live',60,2,0),(1304,423,'Feature Engineering, Handling Imbalanced Data (SMOTE) & Outlier Detection','live',60,3,0),(1305,423,'Project: E-Commerce Customer Segmentation & Market Basket Analysis','live',60,4,0),(1306,424,'Neural Network Foundations: Perceptrons, Multi-Layer Perceptrons & Backpropagation','live',60,1,1),(1307,424,'Activation Functions, Loss Formulations & Modern Optimizers (AdamW, RMSprop)','live',60,2,0),(1308,424,'PyTorch Deep Dive: Tensors, Autograd, Custom Datasets & Training Pipelines','live',60,3,0),(1309,424,'Project: Deep Neural Network for High-Dimensional Disease Classification','live',60,4,0),(1310,425,'Convolutional Neural Networks (CNNs) & Feature Map Visualizations','live',60,1,1),(1311,425,'Transfer Learning with Vision Models: ResNet, MobileNet & Real-Time Object Detection','live',60,2,0),(1312,425,'NLP Foundations: Tokenization, Word Embeddings (Word2Vec) & Sequence Modeling','live',60,3,0),(1313,425,'Transformer Architectures: Self-Attention, BERT & Hugging Face Ecosystem','live',60,4,0),(1314,426,'LLM Engineering: Prompt Design, Token Economics & Vector Databases (Chroma/Pinecone)','live',60,1,1),(1315,426,'Production Retrieval-Augmented Generation (RAG) & LangChain Agents','live',60,2,0),(1316,426,'Model Packaging, FastAPI Microservice Serving & Docker Containerization','live',60,3,0),(1317,426,'Capstone Project: Production-Ready Multi-Modal AI Assistant with Live Inference API','live',60,4,0),(1318,427,'What is AI vs ML vs Deep Learning','live',60,1,1),(1319,427,'Python & Jupyter Environment Setup','live',60,2,0),(1320,427,'NumPy Arrays & Mathematical Operations','live',60,3,0),(1321,427,'Pandas DataFrames & Real-world Data Cleaning','live',60,4,0),(1322,428,'Linear Regression & Cost Functions','live',60,1,1),(1323,428,'Logistic Regression & Binary Classification','live',60,2,0),(1324,428,'Decision Trees & Model Overfitting','live',60,3,0),(1325,428,'Model Evaluation (Precision, Recall, F1-Score)','live',60,4,0),(1326,429,'K-Means Clustering Fundamentals','live',60,1,1),(1327,429,'Customer Segmentation Analysis','live',60,2,0),(1328,429,'Dimensionality Reduction Basics','live',60,3,0),(1329,430,'Building a House Price Predictor','live',60,1,1),(1330,430,'Building a Loan Eligibility Classifier','live',60,2,0),(1331,430,'Deploying ML Models with Streamlit','live',60,3,0),(1332,431,'LLM Tokenization, Temperature & Top-P','live',60,1,1),(1333,431,'Zero-shot, Few-shot & Role-based Prompting','live',60,2,0),(1334,431,'Chain-of-Thought & Self-Consistency Prompting','live',60,3,0),(1335,431,'Handling Hallucinations & Fact Verification','live',60,4,0),(1336,432,'Writing Robust System Prompts','live',60,1,1),(1337,432,'Enforcing JSON & Markdown Output Schemas','live',60,2,0),(1338,432,'Prompt Injection & Security Guardrails','live',60,3,0),(1339,433,'Image Generation with Midjourney & DALL-E','live',60,1,1),(1340,433,'Audio, Voice & Video AI Generation Tools','live',60,2,0),(1341,433,'Multi-Modal Reasoning with Gemini & Claude','live',60,3,0),(1342,434,'Building Custom Specialized AI Assistants','live',60,1,1),(1343,434,'Creating a Reusable Personal Prompt Repository','live',60,2,0),(1344,434,'Showcase Presentation & Evaluation','live',60,3,0),(1345,435,'Variables, Data Types, Control Flow & Loops','live',60,1,1),(1346,435,'Lists, Dictionaries, Tuples & Sets','live',60,2,0),(1347,435,'Functions, Lambdas & List Comprehensions','live',60,3,0),(1348,435,'Object-Oriented Programming (OOP) in Python','live',60,4,0),(1349,436,'Ndarray Creation, Indexing & Slicing','live',60,1,1),(1350,436,'Vectorized Operations & Broadcasting','live',60,2,0),(1351,436,'Matrix Multiplication & Linear Algebra Functions','live',60,3,0),(1352,437,'Series & DataFrames Essentials','live',60,1,1),(1353,437,'Filtering, GroupBy & Pivot Tables','live',60,2,0),(1354,437,'Handling Missing Data, Imputation & Merging','live',60,3,0),(1355,438,'Matplotlib Line, Bar & Scatter Plots','live',60,1,1),(1356,438,'Seaborn Heatmaps & Statistical Distributions','live',60,2,0),(1357,438,'Hands-on EDA Project on a Real-world Dataset','live',60,3,0),(1358,439,'ReAct (Reason + Act) Agent Framework','live',60,1,1),(1359,439,'Memory, State Management & Scratchpads','live',60,2,0),(1360,439,'OpenAI & Claude Function Calling','live',60,3,0),(1361,440,'Connecting Agents to Google Search & Scraping','live',60,1,1),(1362,440,'Database Querying Agents (SQL Agent)','live',60,2,0),(1363,440,'Error Handling & Human-in-the-Loop Safeguards','live',60,3,0),(1364,441,'Make.com & Zapier Integration Patterns','live',60,1,1),(1365,441,'Webhooks & Automated Multi-Step Triggers','live',60,2,0),(1366,441,'Automated Lead Qualification & CRM Sync','live',60,3,0),(1367,442,'Building an Automated Market Research Agent','live',60,1,1),(1368,442,'Generating Formatted Reports & Sending Email Alerts','live',60,2,0),(1369,442,'Deploying the AI Agent Workflow','live',60,3,0),(1370,443,'ChatGPT vs Gemini vs Perplexity: When to Use Which','live',60,1,1),(1371,443,'Prompting Frameworks for Clear, Accurate Answers','live',60,2,0),(1372,443,'Synthesizing Long PDFs, Articles & Research Papers','live',60,3,0),(1373,444,'Drafting High-Impact Professional Emails & Reports','live',60,1,1),(1374,444,'Excel & Google Sheets Formulas with AI','live',60,2,0),(1375,444,'Instant Slide Decks & Presentation Generation','live',60,3,0),(1376,445,'Audio Transcription & Meeting Summarization','live',60,1,1),(1377,445,'Resume Optimization & Cover Letter Personalization','live',60,2,0),(1378,445,'Building Your Personal Daily Productivity System','live',60,3,0),(1379,446,'Scikit-Learn Pipeline & ColumnTransformer','live',60,1,1),(1380,446,'Feature Engineering & Cross-Validation Strategy','live',60,2,0),(1381,446,'Hyperparameter Tuning with GridSearchCV & Optuna','live',60,3,0),(1382,447,'Project 1: Real Estate Price Prediction with XGBoost','live',60,1,1),(1383,447,'Model Evaluation with RMSE & R-Squared','live',60,2,0),(1384,447,'Project 2: Credit Card Fraud & Risk Scoring System','live',60,3,0),(1385,447,'Handling Imbalanced Datasets with SMOTE','live',60,4,0),(1386,448,'Project 3: Medical Diagnostics Classification Model','live',60,1,1),(1387,448,'Project 4: Customer Sentiment & Review Classification','live',60,2,0),(1388,448,'TF-IDF Vectorization & Classification Metrics','live',60,3,0),(1389,449,'Project 5: Movie & Product Recommendation System','live',60,1,1),(1390,449,'Serializing Models with Joblib / ONNX','live',60,2,0),(1391,449,'Building a FastAPI Inference Endpoint & Deploying Live','live',60,3,0),(1392,450,'HTML/CSS — Live Practical Session','live',NULL,1,1),(1393,451,'JavaScript — Live Practical Session','live',NULL,1,0),(1394,452,'React — Live Practical Session','live',NULL,1,0),(1395,453,'Node.js/Express — Live Practical Session','live',NULL,1,0),(1396,454,'MongoDB — Live Practical Session','live',NULL,1,0),(1397,455,'JWT — Live Practical Session','live',NULL,1,0),(1398,456,'Git/GitHub — Live Practical Session','live',NULL,1,0),(1399,457,'deployment — Live Practical Session','live',NULL,1,0),(1400,458,'HTML5 — Live Practical Session','live',NULL,1,1),(1401,459,'CSS3 — Live Practical Session','live',NULL,1,0),(1402,460,'responsive design — Live Practical Session','live',NULL,1,0),(1403,461,'JavaScript — Live Practical Session','live',NULL,1,0),(1404,462,'DOM — Live Practical Session','live',NULL,1,0),(1405,463,'React — Live Practical Session','live',NULL,1,0),(1406,464,'hooks — Live Practical Session','live',NULL,1,0),(1407,465,'APIs — Live Practical Session','live',NULL,1,0),(1408,466,'Syntax — Live Practical Session','live',NULL,1,1),(1409,467,'control flow — Live Practical Session','live',NULL,1,0),(1410,468,'functions — Live Practical Session','live',NULL,1,0),(1411,469,'collections — Live Practical Session','live',NULL,1,0),(1412,470,'OOP — Live Practical Session','live',NULL,1,0),(1413,471,'files — Live Practical Session','live',NULL,1,0),(1414,472,'APIs — Live Practical Session','live',NULL,1,0),(1415,473,'automation — Live Practical Session','live',NULL,1,0),(1416,474,'SQL SELECT — Live Practical Session','live',NULL,1,1),(1417,475,'joins — Live Practical Session','live',NULL,1,0),(1418,476,'grouping — Live Practical Session','live',NULL,1,0),(1419,477,'schema design — Live Practical Session','live',NULL,1,0),(1420,478,'constraints — Live Practical Session','live',NULL,1,0),(1421,479,'indexes — Live Practical Session','live',NULL,1,0),(1422,480,'MySQL — Live Practical Session','live',NULL,1,0),(1423,481,'Core Java — Live Practical Session','live',NULL,1,1),(1424,482,'Big-O — Live Practical Session','live',NULL,1,0),(1425,483,'arrays — Live Practical Session','live',NULL,1,0),(1426,484,'strings — Live Practical Session','live',NULL,1,0),(1427,485,'recursion — Live Practical Session','live',NULL,1,0),(1428,486,'linked lists — Live Practical Session','live',NULL,1,0),(1429,487,'trees — Live Practical Session','live',NULL,1,0),(1430,488,'graphs — Live Practical Session','live',NULL,1,0),(1431,489,'Cloud basics — Live Practical Session','live',NULL,1,1),(1432,490,'IAM — Live Practical Session','live',NULL,1,0),(1433,491,'EC2 — Live Practical Session','live',NULL,1,0),(1434,492,'S3 — Live Practical Session','live',NULL,1,0),(1435,493,'VPC intro — Live Practical Session','live',NULL,1,0),(1436,494,'deployment — Live Practical Session','live',NULL,1,0),(1437,495,'monitoring — Live Practical Session','live',NULL,1,0),(1438,496,'cost safety — Live Practical Session','live',NULL,1,0),(1439,497,'Windows basics — Live Practical Session','live',NULL,1,1),(1440,498,'files — Live Practical Session','live',NULL,1,0),(1441,499,'email — Live Practical Session','live',NULL,1,0),(1442,500,'web — Live Practical Session','live',NULL,1,0),(1443,501,'spreadsheets — Live Practical Session','live',NULL,1,0),(1444,502,'presentation — Live Practical Session','live',NULL,1,0),(1445,503,'cyber hygiene — Live Practical Session','live',NULL,1,0),(1446,504,'HTML — Live Practical Session','live',NULL,1,1),(1447,505,'CSS — Live Practical Session','live',NULL,1,0),(1448,506,'flexbox — Live Practical Session','live',NULL,1,0),(1449,507,'responsive layouts — Live Practical Session','live',NULL,1,0),(1450,508,'forms — Live Practical Session','live',NULL,1,0),(1451,509,'deployment — Live Practical Session','live',NULL,1,0),(1452,510,'AI basics — Live Practical Session','live',NULL,1,1),(1453,511,'prompting — Live Practical Session','live',NULL,1,0),(1454,512,'verification — Live Practical Session','live',NULL,1,0),(1455,513,'documents — Live Practical Session','live',NULL,1,0),(1456,514,'presentations — Live Practical Session','live',NULL,1,0),(1457,515,'productivity — Live Practical Session','live',NULL,1,0),(1458,516,'Career mapping — Live Practical Session','live',NULL,1,1),(1459,517,'CV — Live Practical Session','live',NULL,1,0),(1460,518,'GitHub — Live Practical Session','live',NULL,1,0),(1461,519,'LinkedIn — Live Practical Session','live',NULL,1,0),(1462,520,'interview practice — Live Practical Session','live',NULL,1,0),(1463,521,'communication — Live Practical Session','live',NULL,1,0),(1464,522,'Automation logic — Live Practical Session','live',NULL,1,1),(1465,523,'webhooks — Live Practical Session','live',NULL,1,0),(1466,524,'APIs — Live Practical Session','live',NULL,1,0),(1467,525,'sheets — Live Practical Session','live',NULL,1,0),(1468,526,'AI steps — Live Practical Session','live',NULL,1,0),(1469,527,'errors — Live Practical Session','live',NULL,1,0),(1470,528,'privacy — Live Practical Session','live',NULL,1,0),(1471,529,'Excel — Live Practical Session','live',NULL,1,1),(1472,530,'SQL — Live Practical Session','live',NULL,1,0),(1473,531,'statistics — Live Practical Session','live',NULL,1,0),(1474,532,'Power Query — Live Practical Session','live',NULL,1,0),(1475,533,'Power BI — Live Practical Session','live',NULL,1,0),(1476,534,'DAX — Live Practical Session','live',NULL,1,0),(1477,535,'storytelling — Live Practical Session','live',NULL,1,0),(1478,536,'React patterns — Live Practical Session','live',NULL,1,1),(1479,537,'Next.js — Live Practical Session','live',NULL,1,0),(1480,538,'App Router — Live Practical Session','live',NULL,1,0),(1481,539,'data fetching — Live Practical Session','live',NULL,1,0),(1482,540,'auth — Live Practical Session','live',NULL,1,0),(1483,541,'testing — Live Practical Session','live',NULL,1,0),(1484,542,'deploy — Live Practical Session','live',NULL,1,0),(1485,543,'Node.js — Live Practical Session','live',NULL,1,1),(1486,544,'Express — Live Practical Session','live',NULL,1,0),(1487,545,'REST — Live Practical Session','live',NULL,1,0),(1488,546,'JWT — Live Practical Session','live',NULL,1,0),(1489,547,'PostgreSQL/MongoDB — Live Practical Session','live',NULL,1,0),(1490,548,'security — Live Practical Session','live',NULL,1,0),(1491,549,'testing — Live Practical Session','live',NULL,1,0),(1492,550,'React Native — Live Practical Session','live',NULL,1,1),(1493,551,'Expo — Live Practical Session','live',NULL,1,0),(1494,552,'navigation — Live Practical Session','live',NULL,1,0),(1495,553,'state — Live Practical Session','live',NULL,1,0),(1496,554,'APIs — Live Practical Session','live',NULL,1,0),(1497,555,'auth — Live Practical Session','live',NULL,1,0),(1498,556,'mobile UX — Live Practical Session','live',NULL,1,0),(1499,557,'builds — Live Practical Session','live',NULL,1,0),(1500,558,'LLMs — Live Practical Session','live',NULL,1,1),(1501,559,'APIs — Live Practical Session','live',NULL,1,0),(1502,560,'embeddings — Live Practical Session','live',NULL,1,0),(1503,561,'RAG — Live Practical Session','live',NULL,1,0),(1504,562,'tools — Live Practical Session','live',NULL,1,0),(1505,563,'evaluation — Live Practical Session','live',NULL,1,0),(1506,564,'privacy — Live Practical Session','live',NULL,1,0),(1507,565,'deployment — Live Practical Session','live',NULL,1,0),(1508,566,'Linux — Live Practical Session','live',NULL,1,1),(1509,567,'Git — Live Practical Session','live',NULL,1,0),(1510,568,'Docker — Live Practical Session','live',NULL,1,0),(1511,569,'Compose — Live Practical Session','live',NULL,1,0),(1512,570,'CI/CD — Live Practical Session','live',NULL,1,0),(1513,571,'GitHub Actions — Live Practical Session','live',NULL,1,0),(1514,572,'observability — Live Practical Session','live',NULL,1,0),(1515,573,'Research — Live Practical Session','live',NULL,1,1),(1516,574,'UX flows — Live Practical Session','live',NULL,1,0),(1517,575,'wireframes — Live Practical Session','live',NULL,1,0),(1518,576,'Figma — Live Practical Session','live',NULL,1,0),(1519,577,'design system — Live Practical Session','live',NULL,1,0),(1520,578,'prototype — Live Practical Session','live',NULL,1,0),(1521,579,'handoff — Live Practical Session','live',NULL,1,0),(1522,580,'QA basics — Live Practical Session','live',NULL,1,1),(1523,581,'test plans — Live Practical Session','live',NULL,1,0),(1524,582,'bug reports — Live Practical Session','live',NULL,1,0),(1525,583,'JS basics — Live Practical Session','live',NULL,1,0),(1526,584,'Playwright — Live Practical Session','live',NULL,1,0),(1527,585,'CI — Live Practical Session','live',NULL,1,0),(1528,586,'SEO — Live Practical Session','live',NULL,1,1),(1529,587,'keyword research — Live Practical Session','live',NULL,1,0),(1530,588,'content — Live Practical Session','live',NULL,1,0),(1531,589,'GA4 — Live Practical Session','live',NULL,1,0),(1532,590,'ads fundamentals — Live Practical Session','live',NULL,1,0),(1533,591,'attribution — Live Practical Session','live',NULL,1,0),(1534,592,'Java syntax — Live Practical Session','live',NULL,1,1),(1535,593,'flow control — Live Practical Session','live',NULL,1,0),(1536,594,'methods — Live Practical Session','live',NULL,1,0),(1537,595,'OOP — Live Practical Session','live',NULL,1,0),(1538,596,'arrays — Live Practical Session','live',NULL,1,0),(1539,597,'collections — Live Practical Session','live',NULL,1,0),(1540,598,'errors — Live Practical Session','live',NULL,1,0),(1541,599,'Pandas — Live Practical Session','live',NULL,1,1),(1542,600,'NumPy — Live Practical Session','live',NULL,1,0),(1543,601,'visualisation — Live Practical Session','live',NULL,1,0),(1544,602,'statistics — Live Practical Session','live',NULL,1,0),(1545,603,'Scikit-learn — Live Practical Session','live',NULL,1,0),(1546,604,'evaluation — Live Practical Session','live',NULL,1,0),(1547,605,'HLD/LLD — Live Practical Session','live',NULL,1,1),(1548,606,'APIs — Live Practical Session','live',NULL,1,0),(1549,607,'caching — Live Practical Session','live',NULL,1,0),(1550,608,'queues — Live Practical Session','live',NULL,1,0),(1551,609,'DB design — Live Practical Session','live',NULL,1,0),(1552,610,'scaling — Live Practical Session','live',NULL,1,0),(1553,611,'reliability — Live Practical Session','live',NULL,1,0),(1554,612,'Security foundations — Live Practical Session','live',NULL,1,1),(1555,613,'networking — Live Practical Session','live',NULL,1,0),(1556,614,'web risks — Live Practical Session','live',NULL,1,0),(1557,615,'threat models — Live Practical Session','live',NULL,1,0),(1558,616,'defensive labs — Live Practical Session','live',NULL,1,0),(1559,617,'Python for AI — Live Practical Session','live',NULL,1,1),(1560,618,'Math & Statistics — Live Practical Session','live',NULL,1,0),(1561,619,'Supervised ML — Live Practical Session','live',NULL,1,0),(1562,620,'Unsupervised Learning — Live Practical Session','live',NULL,1,0),(1563,621,'Deep Learning with PyTorch — Live Practical Session','live',NULL,1,0),(1564,622,'Computer Vision — Live Practical Session','live',NULL,1,0),(1565,623,'NLP & Transformers — Live Practical Session','live',NULL,1,0),(1566,624,'Generative AI & RAG — Live Practical Session','live',NULL,1,0),(1567,625,'MLOps & FastAPI Deployment — Live Practical Session','live',NULL,1,0),(1568,626,'Introduction to AI & Data Exploration — Live Practical Session','live',NULL,1,1),(1569,627,'Supervised Learning Algorithms — Live Practical Session','live',NULL,1,0),(1570,628,'Unsupervised Learning & Clustering — Live Practical Session','live',NULL,1,0),(1571,629,'Hands-on ML Projects & Deployment — Live Practical Session','live',NULL,1,0),(1572,630,'Core Prompt Engineering Techniques — Live Practical Session','live',NULL,1,1),(1573,631,'Advanced System Prompts & Structured Outputs — Live Practical Session','live',NULL,1,0),(1574,632,'Multi-Modal GenAI & Media Creation — Live Practical Session','live',NULL,1,0),(1575,633,'Capstone Project: Custom GPTs & Prompt Library — Live Practical Session','live',NULL,1,0),(1576,634,'Python Programming Essentials — Live Practical Session','live',NULL,1,1),(1577,635,'NumPy for Scientific & Vectorized Computing — Live Practical Session','live',NULL,1,0),(1578,636,'Data Wrangling with Pandas — Live Practical Session','live',NULL,1,0),(1579,637,'Data Visualization & Exploratory Data Analysis (EDA) — Live Practical Session','live',NULL,1,0),(1580,638,'Autonomous Agent Architecture — Live Practical Session','live',NULL,1,1),(1581,639,'Tool Calling & External Integrations — Live Practical Session','live',NULL,1,0),(1582,640,'No-Code & Low-Code Workflow Automation — Live Practical Session','live',NULL,1,0),(1583,641,'Capstone Project: Autonomous Research & Report Agent — Live Practical Session','live',NULL,1,0),(1584,642,'Mastering Everyday AI Assistants — Live Practical Session','live',NULL,1,1),(1585,643,'Workplace & Academic Efficiency — Live Practical Session','live',NULL,1,0),(1586,644,'Creative Tools & Practical AI Workflows — Live Practical Session','live',NULL,1,0),(1587,645,'Industrial ML Pipelines & Preprocessing — Live Practical Session','live',NULL,1,1),(1588,646,'Projects 1 & 2: Predictive & Risk Modeling — Live Practical Session','live',NULL,1,0),(1589,647,'Projects 3 & 4: Classification & Sentiment NLP — Live Practical Session','live',NULL,1,0),(1590,648,'Project 5 & Cloud API Deployment — Live Practical Session','live',NULL,1,0);
/*!40000 ALTER TABLE `course_lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_modules`
--

DROP TABLE IF EXISTS `course_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_modules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `title` varchar(180) NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_modules_course` (`course_id`),
  CONSTRAINT `fk_module_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=649 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_modules`
--

LOCK TABLES `course_modules` WRITE;
/*!40000 ALTER TABLE `course_modules` DISABLE KEYS */;
INSERT INTO `course_modules` VALUES (450,13,'HTML/CSS','Guided learning sessions and assignments',1),(451,13,'JavaScript','Guided learning sessions and assignments',2),(452,13,'React','Guided learning sessions and assignments',3),(453,13,'Node.js/Express','Guided learning sessions and assignments',4),(454,13,'MongoDB','Guided learning sessions and assignments',5),(455,13,'JWT','Guided learning sessions and assignments',6),(456,13,'Git/GitHub','Guided learning sessions and assignments',7),(457,13,'deployment','Guided learning sessions and assignments',8),(458,14,'HTML5','Guided learning sessions and assignments',1),(459,14,'CSS3','Guided learning sessions and assignments',2),(460,14,'responsive design','Guided learning sessions and assignments',3),(461,14,'JavaScript','Guided learning sessions and assignments',4),(462,14,'DOM','Guided learning sessions and assignments',5),(463,14,'React','Guided learning sessions and assignments',6),(464,14,'hooks','Guided learning sessions and assignments',7),(465,14,'APIs','Guided learning sessions and assignments',8),(466,46,'Syntax','Guided learning sessions and assignments',1),(467,46,'control flow','Guided learning sessions and assignments',2),(468,46,'functions','Guided learning sessions and assignments',3),(469,46,'collections','Guided learning sessions and assignments',4),(470,46,'OOP','Guided learning sessions and assignments',5),(471,46,'files','Guided learning sessions and assignments',6),(472,46,'APIs','Guided learning sessions and assignments',7),(473,46,'automation','Guided learning sessions and assignments',8),(474,47,'SQL SELECT','Guided learning sessions and assignments',1),(475,47,'joins','Guided learning sessions and assignments',2),(476,47,'grouping','Guided learning sessions and assignments',3),(477,47,'schema design','Guided learning sessions and assignments',4),(478,47,'constraints','Guided learning sessions and assignments',5),(479,47,'indexes','Guided learning sessions and assignments',6),(480,47,'MySQL','Guided learning sessions and assignments',7),(481,21,'Core Java','Guided learning sessions and assignments',1),(482,21,'Big-O','Guided learning sessions and assignments',2),(483,21,'arrays','Guided learning sessions and assignments',3),(484,21,'strings','Guided learning sessions and assignments',4),(485,21,'recursion','Guided learning sessions and assignments',5),(486,21,'linked lists','Guided learning sessions and assignments',6),(487,21,'trees','Guided learning sessions and assignments',7),(488,21,'graphs','Guided learning sessions and assignments',8),(489,48,'Cloud basics','Guided learning sessions and assignments',1),(490,48,'IAM','Guided learning sessions and assignments',2),(491,48,'EC2','Guided learning sessions and assignments',3),(492,48,'S3','Guided learning sessions and assignments',4),(493,48,'VPC intro','Guided learning sessions and assignments',5),(494,48,'deployment','Guided learning sessions and assignments',6),(495,48,'monitoring','Guided learning sessions and assignments',7),(496,48,'cost safety','Guided learning sessions and assignments',8),(497,49,'Windows basics','Guided learning sessions and assignments',1),(498,49,'files','Guided learning sessions and assignments',2),(499,49,'email','Guided learning sessions and assignments',3),(500,49,'web','Guided learning sessions and assignments',4),(501,49,'spreadsheets','Guided learning sessions and assignments',5),(502,49,'presentation','Guided learning sessions and assignments',6),(503,49,'cyber hygiene','Guided learning sessions and assignments',7),(504,50,'HTML','Guided learning sessions and assignments',1),(505,50,'CSS','Guided learning sessions and assignments',2),(506,50,'flexbox','Guided learning sessions and assignments',3),(507,50,'responsive layouts','Guided learning sessions and assignments',4),(508,50,'forms','Guided learning sessions and assignments',5),(509,50,'deployment','Guided learning sessions and assignments',6),(510,51,'AI basics','Guided learning sessions and assignments',1),(511,51,'prompting','Guided learning sessions and assignments',2),(512,51,'verification','Guided learning sessions and assignments',3),(513,51,'documents','Guided learning sessions and assignments',4),(514,51,'presentations','Guided learning sessions and assignments',5),(515,51,'productivity','Guided learning sessions and assignments',6),(516,52,'Career mapping','Guided learning sessions and assignments',1),(517,52,'CV','Guided learning sessions and assignments',2),(518,52,'GitHub','Guided learning sessions and assignments',3),(519,52,'LinkedIn','Guided learning sessions and assignments',4),(520,52,'interview practice','Guided learning sessions and assignments',5),(521,52,'communication','Guided learning sessions and assignments',6),(522,53,'Automation logic','Guided learning sessions and assignments',1),(523,53,'webhooks','Guided learning sessions and assignments',2),(524,53,'APIs','Guided learning sessions and assignments',3),(525,53,'sheets','Guided learning sessions and assignments',4),(526,53,'AI steps','Guided learning sessions and assignments',5),(527,53,'errors','Guided learning sessions and assignments',6),(528,53,'privacy','Guided learning sessions and assignments',7),(529,54,'Excel','Guided learning sessions and assignments',1),(530,54,'SQL','Guided learning sessions and assignments',2),(531,54,'statistics','Guided learning sessions and assignments',3),(532,54,'Power Query','Guided learning sessions and assignments',4),(533,54,'Power BI','Guided learning sessions and assignments',5),(534,54,'DAX','Guided learning sessions and assignments',6),(535,54,'storytelling','Guided learning sessions and assignments',7),(536,55,'React patterns','Guided learning sessions and assignments',1),(537,55,'Next.js','Guided learning sessions and assignments',2),(538,55,'App Router','Guided learning sessions and assignments',3),(539,55,'data fetching','Guided learning sessions and assignments',4),(540,55,'auth','Guided learning sessions and assignments',5),(541,55,'testing','Guided learning sessions and assignments',6),(542,55,'deploy','Guided learning sessions and assignments',7),(543,56,'Node.js','Guided learning sessions and assignments',1),(544,56,'Express','Guided learning sessions and assignments',2),(545,56,'REST','Guided learning sessions and assignments',3),(546,56,'JWT','Guided learning sessions and assignments',4),(547,56,'PostgreSQL/MongoDB','Guided learning sessions and assignments',5),(548,56,'security','Guided learning sessions and assignments',6),(549,56,'testing','Guided learning sessions and assignments',7),(550,57,'React Native','Guided learning sessions and assignments',1),(551,57,'Expo','Guided learning sessions and assignments',2),(552,57,'navigation','Guided learning sessions and assignments',3),(553,57,'state','Guided learning sessions and assignments',4),(554,57,'APIs','Guided learning sessions and assignments',5),(555,57,'auth','Guided learning sessions and assignments',6),(556,57,'mobile UX','Guided learning sessions and assignments',7),(557,57,'builds','Guided learning sessions and assignments',8),(558,58,'LLMs','Guided learning sessions and assignments',1),(559,58,'APIs','Guided learning sessions and assignments',2),(560,58,'embeddings','Guided learning sessions and assignments',3),(561,58,'RAG','Guided learning sessions and assignments',4),(562,58,'tools','Guided learning sessions and assignments',5),(563,58,'evaluation','Guided learning sessions and assignments',6),(564,58,'privacy','Guided learning sessions and assignments',7),(565,58,'deployment','Guided learning sessions and assignments',8),(566,59,'Linux','Guided learning sessions and assignments',1),(567,59,'Git','Guided learning sessions and assignments',2),(568,59,'Docker','Guided learning sessions and assignments',3),(569,59,'Compose','Guided learning sessions and assignments',4),(570,59,'CI/CD','Guided learning sessions and assignments',5),(571,59,'GitHub Actions','Guided learning sessions and assignments',6),(572,59,'observability','Guided learning sessions and assignments',7),(573,60,'Research','Guided learning sessions and assignments',1),(574,60,'UX flows','Guided learning sessions and assignments',2),(575,60,'wireframes','Guided learning sessions and assignments',3),(576,60,'Figma','Guided learning sessions and assignments',4),(577,60,'design system','Guided learning sessions and assignments',5),(578,60,'prototype','Guided learning sessions and assignments',6),(579,60,'handoff','Guided learning sessions and assignments',7),(580,61,'QA basics','Guided learning sessions and assignments',1),(581,61,'test plans','Guided learning sessions and assignments',2),(582,61,'bug reports','Guided learning sessions and assignments',3),(583,61,'JS basics','Guided learning sessions and assignments',4),(584,61,'Playwright','Guided learning sessions and assignments',5),(585,61,'CI','Guided learning sessions and assignments',6),(586,62,'SEO','Guided learning sessions and assignments',1),(587,62,'keyword research','Guided learning sessions and assignments',2),(588,62,'content','Guided learning sessions and assignments',3),(589,62,'GA4','Guided learning sessions and assignments',4),(590,62,'ads fundamentals','Guided learning sessions and assignments',5),(591,62,'attribution','Guided learning sessions and assignments',6),(592,63,'Java syntax','Guided learning sessions and assignments',1),(593,63,'flow control','Guided learning sessions and assignments',2),(594,63,'methods','Guided learning sessions and assignments',3),(595,63,'OOP','Guided learning sessions and assignments',4),(596,63,'arrays','Guided learning sessions and assignments',5),(597,63,'collections','Guided learning sessions and assignments',6),(598,63,'errors','Guided learning sessions and assignments',7),(599,64,'Pandas','Guided learning sessions and assignments',1),(600,64,'NumPy','Guided learning sessions and assignments',2),(601,64,'visualisation','Guided learning sessions and assignments',3),(602,64,'statistics','Guided learning sessions and assignments',4),(603,64,'Scikit-learn','Guided learning sessions and assignments',5),(604,64,'evaluation','Guided learning sessions and assignments',6),(605,65,'HLD/LLD','Guided learning sessions and assignments',1),(606,65,'APIs','Guided learning sessions and assignments',2),(607,65,'caching','Guided learning sessions and assignments',3),(608,65,'queues','Guided learning sessions and assignments',4),(609,65,'DB design','Guided learning sessions and assignments',5),(610,65,'scaling','Guided learning sessions and assignments',6),(611,65,'reliability','Guided learning sessions and assignments',7),(612,66,'Security foundations','Guided learning sessions and assignments',1),(613,66,'networking','Guided learning sessions and assignments',2),(614,66,'web risks','Guided learning sessions and assignments',3),(615,66,'threat models','Guided learning sessions and assignments',4),(616,66,'defensive labs','Guided learning sessions and assignments',5),(617,67,'Python for AI','Guided learning sessions and assignments',1),(618,67,'Math & Statistics','Guided learning sessions and assignments',2),(619,67,'Supervised ML','Guided learning sessions and assignments',3),(620,67,'Unsupervised Learning','Guided learning sessions and assignments',4),(621,67,'Deep Learning with PyTorch','Guided learning sessions and assignments',5),(622,67,'Computer Vision','Guided learning sessions and assignments',6),(623,67,'NLP & Transformers','Guided learning sessions and assignments',7),(624,67,'Generative AI & RAG','Guided learning sessions and assignments',8),(625,67,'MLOps & FastAPI Deployment','Guided learning sessions and assignments',9),(626,37,'Introduction to AI & Data Exploration','Guided learning sessions and assignments',1),(627,37,'Supervised Learning Algorithms','Guided learning sessions and assignments',2),(628,37,'Unsupervised Learning & Clustering','Guided learning sessions and assignments',3),(629,37,'Hands-on ML Projects & Deployment','Guided learning sessions and assignments',4),(630,38,'Core Prompt Engineering Techniques','Guided learning sessions and assignments',1),(631,38,'Advanced System Prompts & Structured Outputs','Guided learning sessions and assignments',2),(632,38,'Multi-Modal GenAI & Media Creation','Guided learning sessions and assignments',3),(633,38,'Capstone Project: Custom GPTs & Prompt Library','Guided learning sessions and assignments',4),(634,39,'Python Programming Essentials','Guided learning sessions and assignments',1),(635,39,'NumPy for Scientific & Vectorized Computing','Guided learning sessions and assignments',2),(636,39,'Data Wrangling with Pandas','Guided learning sessions and assignments',3),(637,39,'Data Visualization & Exploratory Data Analysis (EDA)','Guided learning sessions and assignments',4),(638,40,'Autonomous Agent Architecture','Guided learning sessions and assignments',1),(639,40,'Tool Calling & External Integrations','Guided learning sessions and assignments',2),(640,40,'No-Code & Low-Code Workflow Automation','Guided learning sessions and assignments',3),(641,40,'Capstone Project: Autonomous Research & Report Agent','Guided learning sessions and assignments',4),(642,41,'Mastering Everyday AI Assistants','Guided learning sessions and assignments',1),(643,41,'Workplace & Academic Efficiency','Guided learning sessions and assignments',2),(644,41,'Creative Tools & Practical AI Workflows','Guided learning sessions and assignments',3),(645,42,'Industrial ML Pipelines & Preprocessing','Guided learning sessions and assignments',1),(646,42,'Projects 1 & 2: Predictive & Risk Modeling','Guided learning sessions and assignments',2),(647,42,'Projects 3 & 4: Classification & Sentiment NLP','Guided learning sessions and assignments',3),(648,42,'Project 5 & Cloud API Deployment','Guided learning sessions and assignments',4);
/*!40000 ALTER TABLE `course_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_reviews`
--

DROP TABLE IF EXISTS `course_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reviews_course` (`course_id`),
  KEY `fk_review_user` (`user_id`),
  CONSTRAINT `fk_review_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_reviews`
--

LOCK TABLES `course_reviews` WRITE;
/*!40000 ALTER TABLE `course_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `instructor_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(180) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(60) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `brochure_file` varchar(255) DEFAULT NULL,
  `brochure_name` varchar(255) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `language` varchar(60) DEFAULT 'Hindi + English',
  `duration` varchar(60) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allow_installments` tinyint(1) NOT NULL DEFAULT 1,
  `original_price` decimal(10,2) DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `reviews_count` int(11) NOT NULL DEFAULT 0,
  `students_count` int(11) NOT NULL DEFAULT 0,
  `total_seats` int(11) NOT NULL DEFAULT 0,
  `seats_left` int(11) NOT NULL DEFAULT 0,
  `seats_filled` int(11) NOT NULL DEFAULT 0,
  `certificate` tinyint(1) NOT NULL DEFAULT 1,
  `schedule` varchar(120) DEFAULT NULL,
  `starts_at` date DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_bestseller` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_courses_slug` (`slug`),
  KEY `idx_courses_category` (`category_id`),
  KEY `idx_courses_status` (`status`),
  KEY `fk_course_instructor` (`instructor_id`),
  CONSTRAINT `fk_course_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_course_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (13,2,NULL,'Full Stack Web Development (MERN)','full-stack-web-development','Build and deploy full-stack apps with React, Node.js and MongoDB.','Learn responsive frontend development, modern JavaScript, React, REST APIs, Node.js, Express, MongoDB, authentication and deployment in a mentor-led, project-based cohort.','bi-layers','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,30,12,18,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 11:46:49'),(14,2,NULL,'Frontend Development with React','frontend-development-with-react','Master responsive websites, JavaScript and React through real projects.','Start with semantic HTML, CSS and responsive layouts, then learn JavaScript, reusable React components, hooks, forms and API integration. Build a portfolio-ready frontend application.','bi-window','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,30,15,15,1,NULL,NULL,1,0,'published','2026-10-16 01:43:24','2026-10-16 11:46:49'),(15,2,NULL,'Web Development Fundamentals','web-development-fundamentals','HTML · CSS · JavaScript · responsive design · Git basics','Understand website structure, semantic HTML, modern styling, responsive layouts, core JavaScript and beginner-friendly Git publishing.','bi-globe','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','6 Weeks',3999.00,1,5999.00,4.8,96,340,35,20,15,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(16,2,NULL,'React JS','react-js','JSX · props · state · events · conditional rendering · hooks · reusable components · API calls','Deep dive into React JS library. Master JSX, props, state, event handling, hooks, reusable UI components and dynamic REST API calls.','bi-boxes','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','6 Weeks',4999.00,1,7999.00,4.8,88,290,30,14,16,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(17,2,NULL,'Backend Development with Node.js','backend-development-with-nodejs','Node.js · Express · REST APIs · MongoDB · authentication · validation · deployment','Build robust backend architectures with Node.js, Express, MongoDB, secure JWT authentication, input validation, error handling and cloud deployment.','bi-server','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',6999.00,1,9999.00,4.8,104,380,30,16,14,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(18,1,NULL,'Python Programming','python-programming','Variables · data types · conditions · loops · functions · OOP · files · practical exercises','Build solid programming fundamentals in Python. Learn data types, conditionals, loops, functions, object-oriented programming, file I/O and practical exercises.','bi-filetype-py','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','6 Weeks',2999.00,1,4999.00,4.9,135,480,40,18,22,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(19,2,NULL,'Python with Django','python-with-django','Python · Django · models · views · templates · forms · authentication · database','Build full-fledged web applications with Python and Django. Master models, views, templates, forms, authentication and relational databases.','bi-filetype-py','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',4999.00,1,7999.00,4.8,76,260,30,15,15,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(20,1,NULL,'Core Java Programming','core-java-programming','Java syntax · input · operators · conditions · loops · arrays · strings · OOP','Start with Java syntax, variables, conditions, loops, methods, arrays, strings and OOP principles before building console applications.','bi-filetype-java','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','6 Weeks',3499.00,1,5999.00,4.8,82,310,35,19,16,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(21,6,NULL,'DSA & Problem Solving with Java','dsa-problem-solving-java','Strengthen Java logic, algorithms and interview problem-solving.','Practice Java fundamentals, time complexity, arrays, strings, recursion, linked lists, stacks, queues, trees, graphs and algorithmic problem-solving with guided assessments.','bi-cpu','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,35,11,24,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 11:46:49'),(22,3,NULL,'Data Analysis','data-analysis','Excel · SQL · Python · Pandas · data cleaning · visualization · dashboards · reporting','Turn raw data into business insights. Learn advanced Excel, SQL querying, Python with Pandas, data cleaning, interactive visualization and KPI reporting.','bi-bar-chart-line','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',7999.00,1,11999.00,4.8,118,420,30,13,17,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(23,3,NULL,'Data Science','data-science','Python · statistics · Pandas · visualization · machine learning fundamentals · model evaluation','Learn Python, statistics, Pandas, exploratory data analysis, introductory machine learning algorithms, Scikit-learn and model evaluation.','bi-diagram-3','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','12 Weeks',14999.00,1,21999.00,4.9,125,490,25,9,16,1,NULL,NULL,1,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(24,7,NULL,'UI/UX Design','ui-ux-design','User research · wireframes · Figma · prototypes · design systems · usability testing','Design intuitive mobile and web experiences in Figma. Learn design thinking, user flows, wireframes, component design systems, prototyping and usability testing.','bi-palette','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','6 Weeks',5999.00,1,8999.00,4.8,89,310,30,14,16,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(25,1,NULL,'Git & GitHub Essentials','git-github-essentials','Version control · branches · commits · pull requests · collaboration','Master Git version control from terminal basics to branch management, resolving merge conflicts, creating pull requests and collaborating on GitHub.','bi-git','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','3 Weeks',1499.00,1,2499.00,4.8,64,280,40,22,18,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(26,2,NULL,'REST API Development','rest-api-development','HTTP · API design · CRUD · authentication · Postman · error handling · API documentation','Design, build and test production REST APIs. Master HTTP methods, CRUD logic, token authentication, Postman automation, error handling and Swagger documentation.','bi-hdd-network','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','6 Weeks',3999.00,1,5999.00,4.8,78,290,30,16,14,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(27,4,NULL,'AI Tools for Coding','ai-tools-for-coding','AI-assisted coding · prompting basics · debugging · code review · responsible AI usage','Multiply your programming output with AI coding tools. Learn AI prompting for code generation, automated debugging, test creation, code review and responsible AI usage.','bi-stars','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','4 Weeks',1999.00,1,3499.00,4.8,95,380,35,15,20,1,NULL,NULL,1,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(28,6,NULL,'Resume & Interview Preparation','resume-interview-preparation','Resume building · GitHub portfolio · technical interviews · mock interview practice','Prepare standout resumes, optimize your GitHub portfolio, practice behavioral and technical interview questions, and participate in guided mock interviews.','bi-person-badge','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','4 Weeks',1999.00,1,3499.00,4.8,110,430,40,18,22,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(29,2,NULL,'Advanced JavaScript & TypeScript','advanced-javascript-typescript','ES6+ · Async JavaScript · TypeScript · Debugging · Mini Projects','Level up to modern JS and TypeScript. Master ES6+, event loops, asynchronous programming, TypeScript types, interfaces, generics and strict type-safe development.','bi-filetype-js','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',5999.00,1,8999.00,4.8,84,320,30,14,16,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(30,2,NULL,'Next.js & Modern React','nextjs-modern-react','Next.js · App Router · SSR · API Routes · SEO · Deployment','Build production web applications with Next.js App Router, Server-Side Rendering (SSR), Static Site Generation (SSG), API routes, SEO optimization and deployment.','bi-boxes','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',7999.00,1,11999.00,4.8,92,360,30,12,18,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(31,2,NULL,'MERN Stack Projects','mern-stack-projects','MongoDB · Express · React · Node.js · Authentication · Deployment','100% project-based full stack cohort. Build full-stack applications with MongoDB, Express, React, Node.js, user authentication, payment flows and live deployment.','bi-layers','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','10 Weeks',11999.00,1,16999.00,4.9,130,470,30,10,20,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(32,3,NULL,'SQL & Database Management','sql-database-management','SQL · Joins · Normalization · PostgreSQL · Queries · Database Design','Query, design and manage relational databases with SQL. Learn data modelling, filtering, joins, aggregations, normalization, indexes and PostgreSQL workflows.','bi-database','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','6 Weeks',3999.00,1,5999.00,4.8,105,390,35,17,18,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(33,3,NULL,'Excel for Data Analytics','excel-for-data-analytics','Excel Formulas · Pivot Tables · Data Cleaning · Charts · Dashboards','Master spreadsheet analysis for business. Learn formulas, VLOOKUP, XLOOKUP, Pivot Tables, conditional formatting, data cleaning, charts and KPI dashboards.','bi-file-earmark-spreadsheet','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','4 Weeks',2499.00,1,3999.00,4.8,88,340,40,20,20,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(34,3,NULL,'Power BI & Business Intelligence','power-bi-business-intelligence','Power Query · DAX · Data Modeling · Dashboards · Reports','Turn data into interactive executive reports. Master Power Query transformations, DAX expressions, relational data modeling and publish interactive Power BI dashboards.','bi-bar-chart-line','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','6 Weeks',5999.00,1,8999.00,4.8,96,370,30,13,17,1,NULL,NULL,0,0,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(35,3,NULL,'Machine Learning with Python','machine-learning-with-python','NumPy · Pandas · Scikit-learn · Regression · Classification · Model Evaluation','Train, evaluate and deploy machine learning models with Python. Learn NumPy, Pandas, Scikit-learn, regression, classification, clustering and model evaluation.','bi-diagram-3','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','10 Weeks',12999.00,1,18999.00,4.9,128,460,25,8,17,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(36,4,NULL,'Generative AI & Prompt Engineering','generative-ai-prompt-engineering','Engineering LLMs · Prompt Engineering · AI APIs · RAG Fundamentals · AI Projects','Master LLM development, prompt engineering patterns, OpenAI & Gemini APIs, vector databases, RAG architecture and deploy custom AI assistants.','bi-cpu-fill','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',6999.00,1,9999.00,4.9,140,510,30,9,21,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 01:43:24'),(37,4,NULL,'AI & Machine Learning Fundamentals','ai-machine-learning-fundamentals','Foundations of Artificial Intelligence, Core ML Algorithms & Hands-On Python.','Kickstart your AI & ML journey from scratch. Learn foundational machine learning concepts, data preprocessing with NumPy and Pandas, core regression and classification algorithms, clustering, and hands-on Python model training.','bi-cpu','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,35,16,19,1,NULL,NULL,1,0,'published','2026-10-16 01:43:24','2026-10-16 11:46:50'),(38,4,NULL,'Prompt Engineering & Generative AI','prompt-engineering-generative-ai','Mastering Advanced Prompting, ChatGPT, Claude, Midjourney & GenAI Workflows.','Master prompt engineering for top LLMs including ChatGPT, Claude, and Gemini. Learn role prompting, zero-shot, few-shot, chain-of-thought, system prompts, structured JSON outputs, image generation, and multi-modal generative AI.','bi-chat-left-dots-fill','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,40,14,26,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 11:46:50'),(39,1,NULL,'Python for AI & ML','python-for-ai-ml','Modern Python Programming, NumPy, Pandas, Matplotlib & Math for AI.','The definitive Python course designed specifically for aspiring Artificial Intelligence and Machine Learning developers. Master core Python, data structures, NumPy matrix math, Pandas data manipulation, and Matplotlib visualizations.','bi-filetype-py','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,35,17,18,1,NULL,NULL,0,1,'published','2026-10-16 01:43:24','2026-10-16 11:46:50'),(40,4,NULL,'AI Agents & Workflow Automation','ai-agents-workflow-automation','Autonomous AI Agents, LangChain, Tool Calling, Webhooks & No-Code Automations.','Build intelligent autonomous AI agents that can browse the web, execute tasks, call APIs, and automate business operations using LangChain, LangGraph, Zapier, Make.com, and OpenAI Function Calling.','bi-robot','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,30,12,18,1,NULL,NULL,1,0,'published','2026-10-16 01:43:24','2026-10-16 11:46:50'),(41,4,NULL,'ChatGPT, Gemini & AI Productivity Tools','chatgpt-gemini-ai-productivity-tools','10x Your Productivity in Work & Study with Modern Generative AI Tools.','Supercharge your daily efficiency. Learn how students, job seekers, and working professionals use ChatGPT, Google Gemini, Perplexity, and AI productivity tools to write, research, analyze spreadsheets, and draft presentations in minutes.','bi-lightning-charge-fill','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,50,18,32,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 11:46:50'),(42,3,NULL,'Machine Learning with Python Projects','machine-learning-python-projects','Build 5 Real-World Industry ML Projects from Scikit-Learn to Production APIs.','100% project-based Machine Learning training. Build, tune, and evaluate 5 real-world ML portfolio projects using Scikit-Learn, Pandas, and Python, and package them as REST APIs for production deployment.','bi-diagram-3-fill','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,30,11,19,1,NULL,NULL,1,1,'published','2026-10-16 01:43:24','2026-10-16 11:46:50'),(44,NULL,NULL,'Test Fullstack Pro','test-fullstack-pro',NULL,'Installment test course',NULL,'coding-fullstack.gif',NULL,NULL,'advanced','Hindi + English','12 weeks',12000.00,1,NULL,0.0,0,0,0,0,0,1,NULL,NULL,0,0,'published','2026-10-16 11:12:00','2026-10-16 11:14:35'),(45,NULL,NULL,'Test Disallowed Course','test-disallowed-course',NULL,'No installment test',NULL,'coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','2 weeks',8000.00,0,NULL,0.0,0,0,0,0,0,1,NULL,NULL,0,0,'published','2026-10-16 11:14:35','2026-10-16 11:14:35'),(46,1,1,'Python Programming & Automation','python-programming-automation','Learn Python from zero and automate everyday tasks.','Build solid programming fundamentals in Python, including data types, conditions, loops, functions, OOP, files, requests and practical automation scripts.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(47,1,1,'SQL & Database Fundamentals','sql-database-fundamentals','Query, design and manage relational databases with SQL.','Learn data modelling, SQL querying, filtering, joins, aggregates, constraints, normalisation, indexes and practical MySQL database workflows.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(48,5,1,'AWS Cloud & Deployment Fundamentals','aws-cloud-deployment-fundamentals','Understand cloud services and deploy your first application on AWS.','Learn cloud concepts, compute, storage, IAM basics, networking, deployment, monitoring and cloud cost awareness using hands-on AWS examples.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(49,1,1,'Digital Skills & Computer Fundamentals','digital-skills-computer-fundamentals','Get confident using computers, productivity tools and the internet.','Learn essential computer operations, file management, internet safety, documents, spreadsheets, presentations and online collaboration from the ground up.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(50,1,1,'Build Your First Website','build-your-first-website','Create and publish a responsive website using HTML and CSS.','Understand website structure, semantic HTML, forms, styling, layouts, responsive design and beginner-friendly publishing.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(51,4,1,'AI Tools for Study & Work','ai-tools-for-study-work','Use AI responsibly for research, writing, planning and productivity.','Learn prompt fundamentals, AI-assisted research, document workflows, spreadsheets, presentations and responsible verification of AI outputs.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(52,6,1,'Tech Career Launchpad','tech-career-launchpad','Prepare stronger projects, portfolios, resumes and interviews.','Build a targeted tech career plan with project selection, GitHub and LinkedIn improvements, CV reviews, communication practice and mock interviews. This is career guidance, not a job guarantee.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(53,4,1,'AI Automation with No-Code Tools','ai-automation-nocode-tools','Build practical AI automations without starting from complex code.','Design reliable workflows using triggers, APIs, spreadsheets, forms, webhooks and AI tools. Learn human review, error handling and privacy fundamentals.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(54,3,1,'Data Analytics with Excel, SQL & Power BI','data-analytics-excel-sql-powerbi','Turn raw data into insights and interactive business dashboards.','Practice data cleaning, advanced Excel, SQL analysis, introductory statistics and Power BI data modelling, DAX basics and dashboard storytelling with a portfolio case study.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(55,2,1,'React & Next.js Frontend Engineering','react-nextjs-frontend-engineering','Build performant frontend applications with React and Next.js.','Advance from React foundations to routing, Next.js App Router, rendering strategies, data fetching, authentication, Tailwind and frontend performance.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(56,2,1,'Backend Development with Node.js & APIs','backend-development-nodejs-apis','Build secure REST APIs, authentication and database-backed services.','Create modular services in Node.js and Express, implement REST APIs, authorization, validation, database design, testing and deployment.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:49','2026-10-16 11:46:49'),(57,2,1,'Mobile App Development with React Native','mobile-app-development-react-native','Create Android and iOS apps with React Native and Expo.','Learn mobile navigation, components, state, API integration, device features, authentication, notifications and app build workflows with Expo.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(58,4,1,'Generative AI & AI Agent Engineering','generative-ai-agent-engineering','Build grounded LLM applications, RAG systems and safe AI agents.','Learn LLM APIs, prompt design, embeddings, vector search, retrieval-augmented generation, tool calls, evaluation, guardrails and deployment using realistic applications.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(59,5,1,'Docker, DevOps & CI/CD Essentials','docker-devops-cicd-essentials','Containerise applications and automate reliable deployments.','Learn Git workflows, Docker and Compose, Linux basics, CI/CD pipelines, logs, secrets, cloud deployment and introductory orchestration concepts.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(60,7,1,'UI/UX Design with Figma','ui-ux-design-with-figma','Design intuitive mobile and web experiences in Figma.','Learn design thinking, research basics, user flows, wireframes, layout, type, colour accessibility, prototyping and handoff through a guided design case study.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(61,2,1,'Software Testing & QA with Playwright','software-testing-qa-playwright','Learn manual test design and browser automation with Playwright.','Learn testing fundamentals, test cases, bug reporting, browser testing, selectors, Playwright basics and CI integration through a realistic testing assignment.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(62,7,1,'Digital Marketing, SEO & Analytics','digital-marketing-seo-analytics','Plan digital campaigns and measure results using SEO and analytics.','Learn search intent, on-page SEO, technical basics, content planning, Google Analytics concepts, campaign structure and measurement ethics with a practical audit.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(63,1,1,'Core Java & Object-Oriented Programming','core-java-object-oriented-programming','Build strong Java fundamentals through practical exercises.','Start with Java syntax, variables, conditions, loops, methods, arrays, OOP, exceptions and collections before building a simple console application.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(64,3,1,'Data Science & Machine Learning Foundations','data-science-machine-learning-foundations','Train, evaluate and explain introductory machine-learning models.','Use NumPy, Pandas, statistics, data preparation, visualisation and Scikit-learn to explore datasets and build defensible ML models.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(65,6,1,'System Design & Microservices Foundations','system-design-microservices-foundations','Learn to reason about scalable backend architecture.','Practice API contracts, caching, messaging, databases, consistency, load balancing, reliability and practical system-design trade-offs with guided design reviews.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'advanced','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(66,5,1,'Cybersecurity & Ethical Security Foundations','cybersecurity-ethical-security-foundations','Understand cyber hygiene, networks and safe security assessment.','Learn foundational security principles, networking, common web risks, authentication, basic threat modelling and defensive controls using authorised training labs only.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'beginner','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50'),(67,4,1,'Artificial Intelligence & Machine Learning (AI & ML) Masterclass','artificial-intelligence-machine-learning','Master Python, Math, Supervised/Unsupervised ML, Deep Learning, CV, NLP, Generative AI & MLOps.','Master end-to-end Artificial Intelligence and Machine Learning engineering. Build strong foundations in Python and mathematics, train supervised and unsupervised ML algorithms, architect deep neural networks with PyTorch, build Computer Vision & NLP applications with Transformers, implement Retrieval-Augmented Generation (RAG) agents, and deploy production ML systems with FastAPI and Docker.','bi-code-slash','coding-fullstack.gif',NULL,NULL,'intermediate','Hindi + English','8 Weeks',3999.00,1,6999.00,4.8,120,450,24,8,0,1,'7:00 PM - 8:30 PM','2026-11-01',0,0,'published','2026-10-16 11:46:50','2026-10-16 11:46:50');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_otps`
--

DROP TABLE IF EXISTS `email_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_otps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `purpose` enum('login','reset') NOT NULL DEFAULT 'login',
  `attempts` tinyint(4) NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_otp_email_purpose` (`email`,`purpose`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_otps`
--

LOCK TABLES `email_otps` WRITE;
/*!40000 ALTER TABLE `email_otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enrollments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `progress` tinyint(4) NOT NULL DEFAULT 0,
  `enrolled_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_enroll_user_course` (`user_id`,`course_id`),
  KEY `idx_enroll_course` (`course_id`),
  KEY `fk_enroll_order` (`order_id`),
  CONSTRAINT `fk_enroll_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enroll_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_enroll_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (2,7,13,90,'cancelled',0,'2026-10-16 10:47:26',NULL);
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_registrations`
--

DROP TABLE IF EXISTS `event_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_registrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` enum('registered','attended','cancelled') NOT NULL DEFAULT 'registered',
  `registered_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_event_user` (`event_id`,`user_id`),
  KEY `fk_reg_user` (`user_id`),
  CONSTRAINT `fk_reg_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reg_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_registrations`
--

LOCK TABLES `event_registrations` WRITE;
/*!40000 ALTER TABLE `event_registrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `instructor_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(180) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('frontend','backend','python','dsa','career','general') NOT NULL DEFAULT 'general',
  `type` enum('free','paid') NOT NULL DEFAULT 'free',
  `status` enum('upcoming','ongoing','past') NOT NULL DEFAULT 'upcoming',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `mode` enum('online','offline') NOT NULL DEFAULT 'online',
  `duration` varchar(80) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `location` varchar(190) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `total_seats` int(11) NOT NULL DEFAULT 16,
  `seats_filled` int(11) NOT NULL DEFAULT 0,
  `recording_url` varchar(255) DEFAULT NULL,
  `is_featured_home` tinyint(1) NOT NULL DEFAULT 0,
  `home_sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_events_slug` (`slug`),
  KEY `idx_events_status` (`status`),
  KEY `idx_events_category` (`category`),
  KEY `fk_event_instructor` (`instructor_id`),
  CONSTRAINT `fk_event_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,1,'Frontend Development Bootcamp',NULL,'frontend-development-bootcamp','Learn HTML, CSS and JavaScript through practical projects and expert mentorship.','frontend','free','ongoing',0.00,'2026-06-15','19:00:00','online',NULL,'beginner',NULL,NULL,16,12,NULL,0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(2,1,'React Masterclass',NULL,'react-masterclass','Build modern applications using React and industry best practices.','frontend','paid','ongoing',499.00,'2026-06-18','20:00:00','online',NULL,'beginner',NULL,NULL,16,10,NULL,0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(3,1,'Python Fundamentals Workshop',NULL,'python-fundamentals-workshop','Learn Python from basics to automation through practical examples.','python','free','upcoming',0.00,'2026-06-22','18:00:00','online',NULL,'beginner',NULL,NULL,16,6,NULL,0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(4,1,'Node.js & Express Backend Bootcamp',NULL,'nodejs-express-backend-bootcamp','Build RESTful APIs, connect databases and handle auth with Node.js.','backend','paid','upcoming',699.00,'2026-06-25','19:30:00','online',NULL,'beginner',NULL,NULL,16,4,NULL,0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(5,1,'DSA Crash Course for Interviews',NULL,'dsa-crash-course-interviews','Arrays, strings, sorting and searching — everything you need to crack coding rounds.','dsa','free','upcoming',0.00,'2026-06-28','09:00:00','online',NULL,'beginner',NULL,NULL,16,2,NULL,0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(6,1,'Resume & LinkedIn Masterclass',NULL,'resume-linkedin-masterclass','Build a resume that gets noticed and a LinkedIn profile that attracts recruiters.','career','paid','upcoming',299.00,'2026-07-01','18:30:00','online',NULL,'beginner',NULL,NULL,16,8,NULL,0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(7,1,'HTML & CSS: Zero to Hero',NULL,'html-css-zero-to-hero','A 3-hour live session that took absolute beginners from nothing to a working webpage.','frontend','free','past',0.00,'2026-06-05','19:00:00','online',NULL,'beginner',NULL,NULL,48,48,'https://youtu.be/demo-recording',0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(8,1,'Python for Automation Bootcamp',NULL,'python-for-automation-bootcamp','Automate boring tasks using Python — files, emails, web scraping and more.','python','paid','past',599.00,'2026-06-01','18:00:00','online',NULL,'beginner',NULL,NULL,32,32,'https://youtu.be/demo-recording',0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(9,1,'MySQL Database Design',NULL,'mysql-database-design','Schema design, normalization, joins and queries — everything for backend devs.','backend','paid','past',499.00,'2026-05-20','18:30:00','online',NULL,'beginner',NULL,NULL,27,27,'https://youtu.be/demo-recording',0,0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(10,NULL,'Python Starter Workshop | Advanced Live Coding & Web Scraping','Advanced Python, Web Scraping & Automation','python-starter-workshop','Join the 3-day Python Workshop from TCM One for ₹99. Master advanced Python logic, Web Scraping with BeautifulSoup, REST APIs, and automated data extraction.','python','paid','upcoming',99.00,'2026-10-18','19:00:00','online','3 Days','intermediate',NULL,NULL,50,14,NULL,1,1,'2026-10-13 15:15:38','2026-10-16 01:43:24'),(11,NULL,'7-Day Frontend Website Challenge | TCM One','Build Your First Responsive Website','frontend-website-challenge-7-days','Join the 7-Day Frontend Website Challenge from TCM One for ₹999. Build a responsive website with HTML, CSS, JavaScript and mentor guidance.','frontend','paid','upcoming',999.00,'2026-10-18','19:00:00','online','7 Days','beginner',NULL,NULL,40,8,NULL,1,2,'2026-10-13 15:15:38','2026-10-16 01:43:24'),(12,NULL,'AI Tools for Everyday Work | TCM One Workshop','Work Faster and Smarter with Modern AI','ai-tools-everyday-work-workshop','Learn practical AI tools for everyday study and work with TCM One. Beginner-friendly workshop covering prompting, research and productivity workflows.','general','paid','upcoming',199.00,'2026-10-18','19:00:00','online','2 Days','beginner',NULL,NULL,50,12,NULL,0,3,'2026-10-13 15:15:38','2026-10-16 01:43:24'),(13,NULL,'Find Your Tech Career Path | TCM One Workshop','Roadmap to Software Engineering & Placements','find-your-tech-career-path-workshop','A beginner-friendly TCM One workshop to help students compare tech career paths and choose the right learning roadmap.','career','free','upcoming',0.00,'2026-10-18','19:00:00','online','1 Day','beginner',NULL,NULL,100,35,NULL,0,4,'2026-10-13 15:15:38','2026-10-16 01:43:24');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fcm_tokens`
--

DROP TABLE IF EXISTS `fcm_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fcm_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token` varchar(512) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_fcm_token` (`token`),
  KEY `idx_fcm_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fcm_tokens`
--

LOCK TABLES `fcm_tokens` WRITE;
/*!40000 ALTER TABLE `fcm_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `fcm_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `help_requests`
--

DROP TABLE IF EXISTS `help_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` bigint(20) unsigned NOT NULL,
  `to_user_id` bigint(20) unsigned NOT NULL,
  `message` varchar(500) DEFAULT NULL,
  `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_hr_to` (`to_user_id`),
  KEY `idx_hr_from` (`from_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `help_requests`
--

LOCK TABLES `help_requests` WRITE;
/*!40000 ALTER TABLE `help_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `help_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installment_plans`
--

DROP TABLE IF EXISTS `installment_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installment_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `enrollment_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `parent_order_id` bigint(20) unsigned NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `upfront_amount` decimal(10,2) NOT NULL,
  `total_installments` int(10) unsigned NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(8) NOT NULL DEFAULT 'INR',
  `status` enum('active','completed','defaulted','cancelled') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ip_user` (`user_id`),
  KEY `idx_ip_course` (`course_id`),
  KEY `idx_ip_order` (`parent_order_id`),
  KEY `idx_ip_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installment_plans`
--

LOCK TABLES `installment_plans` WRITE;
/*!40000 ALTER TABLE `installment_plans` DISABLE KEYS */;
INSERT INTO `installment_plans` VALUES (1,NULL,8,35,19,12999.00,6499.50,3,0.00,12999.00,'INR','active','2026-10-16 11:07:32','2026-10-16 11:07:32'),(2,NULL,7,13,25,10000.00,5000.00,2,0.00,10000.00,'INR','active','2026-10-16 11:09:43','2026-10-16 11:09:43'),(3,NULL,7,13,30,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:10:28','2026-10-16 11:10:28'),(4,NULL,9,44,33,12000.00,6000.00,3,0.00,12000.00,'INR','active','2026-10-16 11:14:35','2026-10-16 11:14:35'),(5,NULL,9,44,34,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:14:35','2026-10-16 11:14:35'),(6,NULL,9,44,35,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:14:35','2026-10-16 11:14:35'),(7,NULL,9,44,36,12000.00,4800.00,3,0.00,12000.00,'INR','active','2026-10-16 11:15:58','2026-10-16 11:15:58'),(8,NULL,9,44,37,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:15:58','2026-10-16 11:15:58'),(9,NULL,9,44,38,10000.00,5000.00,2,10000.00,0.00,'INR','completed','2026-10-16 11:15:58','2026-10-16 11:15:58'),(10,NULL,9,44,40,12000.00,4800.00,3,0.00,12000.00,'INR','active','2026-10-16 11:17:15','2026-10-16 11:17:15'),(11,NULL,9,44,41,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:17:15','2026-10-16 11:17:15'),(12,NULL,9,44,42,10000.00,5000.00,2,10000.00,0.00,'INR','completed','2026-10-16 11:17:15','2026-10-16 11:17:15'),(13,NULL,7,13,48,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:17:53','2026-10-16 11:17:53'),(14,NULL,9,44,51,12000.00,4800.00,3,0.00,12000.00,'INR','active','2026-10-16 11:29:41','2026-10-16 11:29:41'),(15,NULL,9,44,52,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:29:41','2026-10-16 11:29:41'),(16,NULL,9,44,53,10000.00,5000.00,2,10000.00,0.00,'INR','completed','2026-10-16 11:29:41','2026-10-16 11:29:41'),(17,NULL,7,13,59,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:29:57','2026-10-16 11:29:57'),(18,NULL,9,44,62,12000.00,4800.00,3,0.00,12000.00,'INR','active','2026-10-16 11:40:14','2026-10-16 11:40:14'),(19,NULL,9,44,63,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:40:14','2026-10-16 11:40:14'),(20,NULL,9,44,64,10000.00,5000.00,2,10000.00,0.00,'INR','completed','2026-10-16 11:40:14','2026-10-16 11:40:14'),(21,NULL,7,13,70,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:40:32','2026-10-16 11:40:32'),(22,NULL,3,35,73,12999.00,6499.50,2,0.00,12999.00,'INR','active','2026-10-16 11:42:03','2026-10-16 11:42:03'),(23,NULL,9,44,75,12000.00,4800.00,3,0.00,12000.00,'INR','active','2026-10-16 11:56:04','2026-10-16 11:56:04'),(24,NULL,9,44,76,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:56:04','2026-10-16 11:56:04'),(25,NULL,9,44,77,10000.00,5000.00,2,10000.00,0.00,'INR','completed','2026-10-16 11:56:04','2026-10-16 11:56:04'),(26,NULL,7,13,83,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:56:23','2026-10-16 11:56:23'),(27,NULL,9,44,86,12000.00,4800.00,3,0.00,12000.00,'INR','active','2026-10-16 11:59:54','2026-10-16 11:59:54'),(28,NULL,9,44,87,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 11:59:54','2026-10-16 11:59:54'),(29,NULL,9,44,88,10000.00,5000.00,2,10000.00,0.00,'INR','completed','2026-10-16 11:59:54','2026-10-16 11:59:54'),(30,NULL,7,13,94,10000.00,5000.00,2,5000.00,5000.00,'INR','active','2026-10-16 12:00:01','2026-10-16 12:00:01');
/*!40000 ALTER TABLE `installment_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installment_reminders_log`
--

DROP TABLE IF EXISTS `installment_reminders_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installment_reminders_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `channel` varchar(30) NOT NULL DEFAULT 'email',
  `stage` varchar(30) NOT NULL,
  `recipient` varchar(150) NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('sent','failed') NOT NULL DEFAULT 'sent',
  `error_message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_schedule_channel_stage` (`schedule_id`,`channel`,`stage`),
  KEY `idx_irl_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installment_reminders_log`
--

LOCK TABLES `installment_reminders_log` WRITE;
/*!40000 ALTER TABLE `installment_reminders_log` DISABLE KEYS */;
INSERT INTO `installment_reminders_log` VALUES (1,8,7,'email','due_date','test_student_payu@thecodemunk.in','2026-10-16 11:17:17','sent',NULL),(2,5,8,'email','due_date','thecodemunk@gmail.com','2026-10-16 11:17:19','sent',NULL),(3,28,9,'email','due_date','test_inst_student@thecodemunk.in','2026-10-16 11:17:21','sent',NULL),(4,19,9,'email','due_date','test_inst_student@thecodemunk.in','2026-10-16 11:17:23','sent',NULL),(5,12,9,'email','due_date','test_inst_student@thecodemunk.in','2026-10-16 11:17:25','sent',NULL),(6,36,9,'email','3_days_before','test_inst_student@thecodemunk.in','2026-10-16 11:17:27','sent',NULL),(7,27,9,'email','3_days_before','test_inst_student@thecodemunk.in','2026-10-16 11:17:29','sent',NULL),(8,39,9,'email','due_date','test_inst_student@thecodemunk.in','2026-10-16 11:29:43','sent',NULL),(9,47,9,'email','3_days_before','test_inst_student@thecodemunk.in','2026-10-16 11:29:45','sent',NULL),(10,50,9,'email','due_date','test_inst_student@thecodemunk.in','2026-10-16 11:40:16','sent',NULL),(11,58,9,'email','3_days_before','test_inst_student@thecodemunk.in','2026-10-16 11:40:18','sent',NULL),(12,63,9,'email','due_date','test_inst_student@thecodemunk.in','2026-10-16 11:56:07','sent',NULL),(13,61,3,'email','due_date','cuboidsoft@gmail.com','2026-10-16 11:56:09','sent',NULL),(14,71,9,'email','3_days_before','test_inst_student@thecodemunk.in','2026-10-16 11:56:11','sent',NULL),(15,74,9,'email','due_date','test_inst_student@thecodemunk.in','2026-10-16 11:59:56','sent',NULL),(16,82,9,'email','3_days_before','test_inst_student@thecodemunk.in','2026-10-16 11:59:58','sent',NULL);
/*!40000 ALTER TABLE `installment_reminders_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `installment_schedules`
--

DROP TABLE IF EXISTS `installment_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `installment_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_order_id` bigint(20) unsigned DEFAULT NULL,
  `installment_plan_id` bigint(20) unsigned DEFAULT NULL,
  `installment_order_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `installment_number` int(10) unsigned NOT NULL,
  `total_installments` int(10) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('pending','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  `payment_ref` varchar(100) DEFAULT NULL,
  `txnid` varchar(100) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `reminded_at` datetime DEFAULT NULL,
  `reminder_stage` tinyint(4) NOT NULL DEFAULT 0,
  `reminder_sent_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_is_user` (`user_id`),
  KEY `idx_is_course` (`course_id`),
  KEY `idx_is_parent` (`parent_order_id`),
  KEY `idx_is_order` (`installment_order_id`),
  KEY `idx_is_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `installment_schedules`
--

LOCK TABLES `installment_schedules` WRITE;
/*!40000 ALTER TABLE `installment_schedules` DISABLE KEYS */;
INSERT INTO `installment_schedules` VALUES (3,16,NULL,NULL,7,13,1,2,5000.00,'2026-10-16','paid','2026-10-16 10:47:29','MIH_INST_001','TCM_INST_1792127849_M1','Installment 1 of 2',NULL,0,NULL,'2026-10-16 10:47:29','2026-10-16 10:47:29'),(4,16,NULL,NULL,7,13,2,2,5000.00,'2026-11-15','pending',NULL,NULL,NULL,'Installment 2 of 2',NULL,0,NULL,'2026-10-16 10:47:29','2026-10-16 10:47:29'),(5,19,1,20,8,35,1,3,6499.50,'2026-10-16','pending',NULL,NULL,'TCM_INST_20261016110732_EF8C37','Upfront Payment (Milestone 1 of 3)','2026-10-16 11:17:19',3,'2026-10-16 11:17:19','2026-10-16 11:07:32','2026-10-16 11:17:19'),(6,19,1,NULL,8,35,2,3,3249.75,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 3 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:07:32','2026-10-16 11:07:32'),(7,19,1,NULL,8,35,3,3,3249.75,'2026-11-15','pending',NULL,NULL,NULL,'Milestone 3 of 3 (Due in 30 days)',NULL,0,NULL,'2026-10-16 11:07:32','2026-10-16 11:07:32'),(8,25,2,NULL,7,13,1,2,5000.00,'2026-10-16','pending',NULL,NULL,NULL,'Upfront Payment (Milestone 1 of 2)','2026-10-16 11:17:17',3,'2026-10-16 11:17:17','2026-10-16 11:09:43','2026-10-16 11:17:17'),(9,25,2,NULL,7,13,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:09:43','2026-10-16 11:09:43'),(10,30,3,NULL,7,13,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:10:28','MIH_INST_001','TCM_INST_1792129228_M1','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:10:28','2026-10-16 11:10:28'),(11,30,3,NULL,7,13,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:10:28','2026-10-16 11:10:28'),(12,33,4,NULL,9,44,1,3,6000.00,'2026-10-16','pending',NULL,NULL,NULL,'Upfront Payment (Milestone 1 of 3)','2026-10-16 11:17:25',3,'2026-10-16 11:17:25','2026-10-16 11:14:35','2026-10-16 11:17:25'),(13,33,4,NULL,9,44,2,3,3000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 3 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(14,33,4,NULL,9,44,3,3,3000.00,'2026-11-15','pending',NULL,NULL,NULL,'Milestone 3 of 3 (Due in 30 days)',NULL,0,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(15,34,5,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:14:35','MIH_M1_1792129475','TXN_M1_1792129475','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(16,34,5,NULL,9,44,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(17,35,6,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:14:35','MIH_A_1792129475','TXN_A_1792129475','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(18,35,6,NULL,9,44,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(19,36,7,NULL,9,44,1,3,4800.00,'2026-10-16','pending',NULL,NULL,NULL,'Upfront Payment (Milestone 1 of 3)','2026-10-16 11:17:23',3,'2026-10-16 11:17:23','2026-10-16 11:15:58','2026-10-16 11:17:23'),(20,36,7,NULL,9,44,2,3,3600.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 3 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(21,36,7,NULL,9,44,3,3,3600.00,'2026-11-15','pending',NULL,NULL,NULL,'Milestone 3 of 3 (Due in 30 days)',NULL,0,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(22,37,8,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:15:58','MIH_M1_1792129558','TXN_M1_1792129558','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(23,37,8,NULL,9,44,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(24,38,9,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:15:58','MIH_A_1792129558','TXN_A_1792129558','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(25,38,9,39,9,44,2,2,5000.00,'2026-10-31','paid','2026-10-16 11:15:58','MIH_B_1792129558','TXN_B_1792129558','Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(26,1,NULL,NULL,9,44,2,3,3000.00,'2026-11-10','pending',NULL,NULL,NULL,'Test milestone | Rescheduled from 2026-10-26 to 2026-11-10: Student requested exam extension',NULL,0,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(27,1,NULL,NULL,9,44,2,2,5000.00,'2026-10-19','pending',NULL,NULL,NULL,'Reminder test milestone','2026-10-16 11:17:29',1,'2026-10-16 11:17:29','2026-10-16 11:15:58','2026-10-16 11:17:29'),(28,40,10,NULL,9,44,1,3,4800.00,'2026-10-16','pending',NULL,NULL,NULL,'Upfront Payment (Milestone 1 of 3)','2026-10-16 11:17:21',3,'2026-10-16 11:17:21','2026-10-16 11:17:15','2026-10-16 11:17:21'),(29,40,10,NULL,9,44,2,3,3600.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 3 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(30,40,10,NULL,9,44,3,3,3600.00,'2026-11-15','pending',NULL,NULL,NULL,'Milestone 3 of 3 (Due in 30 days)',NULL,0,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(31,41,11,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:17:15','MIH_M1_1792129635','TXN_M1_1792129635','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(32,41,11,NULL,9,44,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(33,42,12,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:17:15','MIH_A_1792129635','TXN_A_1792129635','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(34,42,12,43,9,44,2,2,5000.00,'2026-10-31','paid','2026-10-16 11:17:15','MIH_B_1792129635','TXN_B_1792129635','Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(35,1,NULL,NULL,9,44,2,3,3000.00,'2026-11-10','pending',NULL,NULL,NULL,'Test milestone | Rescheduled from 2026-10-26 to 2026-11-10: Student requested exam extension',NULL,0,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(36,1,NULL,NULL,9,44,2,2,5000.00,'2026-10-19','pending',NULL,NULL,NULL,'Reminder test milestone','2026-10-16 11:17:27',1,'2026-10-16 11:17:27','2026-10-16 11:17:15','2026-10-16 11:17:27'),(37,48,13,NULL,7,13,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:17:53','MIH_INST_001','TCM_INST_1792129673_M1','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:17:53','2026-10-16 11:17:53'),(38,48,13,NULL,7,13,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:17:53','2026-10-16 11:17:53'),(39,51,14,NULL,9,44,1,3,4800.00,'2026-10-16','pending',NULL,NULL,NULL,'Upfront Payment (Milestone 1 of 3)','2026-10-16 11:29:43',3,'2026-10-16 11:29:43','2026-10-16 11:29:41','2026-10-16 11:29:43'),(40,51,14,NULL,9,44,2,3,3600.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 3 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(41,51,14,NULL,9,44,3,3,3600.00,'2026-11-15','pending',NULL,NULL,NULL,'Milestone 3 of 3 (Due in 30 days)',NULL,0,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(42,52,15,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:29:41','MIH_M1_1792130381','TXN_M1_1792130381','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(43,52,15,NULL,9,44,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(44,53,16,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:29:41','MIH_A_1792130381','TXN_A_1792130381','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(45,53,16,54,9,44,2,2,5000.00,'2026-10-31','paid','2026-10-16 11:29:41','MIH_B_1792130381','TXN_B_1792130381','Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(46,1,NULL,NULL,9,44,2,3,3000.00,'2026-11-10','pending',NULL,NULL,NULL,'Test milestone | Rescheduled from 2026-10-26 to 2026-11-10: Student requested exam extension',NULL,0,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(47,1,NULL,NULL,9,44,2,2,5000.00,'2026-10-19','pending',NULL,NULL,NULL,'Reminder test milestone','2026-10-16 11:29:45',1,'2026-10-16 11:29:45','2026-10-16 11:29:41','2026-10-16 11:29:45'),(48,59,17,NULL,7,13,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:29:57','MIH_INST_001','TCM_INST_1792130397_M1','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:29:57','2026-10-16 11:29:57'),(49,59,17,NULL,7,13,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:29:57','2026-10-16 11:29:57'),(50,62,18,NULL,9,44,1,3,4800.00,'2026-10-16','pending',NULL,NULL,NULL,'Upfront Payment (Milestone 1 of 3)','2026-10-16 11:40:16',3,'2026-10-16 11:40:16','2026-10-16 11:40:14','2026-10-16 11:40:16'),(51,62,18,NULL,9,44,2,3,3600.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 3 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(52,62,18,NULL,9,44,3,3,3600.00,'2026-11-15','pending',NULL,NULL,NULL,'Milestone 3 of 3 (Due in 30 days)',NULL,0,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(53,63,19,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:40:14','MIH_M1_1792131014','TXN_M1_1792131014','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(54,63,19,NULL,9,44,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(55,64,20,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:40:14','MIH_A_1792131014','TXN_A_1792131014','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(56,64,20,65,9,44,2,2,5000.00,'2026-10-31','paid','2026-10-16 11:40:14','MIH_B_1792131014','TXN_B_1792131014','Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(57,1,NULL,NULL,9,44,2,3,3000.00,'2026-11-10','pending',NULL,NULL,NULL,'Test milestone | Rescheduled from 2026-10-26 to 2026-11-10: Student requested exam extension',NULL,0,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(58,1,NULL,NULL,9,44,2,2,5000.00,'2026-10-19','pending',NULL,NULL,NULL,'Reminder test milestone','2026-10-16 11:40:18',1,'2026-10-16 11:40:18','2026-10-16 11:40:14','2026-10-16 11:40:18'),(59,70,21,NULL,7,13,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:40:32','MIH_INST_001','TCM_INST_1792131032_M1','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:40:32','2026-10-16 11:40:32'),(60,70,21,NULL,7,13,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:40:32','2026-10-16 11:40:32'),(61,73,22,74,3,35,1,2,6499.50,'2026-10-16','pending',NULL,NULL,'TCM_INST_20261016114203_8D68AE','Upfront Payment (Milestone 1 of 2)','2026-10-16 11:56:09',3,'2026-10-16 11:56:09','2026-10-16 11:42:03','2026-10-16 11:56:09'),(62,73,22,NULL,3,35,2,2,6499.50,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:42:03','2026-10-16 11:42:03'),(63,75,23,NULL,9,44,1,3,4800.00,'2026-10-16','pending',NULL,NULL,NULL,'Upfront Payment (Milestone 1 of 3)','2026-10-16 11:56:07',3,'2026-10-16 11:56:07','2026-10-16 11:56:04','2026-10-16 11:56:07'),(64,75,23,NULL,9,44,2,3,3600.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 3 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(65,75,23,NULL,9,44,3,3,3600.00,'2026-11-15','pending',NULL,NULL,NULL,'Milestone 3 of 3 (Due in 30 days)',NULL,0,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(66,76,24,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:56:04','MIH_M1_1792131964','TXN_M1_1792131964','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(67,76,24,NULL,9,44,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(68,77,25,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:56:04','MIH_A_1792131964','TXN_A_1792131964','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(69,77,25,78,9,44,2,2,5000.00,'2026-10-31','paid','2026-10-16 11:56:04','MIH_B_1792131964','TXN_B_1792131964','Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(70,1,NULL,NULL,9,44,2,3,3000.00,'2026-11-10','pending',NULL,NULL,NULL,'Test milestone | Rescheduled from 2026-10-26 to 2026-11-10: Student requested exam extension',NULL,0,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(71,1,NULL,NULL,9,44,2,2,5000.00,'2026-10-19','pending',NULL,NULL,NULL,'Reminder test milestone','2026-10-16 11:56:11',1,'2026-10-16 11:56:11','2026-10-16 11:56:04','2026-10-16 11:56:11'),(72,83,26,NULL,7,13,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:56:23','MIH_INST_001','TCM_INST_1792131983_M1','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:56:23','2026-10-16 11:56:23'),(73,83,26,NULL,7,13,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:56:23','2026-10-16 11:56:23'),(74,86,27,NULL,9,44,1,3,4800.00,'2026-10-16','pending',NULL,NULL,NULL,'Upfront Payment (Milestone 1 of 3)','2026-10-16 11:59:56',3,'2026-10-16 11:59:56','2026-10-16 11:59:54','2026-10-16 11:59:56'),(75,86,27,NULL,9,44,2,3,3600.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 3 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(76,86,27,NULL,9,44,3,3,3600.00,'2026-11-15','pending',NULL,NULL,NULL,'Milestone 3 of 3 (Due in 30 days)',NULL,0,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(77,87,28,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:59:54','MIH_M1_1792132194','TXN_M1_1792132194','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(78,87,28,NULL,9,44,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(79,88,29,NULL,9,44,1,2,5000.00,'2026-10-16','paid','2026-10-16 11:59:54','MIH_A_1792132194','TXN_A_1792132194','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(80,88,29,89,9,44,2,2,5000.00,'2026-10-31','paid','2026-10-16 11:59:54','MIH_B_1792132194','TXN_B_1792132194','Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(81,1,NULL,NULL,9,44,2,3,3000.00,'2026-11-10','pending',NULL,NULL,NULL,'Test milestone | Rescheduled from 2026-10-26 to 2026-11-10: Student requested exam extension',NULL,0,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(82,1,NULL,NULL,9,44,2,2,5000.00,'2026-10-19','pending',NULL,NULL,NULL,'Reminder test milestone','2026-10-16 11:59:58',1,'2026-10-16 11:59:58','2026-10-16 11:59:54','2026-10-16 11:59:58'),(83,94,30,NULL,7,13,1,2,5000.00,'2026-10-16','paid','2026-10-16 12:00:01','MIH_INST_001','TCM_INST_1792132201_M1','Upfront Payment (Milestone 1 of 2)',NULL,0,NULL,'2026-10-16 12:00:01','2026-10-16 12:00:01'),(84,94,30,NULL,7,13,2,2,5000.00,'2026-10-31','pending',NULL,NULL,NULL,'Milestone 2 of 2 (Due in 15 days)',NULL,0,NULL,'2026-10-16 12:00:01','2026-10-16 12:00:01');
/*!40000 ALTER TABLE `installment_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internship_applications`
--

DROP TABLE IF EXISTS `internship_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internship_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `college` varchar(160) DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `why` text DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `resume_file` varchar(255) DEFAULT NULL,
  `status` enum('submitted','under_review','shortlisted','selected','rejected') NOT NULL DEFAULT 'submitted',
  `notes` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_appl_user_program` (`user_id`,`program_id`),
  KEY `idx_appl_status` (`status`),
  KEY `idx_appl_program` (`program_id`),
  CONSTRAINT `fk_appl_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internship_applications`
--

LOCK TABLES `internship_applications` WRITE;
/*!40000 ALTER TABLE `internship_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `internship_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `interest_type` enum('course','event','program','general','contact') NOT NULL DEFAULT 'general',
  `interest_id` bigint(20) unsigned DEFAULT NULL,
  `interest_title` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `source` varchar(80) DEFAULT 'website',
  `status` enum('new','contacted','converted','lost') NOT NULL DEFAULT 'new',
  `whatsapp_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_leads_status` (`status`),
  KEY `idx_leads_user` (`user_id`),
  CONSTRAINT `fk_lead_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,NULL,'Sneha Gupta','sneha@example.com','9876543210','program',1,'TCM Live Classes — Full Stack','Interested in the full stack live classes. Please share details.','website','new',0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(2,NULL,'Mohit Raj','mohit@example.com','9876500000','course',5,'DSA & Problem Solving','Want to know about the next DSA batch.','website','contacted',0,'2026-10-13 15:04:10','2026-10-13 15:04:10'),(3,NULL,'Rahul Sharma','rahul.test@example.com','9876543210','course',39,'Prompt Engineering & Generative AI','Requested free demo for Prompt Engineering & Generative AI\nCourse price: ₹1,499\nPreferred slot: Evening 6 PM - 9 PM\nGoal: Job in AI','free_demo_modal','new',0,'2026-10-14 12:10:14','2026-10-14 12:10:14'),(4,NULL,'Ayushman Chourasiya','imayushmanchourasia@gmail.com','09202209158','course',17,'DSA & Problem Solving with Java','Requested free demo for DSA & Problem Solving with Java\nCourse price: ₹7,999\nPreferred slot: Evening 6 PM - 9 PM','free_demo_modal','new',0,'2026-10-14 12:21:09','2026-10-14 12:21:09');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lesson_progress`
--

DROP TABLE IF EXISTS `lesson_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lesson_progress` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `lesson_id` bigint(20) unsigned NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_progress_user_lesson` (`user_id`,`lesson_id`),
  KEY `fk_progress_lesson` (`lesson_id`),
  CONSTRAINT `fk_progress_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `course_lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lesson_progress`
--

LOCK TABLES `lesson_progress` WRITE;
/*!40000 ALTER TABLE `lesson_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `lesson_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_class_links`
--

DROP TABLE IF EXISTS `live_class_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_class_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `meeting_url` varchar(500) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `target` enum('all','course','program','specific') NOT NULL DEFAULT 'all',
  `target_id` bigint(20) unsigned DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `sent_by` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_lcl_target` (`target`),
  KEY `idx_lcl_sent` (`sent_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_class_links`
--

LOCK TABLES `live_class_links` WRITE;
/*!40000 ALTER TABLE `live_class_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `live_class_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_sessions`
--

DROP TABLE IF EXISTS `live_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `program_id` bigint(20) unsigned DEFAULT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `host` varchar(120) DEFAULT NULL,
  `session_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `meeting_url` varchar(255) DEFAULT NULL,
  `recording_url` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','live','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ls_program` (`program_id`),
  KEY `idx_ls_course` (`course_id`),
  CONSTRAINT `fk_ls_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ls_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_sessions`
--

LOCK TABLES `live_sessions` WRITE;
/*!40000 ALTER TABLE `live_sessions` DISABLE KEYS */;
INSERT INTO `live_sessions` VALUES (1,1,NULL,'Kickoff: Web Dev Foundations','Orientation and how the web works.','Ayushman','2026-07-01','19:00:00','20:30:00','https://meet.google.com/demo-tcm',NULL,'scheduled','2026-10-13 15:04:10'),(2,1,NULL,'HTML & CSS Live Code-along','Build a landing page together.','Ayushman','2026-07-02','19:00:00','20:30:00','https://meet.google.com/demo-tcm',NULL,'scheduled','2026-10-13 15:04:10'),(3,2,NULL,'Weekly Doubt Session','Bring your blockers, we solve them live.','TCM Mentor','2026-07-11','11:00:00','12:00:00','https://meet.google.com/demo-tcm',NULL,'scheduled','2026-10-13 15:04:10');
/*!40000 ALTER TABLE `live_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletter_subscribers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_news_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_subscribers`
--

LOCK TABLES `newsletter_subscribers` WRITE;
/*!40000 ALTER TABLE `newsletter_subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` varchar(20) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `body` text DEFAULT NULL,
  `icon` varchar(10) NOT NULL DEFAULT '?',
  `click_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`),
  KEY `idx_notif_unread` (`user_id`,`is_read`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,'admin','🎟️ New Workshop Lead — Aman Verma','Aman Verma registered interest for Python Starter Workshop (₹99).','🎟️','/admin/workshop-leads',0,'2026-10-13 15:36:38'),(2,1,'admin','🎯 New Lead — Rahul Sharma','Rahul Sharma is interested in Prompt Engineering & Generative AI. Review and follow up.','🎯','/admin/leads',0,'2026-10-14 12:10:14'),(3,1,'admin','🎯 New Lead — Ayushman Chourasiya','Ayushman Chourasiya is interested in DSA & Problem Solving with Java. Review and follow up.','🎯','/admin/leads',0,'2026-10-14 12:21:09'),(4,1,'admin','🎟️ New Workshop Lead — Ayushman Ayushman','Ayushman Ayushman registered interest for Python Starter Workshop (₹99).','🎟️','/tcm/admin/workshop-leads',0,'2026-10-16 00:37:26'),(5,1,'admin','🎟️ New Workshop Lead — Test Learner','Test Learner registered interest for Python Starter Workshop (₹99).','🎟️','/tcm/admin/workshop-leads',0,'2026-10-16 01:21:24'),(6,1,'admin','🎟️ New Workshop Lead — Free Learner','Free Learner registered interest for Find Your Tech Career Path.','🎟️','/tcm/admin/workshop-leads',0,'2026-10-16 01:21:38');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_number` varchar(40) NOT NULL,
  `item_type` enum('course','event') NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `item_title` varchar(200) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_installment` tinyint(1) NOT NULL DEFAULT 0,
  `installment_number` int(10) unsigned DEFAULT NULL,
  `total_installments` int(10) unsigned DEFAULT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'INR',
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `gateway_status` varchar(50) DEFAULT NULL,
  `payment_method` varchar(40) DEFAULT NULL,
  `payment_ref` varchar(120) DEFAULT NULL,
  `receipt_number` varchar(60) DEFAULT NULL,
  `verified_by_api` tinyint(1) NOT NULL DEFAULT 0,
  `txnid` varchar(100) DEFAULT NULL,
  `raw_response` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_orders_number` (`order_number`),
  KEY `idx_orders_user` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,'TCM-20261016-EB8350','course',31,'Software Testing & QA with Playwright',3999.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed','payu','TCM_CR_20261016005503_5C2CDF',NULL,0,'TCM_CR_20261016005503_5C2CDF',NULL,'2026-10-16 00:55:03','2026-10-16 08:35:40'),(2,4,'TCM-20261016-C835FE','event',10,'Python Starter Workshop',99.00,0.00,0.00,0,NULL,NULL,'INR','pending',NULL,'payu','TCM_WS_20261016012124_E3D58D',NULL,0,'TCM_WS_20261016012124_E3D58D',NULL,'2026-10-16 01:21:24','2026-10-16 01:21:24'),(3,5,'TCM-20261016-4ABD31','event',13,'Find Your Tech Career Path',0.00,0.00,0.00,0,NULL,NULL,'INR','paid',NULL,'free','FREE_ENROLL',NULL,0,NULL,NULL,'2026-10-16 01:21:38','2026-10-16 01:21:38'),(4,4,'TCM-20261016-96FED3','event',10,'Python Starter Workshop | Advanced Live Coding & Web Scraping',99.00,0.00,0.00,0,NULL,NULL,'INR','pending',NULL,'payu','TCM_WS_20261016014350_8BBAA4',NULL,0,'TCM_WS_20261016014350_8BBAA4',NULL,'2026-10-16 01:43:50','2026-10-16 01:43:50'),(12,7,'TCM-20261016-C76F38','course',13,'Test Course',99.00,99.00,0.00,0,NULL,NULL,'INR','paid','success','payu','MIH_5684f27d','TCM-REC-202610-00012',0,'TCM_TEST_1792127846_A','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792127846_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_5684f27d\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-C76F38\",\"hash\":\"90ca8f19d7b9a0ed5a8c252c16f1df2c554a12813baedbd5e25d200b8a853799df2188b4b6895ab0d4bf1a69d2a9cd1d2a2bca682fdc4a4ef52c8c8a7060e08c\"}','2026-10-16 10:47:26','2026-10-16 10:47:26'),(13,7,'TCM-20261016-7B0C57','course',13,'Idempotency Test Course',199.00,199.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,'MIH_EXISTING','TCM-REC-EXISTING',0,'TCM_TEST_1792127849_IDEMP',NULL,'2026-10-16 10:47:29','2026-10-16 10:47:29'),(14,7,'TCM-20261016-9F2A68','course',13,'Protected Course',499.00,499.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,NULL,'TCM-REC-PROTECTED',0,'TCM_TEST_1792127849_OUTOFORDER',NULL,'2026-10-16 10:47:29','2026-10-16 10:47:29'),(15,7,'TCM-20261016-56B3A2','course',13,'Failed Test Course',149.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed',NULL,NULL,NULL,0,'TCM_TEST_1792127849_LEGIT_FAIL','{\"txnid\":\"TCM_TEST_1792127849_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-56B3A2\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 10:47:29','2026-10-16 10:47:29'),(16,7,'TCM-20261016-22AFC8','course',13,'Full Stack Bootcamp',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending','installment_plan','payu','PLAN_TEST',NULL,0,NULL,NULL,'2026-10-16 10:47:29','2026-10-16 10:47:29'),(17,7,'TCM-20261016-71E7A9','course',13,'Refundable Course',1000.00,0.00,1000.00,0,NULL,NULL,'INR','refunded','refunded',NULL,NULL,NULL,0,'TCM_REF_1792127849_TXN',NULL,'2026-10-16 10:47:29','2026-10-16 10:47:29'),(18,7,'TCM-20261016-C9A73D','course',13,'Disputed Course',2500.00,2500.00,0.00,0,NULL,NULL,'INR','paid','disputed',NULL,NULL,NULL,0,'TCM_DISP_1792127849_TXN',NULL,'2026-10-16 10:47:29','2026-10-16 10:47:29'),(19,8,'TCM-20261016-B9DE14','course',35,'Machine Learning with Python',12999.00,0.00,12999.00,1,1,3,'INR','pending','installment_plan','payu','PLAN_47bb4704',NULL,0,NULL,NULL,'2026-10-16 11:07:32','2026-10-16 11:07:32'),(20,8,'TCM-INST-20261016-5-D8B1','course',35,'Machine Learning with Python (Installment 1 of 3)',6499.50,0.00,6499.50,1,1,3,'INR','pending','initiated','payu','TCM_INST_20261016110732_EF8C37',NULL,0,'TCM_INST_20261016110732_EF8C37',NULL,'2026-10-16 11:07:32','2026-10-16 11:07:32'),(21,7,'TCM-20261016-8064F0','course',13,'Test Course',99.00,99.00,0.00,0,NULL,NULL,'INR','paid','success','payu','MIH_f0a15d4a','TCM-REC-202610-00021',0,'TCM_TEST_1792129181_A','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129181_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_f0a15d4a\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-8064F0\",\"hash\":\"6824b399bdebf4f8e0c30224af51bc9a5a6e660977cc27a80338ef1669c520d1d9a322facd6a7055a40633b9ce452e31652528892b00e8709db8e2d4d616293b\"}','2026-10-16 11:09:41','2026-10-16 11:09:41'),(22,7,'TCM-20261016-531513','course',13,'Idempotency Test Course',199.00,199.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,'MIH_EXISTING','TCM-REC-EXISTING',0,'TCM_TEST_1792129183_IDEMP',NULL,'2026-10-16 11:09:43','2026-10-16 11:09:43'),(23,7,'TCM-20261016-C434B3','course',13,'Protected Course',499.00,499.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,NULL,'TCM-REC-PROTECTED',0,'TCM_TEST_1792129183_OUTOFORDER',NULL,'2026-10-16 11:09:43','2026-10-16 11:09:43'),(24,7,'TCM-20261016-B7845D','course',13,'Failed Test Course',149.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed',NULL,NULL,NULL,0,'TCM_TEST_1792129183_LEGIT_FAIL','{\"txnid\":\"TCM_TEST_1792129183_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-B7845D\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:09:43','2026-10-16 11:09:43'),(25,7,'TCM-20261016-0D9A78','course',13,'Full Stack Bootcamp',10000.00,0.00,10000.00,1,NULL,2,'INR','pending','installment_plan','payu','PLAN_TEST',NULL,0,NULL,NULL,'2026-10-16 11:09:43','2026-10-16 11:09:43'),(26,7,'TCM-20261016-6E9C24','course',13,'Test Course',99.00,99.00,0.00,0,NULL,NULL,'INR','paid','success','payu','MIH_331d77b8','TCM-REC-202610-00026',0,'TCM_TEST_1792129225_A','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129225_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_331d77b8\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-6E9C24\",\"hash\":\"f55d413671c7ad42b9bd63b8dd4f8b4a150ed3e2357667ba744f17836a69f0c3e99587ced60d0c4d66db60cfc03b547130663c02ba7f9a59405edef8f93ebce0\"}','2026-10-16 11:10:25','2026-10-16 11:10:25'),(27,7,'TCM-20261016-36A380','course',13,'Idempotency Test Course',199.00,199.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,'MIH_EXISTING','TCM-REC-EXISTING',0,'TCM_TEST_1792129228_IDEMP',NULL,'2026-10-16 11:10:28','2026-10-16 11:10:28'),(28,7,'TCM-20261016-C8F0F8','course',13,'Protected Course',499.00,499.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,NULL,'TCM-REC-PROTECTED',0,'TCM_TEST_1792129228_OUTOFORDER',NULL,'2026-10-16 11:10:28','2026-10-16 11:10:28'),(29,7,'TCM-20261016-50861D','course',13,'Failed Test Course',149.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed',NULL,NULL,NULL,0,'TCM_TEST_1792129228_LEGIT_FAIL','{\"txnid\":\"TCM_TEST_1792129228_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-50861D\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:10:28','2026-10-16 11:10:28'),(30,7,'TCM-20261016-E7DDCE','course',13,'Full Stack Bootcamp',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending','installment_plan','payu','PLAN_TEST',NULL,0,NULL,NULL,'2026-10-16 11:10:28','2026-10-16 11:10:28'),(31,7,'TCM-20261016-5DD14D','course',13,'Refundable Course',1000.00,0.00,1000.00,0,NULL,NULL,'INR','refunded','refunded',NULL,NULL,NULL,0,'TCM_REF_1792129228_TXN',NULL,'2026-10-16 11:10:28','2026-10-16 11:10:28'),(32,7,'TCM-20261016-E5AB9F','course',13,'Disputed Course',2500.00,2500.00,0.00,0,NULL,NULL,'INR','paid','disputed',NULL,NULL,NULL,0,'TCM_DISP_1792129228_TXN',NULL,'2026-10-16 11:10:28','2026-10-16 11:10:28'),(33,9,'ORD-TEST-1792129475','course',44,'Test Fullstack Pro',12000.00,0.00,12000.00,1,NULL,3,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(34,9,'ORD-M1-1792129475','course',44,'Test Fullstack Pro',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(35,9,'ORD-FULL-1792129475','course',44,'Test Fullstack Pro',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:14:35','2026-10-16 11:14:35'),(36,9,'ORD-TEST-1792129558','course',44,'Test Fullstack Pro',12000.00,0.00,12000.00,1,NULL,3,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(37,9,'ORD-M1-1792129558','course',44,'Test Fullstack Pro',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(38,9,'ORD-FULL-1792129558','course',44,'Test Fullstack Pro',10000.00,10000.00,0.00,1,NULL,2,'INR','paid',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(39,9,'TCM-INST-20261016-25-A715','course',44,'Test Fullstack Pro (Installment 2 of 2)',5000.00,0.00,5000.00,1,2,2,'INR','pending','initiated','payu','TCM_INST_20261016111558_1A7B86',NULL,0,'TCM_INST_20261016111558_1A7B86',NULL,'2026-10-16 11:15:58','2026-10-16 11:15:58'),(40,9,'ORD-TEST-1792129635','course',44,'Test Fullstack Pro',12000.00,0.00,12000.00,1,NULL,3,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(41,9,'ORD-M1-1792129635','course',44,'Test Fullstack Pro',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(42,9,'ORD-FULL-1792129635','course',44,'Test Fullstack Pro',10000.00,10000.00,0.00,1,NULL,2,'INR','paid',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(43,9,'TCM-INST-20261016-34-8C52','course',44,'Test Fullstack Pro (Installment 2 of 2)',5000.00,0.00,5000.00,1,2,2,'INR','pending','initiated','payu','TCM_INST_20261016111715_D8CB8D',NULL,0,'TCM_INST_20261016111715_D8CB8D',NULL,'2026-10-16 11:17:15','2026-10-16 11:17:15'),(44,7,'TCM-20261016-0C29DC','course',13,'Test Course',99.00,99.00,0.00,0,NULL,NULL,'INR','paid','success','payu','MIH_25f1f8d4','TCM-REC-202610-00044',0,'TCM_TEST_1792129670_A','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129670_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_25f1f8d4\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-0C29DC\",\"hash\":\"4aae71936a8d4a4dc9d26e0330ec3f4e6bbce7ee61f5e6c97d11aba977c8b4f59e4624c96d2936ad55a5a8d032657b2fda055059f3e9a96344020fab0b116e45\"}','2026-10-16 11:17:50','2026-10-16 11:17:50'),(45,7,'TCM-20261016-FED0E3','course',13,'Idempotency Test Course',199.00,199.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,'MIH_EXISTING','TCM-REC-EXISTING',0,'TCM_TEST_1792129673_IDEMP',NULL,'2026-10-16 11:17:53','2026-10-16 11:17:53'),(46,7,'TCM-20261016-622CF9','course',13,'Protected Course',499.00,499.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,NULL,'TCM-REC-PROTECTED',0,'TCM_TEST_1792129673_OUTOFORDER',NULL,'2026-10-16 11:17:53','2026-10-16 11:17:53'),(47,7,'TCM-20261016-96FF0F','course',13,'Failed Test Course',149.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed',NULL,NULL,NULL,0,'TCM_TEST_1792129673_LEGIT_FAIL','{\"txnid\":\"TCM_TEST_1792129673_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-96FF0F\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:17:53','2026-10-16 11:17:53'),(48,7,'TCM-20261016-2B636C','course',13,'Full Stack Bootcamp',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending','installment_plan','payu','PLAN_TEST',NULL,0,NULL,NULL,'2026-10-16 11:17:53','2026-10-16 11:17:53'),(49,7,'TCM-20261016-13CB5F','course',13,'Refundable Course',1000.00,0.00,1000.00,0,NULL,NULL,'INR','refunded','refunded',NULL,NULL,NULL,0,'TCM_REF_1792129673_TXN',NULL,'2026-10-16 11:17:53','2026-10-16 11:17:53'),(50,7,'TCM-20261016-37ED44','course',13,'Disputed Course',2500.00,2500.00,0.00,0,NULL,NULL,'INR','paid','disputed',NULL,NULL,NULL,0,'TCM_DISP_1792129673_TXN',NULL,'2026-10-16 11:17:53','2026-10-16 11:17:53'),(51,9,'ORD-TEST-1792130381','course',44,'Test Fullstack Pro',12000.00,0.00,12000.00,1,NULL,3,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(52,9,'ORD-M1-1792130381','course',44,'Test Fullstack Pro',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(53,9,'ORD-FULL-1792130381','course',44,'Test Fullstack Pro',10000.00,10000.00,0.00,1,NULL,2,'INR','paid',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(54,9,'TCM-INST-20261016-45-7120','course',44,'Test Fullstack Pro (Installment 2 of 2)',5000.00,0.00,5000.00,1,2,2,'INR','pending','initiated','payu','TCM_INST_20261016112941_50EA6E',NULL,0,'TCM_INST_20261016112941_50EA6E',NULL,'2026-10-16 11:29:41','2026-10-16 11:29:41'),(55,7,'TCM-20261016-AB6C20','course',13,'Test Course',99.00,99.00,0.00,0,NULL,NULL,'INR','paid','success','payu','MIH_8aa4c536','TCM-REC-202610-00055',0,'TCM_TEST_1792130394_A','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792130394_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_8aa4c536\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-AB6C20\",\"hash\":\"1e4e81803a47509eb23038da128760192b5e04f1772bd846edf02219adf5ea5a6eee13a471b57dadccde9ebfb46682b81a9c61542ad7e2aabda0bdd522549f39\"}','2026-10-16 11:29:54','2026-10-16 11:29:54'),(56,7,'TCM-20261016-4EDE27','course',13,'Idempotency Test Course',199.00,199.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,'MIH_EXISTING','TCM-REC-EXISTING',0,'TCM_TEST_1792130397_IDEMP',NULL,'2026-10-16 11:29:57','2026-10-16 11:29:57'),(57,7,'TCM-20261016-97D8D1','course',13,'Protected Course',499.00,499.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,NULL,'TCM-REC-PROTECTED',0,'TCM_TEST_1792130397_OUTOFORDER',NULL,'2026-10-16 11:29:57','2026-10-16 11:29:57'),(58,7,'TCM-20261016-7A7FDE','course',13,'Failed Test Course',149.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed',NULL,NULL,NULL,0,'TCM_TEST_1792130397_LEGIT_FAIL','{\"txnid\":\"TCM_TEST_1792130397_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-7A7FDE\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:29:57','2026-10-16 11:29:57'),(59,7,'TCM-20261016-6A110C','course',13,'Full Stack Bootcamp',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending','installment_plan','payu','PLAN_TEST',NULL,0,NULL,NULL,'2026-10-16 11:29:57','2026-10-16 11:29:57'),(60,7,'TCM-20261016-E4D9EA','course',13,'Refundable Course',1000.00,0.00,1000.00,0,NULL,NULL,'INR','refunded','refunded',NULL,NULL,NULL,0,'TCM_REF_1792130397_TXN',NULL,'2026-10-16 11:29:57','2026-10-16 11:29:57'),(61,7,'TCM-20261016-3E4F87','course',13,'Disputed Course',2500.00,2500.00,0.00,0,NULL,NULL,'INR','paid','disputed',NULL,NULL,NULL,0,'TCM_DISP_1792130397_TXN',NULL,'2026-10-16 11:29:57','2026-10-16 11:29:57'),(62,9,'ORD-TEST-1792131014','course',44,'Test Fullstack Pro',12000.00,0.00,12000.00,1,NULL,3,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(63,9,'ORD-M1-1792131014','course',44,'Test Fullstack Pro',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(64,9,'ORD-FULL-1792131014','course',44,'Test Fullstack Pro',10000.00,10000.00,0.00,1,NULL,2,'INR','paid',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(65,9,'TCM-INST-20261016-56-2B5B','course',44,'Test Fullstack Pro (Installment 2 of 2)',5000.00,0.00,5000.00,1,2,2,'INR','pending','initiated','payu','TCM_INST_20261016114014_B3188F',NULL,0,'TCM_INST_20261016114014_B3188F',NULL,'2026-10-16 11:40:14','2026-10-16 11:40:14'),(66,7,'TCM-20261016-6470A8','course',13,'Test Course',99.00,99.00,0.00,0,NULL,NULL,'INR','paid','success','payu','MIH_78e68bd9','TCM-REC-202610-00066',0,'TCM_TEST_1792131030_A','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792131030_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_78e68bd9\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-6470A8\",\"hash\":\"dcbbf9e7a71b7c0f716b6aadc0b3768dd1e9af9164ffd2266311bdc39ba084e84bafd794d44cb19f5f4de69716654f3f1ea6d2c144be790ff116d7f6b0e39929\"}','2026-10-16 11:40:30','2026-10-16 11:40:30'),(67,7,'TCM-20261016-F7B9DA','course',13,'Idempotency Test Course',199.00,199.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,'MIH_EXISTING','TCM-REC-EXISTING',0,'TCM_TEST_1792131032_IDEMP',NULL,'2026-10-16 11:40:32','2026-10-16 11:40:32'),(68,7,'TCM-20261016-404DB1','course',13,'Protected Course',499.00,499.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,NULL,'TCM-REC-PROTECTED',0,'TCM_TEST_1792131032_OUTOFORDER',NULL,'2026-10-16 11:40:32','2026-10-16 11:40:32'),(69,7,'TCM-20261016-C134C7','course',13,'Failed Test Course',149.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed',NULL,NULL,NULL,0,'TCM_TEST_1792131032_LEGIT_FAIL','{\"txnid\":\"TCM_TEST_1792131032_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-C134C7\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:40:32','2026-10-16 11:40:32'),(70,7,'TCM-20261016-6CB780','course',13,'Full Stack Bootcamp',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending','installment_plan','payu','PLAN_TEST',NULL,0,NULL,NULL,'2026-10-16 11:40:32','2026-10-16 11:40:32'),(71,7,'TCM-20261016-FD4E29','course',13,'Refundable Course',1000.00,0.00,1000.00,0,NULL,NULL,'INR','refunded','refunded',NULL,NULL,NULL,0,'TCM_REF_1792131032_TXN',NULL,'2026-10-16 11:40:32','2026-10-16 11:40:32'),(72,7,'TCM-20261016-09E5A7','course',13,'Disputed Course',2500.00,2500.00,0.00,0,NULL,NULL,'INR','paid','disputed',NULL,NULL,NULL,0,'TCM_DISP_1792131032_TXN',NULL,'2026-10-16 11:40:32','2026-10-16 11:40:32'),(73,3,'TCM-20261016-1688CF','course',35,'Machine Learning with Python',12999.00,0.00,12999.00,1,1,2,'INR','pending','installment_plan','payu','PLAN_aafeb838',NULL,0,NULL,NULL,'2026-10-16 11:42:03','2026-10-16 11:42:03'),(74,3,'TCM-INST-20261016-61-1637','course',35,'Machine Learning with Python (Installment 1 of 2)',6499.50,0.00,6499.50,1,1,2,'INR','pending','initiated','payu','TCM_INST_20261016114203_8D68AE',NULL,0,'TCM_INST_20261016114203_8D68AE',NULL,'2026-10-16 11:42:03','2026-10-16 11:42:03'),(75,9,'ORD-TEST-1792131964','course',44,'Test Fullstack Pro',12000.00,0.00,12000.00,1,NULL,3,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(76,9,'ORD-M1-1792131964','course',44,'Test Fullstack Pro',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(77,9,'ORD-FULL-1792131964','course',44,'Test Fullstack Pro',10000.00,10000.00,0.00,1,NULL,2,'INR','paid',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(78,9,'TCM-INST-20261016-69-182B','course',44,'Test Fullstack Pro (Installment 2 of 2)',5000.00,0.00,5000.00,1,2,2,'INR','pending','initiated','payu','TCM_INST_20261016115604_79FE96',NULL,0,'TCM_INST_20261016115604_79FE96',NULL,'2026-10-16 11:56:04','2026-10-16 11:56:04'),(79,7,'TCM-20261016-3F4714','course',13,'Test Course',99.00,99.00,0.00,0,NULL,NULL,'INR','paid','success','payu','MIH_1c4b4379','TCM-REC-202610-00079',0,'TCM_TEST_1792131978_A','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792131978_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_1c4b4379\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-3F4714\",\"hash\":\"f44a488ec39e0c7b344942d5caa064bf34685be9e415b3c38dd0c786037df41ac11fa6d3eae200a415d5d594fdfea344ac90e2d1f13870a32df3483b0d38903c\"}','2026-10-16 11:56:18','2026-10-16 11:56:22'),(80,7,'TCM-20261016-92E249','course',13,'Idempotency Test Course',199.00,199.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,'MIH_EXISTING','TCM-REC-EXISTING',0,'TCM_TEST_1792131983_IDEMP',NULL,'2026-10-16 11:56:23','2026-10-16 11:56:23'),(81,7,'TCM-20261016-40D769','course',13,'Protected Course',499.00,499.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,NULL,'TCM-REC-PROTECTED',0,'TCM_TEST_1792131983_OUTOFORDER',NULL,'2026-10-16 11:56:23','2026-10-16 11:56:23'),(82,7,'TCM-20261016-8DF26B','course',13,'Failed Test Course',149.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed',NULL,NULL,NULL,0,'TCM_TEST_1792131983_LEGIT_FAIL','{\"txnid\":\"TCM_TEST_1792131983_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-8DF26B\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:56:23','2026-10-16 11:56:23'),(83,7,'TCM-20261016-1FC77C','course',13,'Full Stack Bootcamp',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending','installment_plan','payu','PLAN_TEST',NULL,0,NULL,NULL,'2026-10-16 11:56:23','2026-10-16 11:56:23'),(84,7,'TCM-20261016-0AFEB9','course',13,'Refundable Course',1000.00,0.00,1000.00,0,NULL,NULL,'INR','refunded','refunded',NULL,NULL,NULL,0,'TCM_REF_1792131983_TXN',NULL,'2026-10-16 11:56:23','2026-10-16 11:56:24'),(85,7,'TCM-20261016-9E1D94','course',13,'Disputed Course',2500.00,2500.00,0.00,0,NULL,NULL,'INR','paid','disputed',NULL,NULL,NULL,0,'TCM_DISP_1792131984_TXN',NULL,'2026-10-16 11:56:24','2026-10-16 11:56:24'),(86,9,'ORD-TEST-1792132194','course',44,'Test Fullstack Pro',12000.00,0.00,12000.00,1,NULL,3,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(87,9,'ORD-M1-1792132194','course',44,'Test Fullstack Pro',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(88,9,'ORD-FULL-1792132194','course',44,'Test Fullstack Pro',10000.00,10000.00,0.00,1,NULL,2,'INR','paid',NULL,'payu',NULL,NULL,0,NULL,NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(89,9,'TCM-INST-20261016-80-9C1C','course',44,'Test Fullstack Pro (Installment 2 of 2)',5000.00,0.00,5000.00,1,2,2,'INR','pending','initiated','payu','TCM_INST_20261016115954_8DFD87',NULL,0,'TCM_INST_20261016115954_8DFD87',NULL,'2026-10-16 11:59:54','2026-10-16 11:59:54'),(90,7,'TCM-20261016-EED4DB','course',13,'Test Course',99.00,99.00,0.00,0,NULL,NULL,'INR','paid','success','payu','MIH_c8c45d8b','TCM-REC-202610-00090',0,'TCM_TEST_1792132198_A','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792132198_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_c8c45d8b\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-EED4DB\",\"hash\":\"6befcdbf628df6161c0deda499179a7d67ed80d6018c3b0e7317e90aef99b394b3eca2206483e40c9d318a32abe4a287d2cf7e7ab5e02a5436109fddb455b9d1\"}','2026-10-16 11:59:58','2026-10-16 11:59:59'),(91,7,'TCM-20261016-848FB0','course',13,'Idempotency Test Course',199.00,199.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,'MIH_EXISTING','TCM-REC-EXISTING',0,'TCM_TEST_1792132201_IDEMP',NULL,'2026-10-16 12:00:01','2026-10-16 12:00:01'),(92,7,'TCM-20261016-72A3E0','course',13,'Protected Course',499.00,499.00,0.00,0,NULL,NULL,'INR','paid','success',NULL,NULL,'TCM-REC-PROTECTED',0,'TCM_TEST_1792132201_OUTOFORDER',NULL,'2026-10-16 12:00:01','2026-10-16 12:00:01'),(93,7,'TCM-20261016-7E9184','course',13,'Failed Test Course',149.00,0.00,0.00,0,NULL,NULL,'INR','failed','failed',NULL,NULL,NULL,0,'TCM_TEST_1792132201_LEGIT_FAIL','{\"txnid\":\"TCM_TEST_1792132201_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-7E9184\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 12:00:01','2026-10-16 12:00:01'),(94,7,'TCM-20261016-17746D','course',13,'Full Stack Bootcamp',10000.00,5000.00,5000.00,1,NULL,2,'INR','pending','installment_plan','payu','PLAN_TEST',NULL,0,NULL,NULL,'2026-10-16 12:00:01','2026-10-16 12:00:01'),(95,7,'TCM-20261016-7DCEFC','course',13,'Refundable Course',1000.00,0.00,1000.00,0,NULL,NULL,'INR','refunded','refunded',NULL,NULL,NULL,0,'TCM_REF_1792132201_TXN',NULL,'2026-10-16 12:00:01','2026-10-16 12:00:01'),(96,7,'TCM-20261016-0BEBC9','course',13,'Disputed Course',2500.00,2500.00,0.00,0,NULL,NULL,'INR','paid','disputed',NULL,NULL,NULL,0,'TCM_DISP_1792132201_TXN',NULL,'2026-10-16 12:00:01','2026-10-16 12:00:01');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_disputes`
--

DROP TABLE IF EXISTS `payment_disputes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_disputes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_transaction_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `payu_dispute_id` varchar(100) DEFAULT NULL,
  `dispute_type` varchar(80) DEFAULT NULL,
  `dispute_status` varchar(80) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'INR',
  `reason` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `raw_payload` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pd_order` (`order_id`),
  KEY `idx_pd_user` (`user_id`),
  KEY `idx_pd_dispute_id` (`payu_dispute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_disputes`
--

LOCK TABLES `payment_disputes` WRITE;
/*!40000 ALTER TABLE `payment_disputes` DISABLE KEYS */;
INSERT INTO `payment_disputes` VALUES (2,NULL,18,7,'DISP_b68c593c','chargeback','open',2500.00,'INR','Cardholder unrecognized transaction','2026-10-23',NULL,NULL,'{\"txnid\":\"TCM_DISP_1792127849_TXN\",\"dispute_id\":\"DISP_b68c593c\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}','2026-10-16 10:47:29','2026-10-16 10:47:29'),(3,NULL,32,7,'DISP_99037ec9','chargeback','open',2500.00,'INR','Cardholder unrecognized transaction','2026-10-23',NULL,NULL,'{\"txnid\":\"TCM_DISP_1792129228_TXN\",\"dispute_id\":\"DISP_99037ec9\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}','2026-10-16 11:10:28','2026-10-16 11:10:28'),(4,NULL,50,7,'DISP_4d4b1b6f','chargeback','open',2500.00,'INR','Cardholder unrecognized transaction','2026-10-23',NULL,NULL,'{\"txnid\":\"TCM_DISP_1792129673_TXN\",\"dispute_id\":\"DISP_4d4b1b6f\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}','2026-10-16 11:17:53','2026-10-16 11:17:53'),(5,NULL,61,7,'DISP_bba3c98f','chargeback','open',2500.00,'INR','Cardholder unrecognized transaction','2026-10-23',NULL,NULL,'{\"txnid\":\"TCM_DISP_1792130397_TXN\",\"dispute_id\":\"DISP_bba3c98f\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}','2026-10-16 11:29:57','2026-10-16 11:29:57'),(6,NULL,72,7,'DISP_4634c831','chargeback','open',2500.00,'INR','Cardholder unrecognized transaction','2026-10-23',NULL,NULL,'{\"txnid\":\"TCM_DISP_1792131032_TXN\",\"dispute_id\":\"DISP_4634c831\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}','2026-10-16 11:40:32','2026-10-16 11:40:32'),(7,NULL,85,7,'DISP_dbef6732','chargeback','open',2500.00,'INR','Cardholder unrecognized transaction','2026-10-23',NULL,NULL,'{\"txnid\":\"TCM_DISP_1792131984_TXN\",\"dispute_id\":\"DISP_dbef6732\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}','2026-10-16 11:56:24','2026-10-16 11:56:24'),(8,NULL,96,7,'DISP_35114c6b','chargeback','open',2500.00,'INR','Cardholder unrecognized transaction','2026-10-23',NULL,NULL,'{\"txnid\":\"TCM_DISP_1792132201_TXN\",\"dispute_id\":\"DISP_35114c6b\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}','2026-10-16 12:00:01','2026-10-16 12:00:01');
/*!40000 ALTER TABLE `payment_disputes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_refunds`
--

DROP TABLE IF EXISTS `payment_refunds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_refunds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_transaction_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `payu_refund_id` varchar(100) DEFAULT NULL,
  `refund_txnid` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'INR',
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `reason` varchar(255) DEFAULT NULL,
  `raw_response` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pr_order` (`order_id`),
  KEY `idx_pr_user` (`user_id`),
  KEY `idx_pr_payu_id` (`payu_refund_id`),
  KEY `idx_pr_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_refunds`
--

LOCK TABLES `payment_refunds` WRITE;
/*!40000 ALTER TABLE `payment_refunds` DISABLE KEYS */;
INSERT INTO `payment_refunds` VALUES (2,NULL,17,7,'PAYU_REF_ff669bec','TCM_REF_1792127849_TXN',1000.00,'INR','success','Student requested cancellation within policy','{\"txnid\":\"TCM_REF_1792127849_TXN\",\"payu_refund_id\":\"PAYU_REF_ff669bec\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}','2026-10-16 10:47:29','2026-10-16 10:47:29'),(3,NULL,31,7,'PAYU_REF_384e70c1','TCM_REF_1792129228_TXN',1000.00,'INR','success','Student requested cancellation within policy','{\"txnid\":\"TCM_REF_1792129228_TXN\",\"payu_refund_id\":\"PAYU_REF_384e70c1\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}','2026-10-16 11:10:28','2026-10-16 11:10:28'),(4,NULL,49,7,'PAYU_REF_73cf8a83','TCM_REF_1792129673_TXN',1000.00,'INR','success','Student requested cancellation within policy','{\"txnid\":\"TCM_REF_1792129673_TXN\",\"payu_refund_id\":\"PAYU_REF_73cf8a83\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}','2026-10-16 11:17:53','2026-10-16 11:17:53'),(5,NULL,60,7,'PAYU_REF_97e5206a','TCM_REF_1792130397_TXN',1000.00,'INR','success','Student requested cancellation within policy','{\"txnid\":\"TCM_REF_1792130397_TXN\",\"payu_refund_id\":\"PAYU_REF_97e5206a\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}','2026-10-16 11:29:57','2026-10-16 11:29:57'),(6,NULL,71,7,'PAYU_REF_5516ec21','TCM_REF_1792131032_TXN',1000.00,'INR','success','Student requested cancellation within policy','{\"txnid\":\"TCM_REF_1792131032_TXN\",\"payu_refund_id\":\"PAYU_REF_5516ec21\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}','2026-10-16 11:40:32','2026-10-16 11:40:32'),(7,NULL,84,7,'PAYU_REF_a8ba1ebf','TCM_REF_1792131983_TXN',1000.00,'INR','success','Student requested cancellation within policy','{\"txnid\":\"TCM_REF_1792131983_TXN\",\"payu_refund_id\":\"PAYU_REF_a8ba1ebf\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}','2026-10-16 11:56:24','2026-10-16 11:56:24'),(8,NULL,95,7,'PAYU_REF_afd79be0','TCM_REF_1792132201_TXN',1000.00,'INR','success','Student requested cancellation within policy','{\"txnid\":\"TCM_REF_1792132201_TXN\",\"payu_refund_id\":\"PAYU_REF_afd79be0\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}','2026-10-16 12:00:01','2026-10-16 12:00:01');
/*!40000 ALTER TABLE `payment_refunds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `installment_schedule_id` bigint(20) unsigned DEFAULT NULL,
  `installment_plan_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `txnid` varchar(100) NOT NULL,
  `mihpayid` varchar(100) DEFAULT NULL,
  `bank_ref_num` varchar(100) DEFAULT NULL,
  `refund_reference` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(8) NOT NULL DEFAULT 'INR',
  `status` enum('pending','success','failure','refunded','disputed') NOT NULL DEFAULT 'pending',
  `payment_mode` varchar(50) DEFAULT NULL,
  `card_type` varchar(50) DEFAULT NULL,
  `bank_code` varchar(50) DEFAULT NULL,
  `error_code` varchar(100) DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `hash_verified` tinyint(1) NOT NULL DEFAULT 0,
  `api_verified` tinyint(1) NOT NULL DEFAULT 0,
  `raw_payload` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_txnid` (`txnid`),
  KEY `idx_pt_order` (`order_id`),
  KEY `idx_pt_user` (`user_id`),
  KEY `idx_pt_mihpayid` (`mihpayid`),
  KEY `idx_pt_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
INSERT INTO `payment_transactions` VALUES (4,12,NULL,NULL,7,'TCM_TEST_1792127846_A','MIH_5684f27d','BANK_REF_12345',NULL,99.00,'INR','success','UPI','','',NULL,NULL,1,0,'{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792127846_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_5684f27d\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-C76F38\",\"hash\":\"90ca8f19d7b9a0ed5a8c252c16f1df2c554a12813baedbd5e25d200b8a853799df2188b4b6895ab0d4bf1a69d2a9cd1d2a2bca682fdc4a4ef52c8c8a7060e08c\"}','2026-10-16 10:47:26','2026-10-16 10:47:26'),(5,14,NULL,NULL,7,'TCM_TEST_1792127849_OUTOFORDER_late_fail',NULL,NULL,NULL,499.00,'INR','failure',NULL,NULL,NULL,'E000','Delayed bank timeout notification',0,0,'{\"txnid\":\"TCM_TEST_1792127849_OUTOFORDER\",\"udf5\":\"TCM-20261016-9F2A68\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}','2026-10-16 10:47:29','2026-10-16 10:47:29'),(6,15,NULL,NULL,7,'TCM_TEST_1792127849_LEGIT_FAIL',NULL,NULL,NULL,149.00,'INR','failure',NULL,NULL,NULL,'E000','User cancelled payment on bank portal',0,0,'{\"txnid\":\"TCM_TEST_1792127849_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-56B3A2\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 10:47:29','2026-10-16 10:47:29'),(7,21,NULL,NULL,7,'TCM_TEST_1792129181_A','MIH_f0a15d4a','BANK_REF_12345',NULL,99.00,'INR','success','UPI','','',NULL,NULL,1,0,'{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129181_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_f0a15d4a\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-8064F0\",\"hash\":\"6824b399bdebf4f8e0c30224af51bc9a5a6e660977cc27a80338ef1669c520d1d9a322facd6a7055a40633b9ce452e31652528892b00e8709db8e2d4d616293b\"}','2026-10-16 11:09:41','2026-10-16 11:09:41'),(8,23,NULL,NULL,7,'TCM_TEST_1792129183_OUTOFORDER_late_fail',NULL,NULL,NULL,499.00,'INR','failure',NULL,NULL,NULL,'E000','Delayed bank timeout notification',0,0,'{\"txnid\":\"TCM_TEST_1792129183_OUTOFORDER\",\"udf5\":\"TCM-20261016-C434B3\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}','2026-10-16 11:09:43','2026-10-16 11:09:43'),(9,24,NULL,NULL,7,'TCM_TEST_1792129183_LEGIT_FAIL',NULL,NULL,NULL,149.00,'INR','failure',NULL,NULL,NULL,'E000','User cancelled payment on bank portal',0,0,'{\"txnid\":\"TCM_TEST_1792129183_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-B7845D\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:09:43','2026-10-16 11:09:43'),(10,26,NULL,NULL,7,'TCM_TEST_1792129225_A','MIH_331d77b8','BANK_REF_12345',NULL,99.00,'INR','success','UPI','','',NULL,NULL,1,0,'{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129225_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_331d77b8\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-6E9C24\",\"hash\":\"f55d413671c7ad42b9bd63b8dd4f8b4a150ed3e2357667ba744f17836a69f0c3e99587ced60d0c4d66db60cfc03b547130663c02ba7f9a59405edef8f93ebce0\"}','2026-10-16 11:10:25','2026-10-16 11:10:25'),(11,28,NULL,NULL,7,'TCM_TEST_1792129228_OUTOFORDER_late_fail',NULL,NULL,NULL,499.00,'INR','failure',NULL,NULL,NULL,'E000','Delayed bank timeout notification',0,0,'{\"txnid\":\"TCM_TEST_1792129228_OUTOFORDER\",\"udf5\":\"TCM-20261016-C8F0F8\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}','2026-10-16 11:10:28','2026-10-16 11:10:28'),(12,29,NULL,NULL,7,'TCM_TEST_1792129228_LEGIT_FAIL',NULL,NULL,NULL,149.00,'INR','failure',NULL,NULL,NULL,'E000','User cancelled payment on bank portal',0,0,'{\"txnid\":\"TCM_TEST_1792129228_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-50861D\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:10:28','2026-10-16 11:10:28'),(13,44,NULL,NULL,7,'TCM_TEST_1792129670_A','MIH_25f1f8d4','BANK_REF_12345',NULL,99.00,'INR','success','UPI','','',NULL,NULL,1,0,'{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129670_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_25f1f8d4\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-0C29DC\",\"hash\":\"4aae71936a8d4a4dc9d26e0330ec3f4e6bbce7ee61f5e6c97d11aba977c8b4f59e4624c96d2936ad55a5a8d032657b2fda055059f3e9a96344020fab0b116e45\"}','2026-10-16 11:17:50','2026-10-16 11:17:50'),(14,46,NULL,NULL,7,'TCM_TEST_1792129673_OUTOFORDER_late_fail',NULL,NULL,NULL,499.00,'INR','failure',NULL,NULL,NULL,'E000','Delayed bank timeout notification',0,0,'{\"txnid\":\"TCM_TEST_1792129673_OUTOFORDER\",\"udf5\":\"TCM-20261016-622CF9\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}','2026-10-16 11:17:53','2026-10-16 11:17:53'),(15,47,NULL,NULL,7,'TCM_TEST_1792129673_LEGIT_FAIL',NULL,NULL,NULL,149.00,'INR','failure',NULL,NULL,NULL,'E000','User cancelled payment on bank portal',0,0,'{\"txnid\":\"TCM_TEST_1792129673_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-96FF0F\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:17:53','2026-10-16 11:17:53'),(16,55,NULL,NULL,7,'TCM_TEST_1792130394_A','MIH_8aa4c536','BANK_REF_12345',NULL,99.00,'INR','success','UPI','','',NULL,NULL,1,0,'{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792130394_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_8aa4c536\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-AB6C20\",\"hash\":\"1e4e81803a47509eb23038da128760192b5e04f1772bd846edf02219adf5ea5a6eee13a471b57dadccde9ebfb46682b81a9c61542ad7e2aabda0bdd522549f39\"}','2026-10-16 11:29:54','2026-10-16 11:29:54'),(17,57,NULL,NULL,7,'TCM_TEST_1792130397_OUTOFORDER_late_fail',NULL,NULL,NULL,499.00,'INR','failure',NULL,NULL,NULL,'E000','Delayed bank timeout notification',0,0,'{\"txnid\":\"TCM_TEST_1792130397_OUTOFORDER\",\"udf5\":\"TCM-20261016-97D8D1\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}','2026-10-16 11:29:57','2026-10-16 11:29:57'),(18,58,NULL,NULL,7,'TCM_TEST_1792130397_LEGIT_FAIL',NULL,NULL,NULL,149.00,'INR','failure',NULL,NULL,NULL,'E000','User cancelled payment on bank portal',0,0,'{\"txnid\":\"TCM_TEST_1792130397_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-7A7FDE\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:29:57','2026-10-16 11:29:57'),(19,66,NULL,NULL,7,'TCM_TEST_1792131030_A','MIH_78e68bd9','BANK_REF_12345',NULL,99.00,'INR','success','UPI','','',NULL,NULL,1,0,'{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792131030_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_78e68bd9\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-6470A8\",\"hash\":\"dcbbf9e7a71b7c0f716b6aadc0b3768dd1e9af9164ffd2266311bdc39ba084e84bafd794d44cb19f5f4de69716654f3f1ea6d2c144be790ff116d7f6b0e39929\"}','2026-10-16 11:40:30','2026-10-16 11:40:30'),(20,68,NULL,NULL,7,'TCM_TEST_1792131032_OUTOFORDER_late_fail',NULL,NULL,NULL,499.00,'INR','failure',NULL,NULL,NULL,'E000','Delayed bank timeout notification',0,0,'{\"txnid\":\"TCM_TEST_1792131032_OUTOFORDER\",\"udf5\":\"TCM-20261016-404DB1\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}','2026-10-16 11:40:32','2026-10-16 11:40:32'),(21,69,NULL,NULL,7,'TCM_TEST_1792131032_LEGIT_FAIL',NULL,NULL,NULL,149.00,'INR','failure',NULL,NULL,NULL,'E000','User cancelled payment on bank portal',0,0,'{\"txnid\":\"TCM_TEST_1792131032_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-C134C7\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:40:32','2026-10-16 11:40:32'),(22,79,NULL,NULL,7,'TCM_TEST_1792131978_A','MIH_1c4b4379','BANK_REF_12345',NULL,99.00,'INR','success','UPI','','',NULL,NULL,1,0,'{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792131978_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_1c4b4379\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-3F4714\",\"hash\":\"f44a488ec39e0c7b344942d5caa064bf34685be9e415b3c38dd0c786037df41ac11fa6d3eae200a415d5d594fdfea344ac90e2d1f13870a32df3483b0d38903c\"}','2026-10-16 11:56:22','2026-10-16 11:56:22'),(23,81,NULL,NULL,7,'TCM_TEST_1792131983_OUTOFORDER_late_fail',NULL,NULL,NULL,499.00,'INR','failure',NULL,NULL,NULL,'E000','Delayed bank timeout notification',0,0,'{\"txnid\":\"TCM_TEST_1792131983_OUTOFORDER\",\"udf5\":\"TCM-20261016-40D769\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}','2026-10-16 11:56:23','2026-10-16 11:56:23'),(24,82,NULL,NULL,7,'TCM_TEST_1792131983_LEGIT_FAIL',NULL,NULL,NULL,149.00,'INR','failure',NULL,NULL,NULL,'E000','User cancelled payment on bank portal',0,0,'{\"txnid\":\"TCM_TEST_1792131983_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-8DF26B\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 11:56:23','2026-10-16 11:56:23'),(25,90,NULL,NULL,7,'TCM_TEST_1792132198_A','MIH_c8c45d8b','BANK_REF_12345',NULL,99.00,'INR','success','UPI','','',NULL,NULL,1,0,'{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792132198_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_c8c45d8b\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-EED4DB\",\"hash\":\"6befcdbf628df6161c0deda499179a7d67ed80d6018c3b0e7317e90aef99b394b3eca2206483e40c9d318a32abe4a287d2cf7e7ab5e02a5436109fddb455b9d1\"}','2026-10-16 11:59:59','2026-10-16 11:59:59'),(26,92,NULL,NULL,7,'TCM_TEST_1792132201_OUTOFORDER_late_fail',NULL,NULL,NULL,499.00,'INR','failure',NULL,NULL,NULL,'E000','Delayed bank timeout notification',0,0,'{\"txnid\":\"TCM_TEST_1792132201_OUTOFORDER\",\"udf5\":\"TCM-20261016-72A3E0\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}','2026-10-16 12:00:01','2026-10-16 12:00:01'),(27,93,NULL,NULL,7,'TCM_TEST_1792132201_LEGIT_FAIL',NULL,NULL,NULL,149.00,'INR','failure',NULL,NULL,NULL,'E000','User cancelled payment on bank portal',0,0,'{\"txnid\":\"TCM_TEST_1792132201_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-7E9184\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}','2026-10-16 12:00:01','2026-10-16 12:00:01');
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_achievements`
--

DROP TABLE IF EXISTS `portfolio_achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portfolio_achievements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(180) NOT NULL,
  `issuer` varchar(160) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `achieved_on` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pach_user` (`user_id`),
  CONSTRAINT `fk_pach_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_achievements`
--

LOCK TABLES `portfolio_achievements` WRITE;
/*!40000 ALTER TABLE `portfolio_achievements` DISABLE KEYS */;
/*!40000 ALTER TABLE `portfolio_achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_projects`
--

DROP TABLE IF EXISTS `portfolio_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portfolio_projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `tech_stack` varchar(255) DEFAULT NULL,
  `repo_url` varchar(255) DEFAULT NULL,
  `live_url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pproj_user` (`user_id`),
  CONSTRAINT `fk_pproj_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_projects`
--

LOCK TABLES `portfolio_projects` WRITE;
/*!40000 ALTER TABLE `portfolio_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `portfolio_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_skills`
--

DROP TABLE IF EXISTS `portfolio_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portfolio_skills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT 50,
  PRIMARY KEY (`id`),
  KEY `idx_pskill_user` (`user_id`),
  CONSTRAINT `fk_pskill_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_skills`
--

LOCK TABLES `portfolio_skills` WRITE;
/*!40000 ALTER TABLE `portfolio_skills` DISABLE KEYS */;
/*!40000 ALTER TABLE `portfolio_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_comments`
--

DROP TABLE IF EXISTS `post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `body` text NOT NULL,
  `status` enum('pending','approved','hidden') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post_comments_post_status` (`post_id`,`status`,`created_at`),
  KEY `idx_post_comments_status` (`status`),
  KEY `fk_post_comments_user` (`user_id`),
  CONSTRAINT `fk_post_comments_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_post_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_comments`
--

LOCK TABLES `post_comments` WRITE;
/*!40000 ALTER TABLE `post_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_reactions`
--

DROP TABLE IF EXISTS `post_reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_reactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `reaction` varchar(20) NOT NULL DEFAULT 'like',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_post_user_reaction` (`post_id`,`user_id`,`reaction`),
  KEY `idx_post_reactions_post` (`post_id`),
  KEY `fk_post_reactions_user` (`user_id`),
  CONSTRAINT `fk_post_reactions_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_post_reactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_reactions`
--

LOCK TABLES `post_reactions` WRITE;
/*!40000 ALTER TABLE `post_reactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_reactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `excerpt` varchar(300) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `category` varchar(80) DEFAULT 'General',
  `tags` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `views` int(11) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_posts_slug` (`slug`),
  KEY `idx_posts_status` (`status`),
  KEY `fk_post_author` (`author_id`),
  CONSTRAINT `fk_post_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (3,1,'What is the Current IT Industry Situation? A Realistic Guide for Tech Aspirants','what-is-the-current-it-industry-situation','Between mass hiring freezes, restructuring at legacy IT giants (TCS, Infosys, Wipro, HCL), and the rise of AI automation, the tech career landscape has fundamentally changed. Here is what is really happening—and how to turn this uncertainty into your biggest advantage.','If you open LinkedIn, Twitter (X), or tech forums today, the headlines can look terrifying: \"Mass hiring freeze across IT giants\", \"Over 200,000 layoffs across global tech\", \"Will AI replace all coders by 2026?\"\n\nFor thousands of college students, fresh graduates, and junior engineers preparing for software careers, the road ahead feels deeply confusing. For nearly twenty years, the formula in India was simple and repeatable: complete an engineering degree, memorize basic aptitude questions, crack campus mass recruitment for service giants like TCS, Infosys, Wipro, or HCL, sit in training on the bench, and slowly build a comfortable career.\n\nToday, that playbook is completely broken. But what is really happening behind the sensational headlines? Are software engineering jobs disappearing, or are they evolving into something completely different?\n\nLet us break down each signpost on the road today—and what it actually means for your career.\n\n## 1. The Mass Hiring Freeze: Why Did the \"Old Model\" Stop?\n\nTo understand the current slowdown at legacy IT companies, you have to understand their underlying business model. For two decades, Indian IT services grew through \"time and material\" (T&M) billing:\n- A US or European enterprise paid an Indian IT firm $25 to $50 per hour for every engineer assigned to maintain their software.\n- The IT firm hired 30,000 to 50,000 fresh graduates every single year at an entry package of ₹3.5 to ₹4 LPA.\n- Even if new hires sat on the \"bench\" for 4 to 6 months, client billing soon covered the overhead.\n\nStarting in late 2023 and continuing through 2025–2026, two massive shifts occurred simultaneously:\nFirst, Western corporate clients slashed discretionary IT budgets and insisted on fixed-fee contracts instead of endless hourly billing.\nSecond, companies began demanding immediate productivity. Clients no longer want to pay for 10 junior engineers to perform manual regression testing or write boilerplate CRUD code when a senior engineer using modern tooling can do the same work in half the time.\n\n> Key Takeaway: The mass hiring freeze is not a collapse of technology; it is the death of commodity headcount outsourcing. The industry has stopped hiring \"warm bodies\" to bill clients and is now hiring problem solvers who can deliver on day one.\n\n## 2. The Layoffs Reality: Who Is Being Affected?\n\nHeadlines frequently combine all tech layoffs into one bucket, creating unnecessary panic. When you examine the actual data across the IT industry, the cuts fall largely into three distinct categories:\n- **Middle Management & Coordination Roles:** Layers of project managers whose primary job was coordinating between clients and junior coders are being streamlined as agile AI tools automate task tracking, sprint management, and status reporting.\n- **Legacy & Outdated Tech Stacks:** Engineers who spent 8–10 years on proprietary legacy platforms, manual QA testing, or monolithic maintenance without ever learning cloud-native architectures, containerization, or modern APIs.\n- **Overhired Pandemic Surges:** Companies that hired recklessly during the 2021–2022 venture capital boom have right-sized their teams to focus on profitability and sustainable runway.\n\nNotice what is not being cut: core product engineers, full-stack developers who understand systems end-to-end, AI/data infrastructure engineers, and developers who can take a product from concept to deployment.\n\n## 3. AI Automation: Threat or Superpower?\n\nThe biggest question on every aspiring developer’s mind is: \"Will AI take away all coding jobs?\"\n\nLet us be completely transparent. Generative AI tools (ChatGPT, GitHub Copilot, Claude, Devin, Cursor) have permanently changed the nature of programming:\n- Boilerplate HTML/CSS layouts that used to take a junior developer 4 hours can now be generated in 30 seconds.\n- Basic CRUD endpoints, unit test skeletons, and regex patterns can be drafted almost instantly.\n- Code documentation and repetitive syntax translation between languages is largely automated.\n\nDoes this mean software engineers are obsolete? No. It means that syntax typists are obsolete.\n\nCoding was never just about typing `for` loops or memorizing syntax rules. Software engineering is about:\n- **System Architecture:** How does your database scale when 10,000 concurrent users checkout simultaneously?\n- **Business Domain Logic:** Understanding what the user actually needs and translating ambiguous real-world requirements into resilient software.\n- **Debugging & Edge Cases:** AI frequently hallucinates, introduces subtle race conditions, and misses crucial security vulnerabilities. Who finds and fixes those? An engineer who deeply understands the fundamentals.\n- **Security & Data Integrity:** Ensuring auth tokens, payment webhooks, database transactions, and user privacy are airtight.\n\n> Think of AI like the calculator in mathematics. The calculator did not kill mathematicians or accountants—it freed them from manual arithmetic so they could solve higher-level problems. In the same way, AI elevates you from a syntax typist to an architect.\n\n## 4. The Hidden Goldmine: Where the New Opportunities Actually Are\n\nWhile traditional campus recruitment numbers are muted, hiring in other sectors of the Indian and global tech ecosystem is experiencing an unprecedented boom:\n\n### A. Global Capability Centers (GCCs) in India\nOver 1,600 multinational corporations (including Goldman Sachs, JP Morgan, Walmart, Target, Boeing, and Airbus) have established massive GCCs in Bengaluru, Hyderabad, Pune, and NCR. Unlike outsourcing vendors, GCCs build core intellectual property and internal products. They hire engineers at packages ranging from ₹8 LPA to ₹25+ LPA for freshers and early-career talent.\n\n### B. High-Growth Product Startups & AI Integration\nEvery existing business—from fintech to logistics to healthcare—is scrambling to integrate AI into their workflows. They don\'t need theoretical PhD researchers; they need engineers who can connect LLM APIs, build vector search databases, set up RAG (Retrieval-Augmented Generation) pipelines, and ship fast web apps.\n\n### C. Full-Stack Production Engineers\nCompanies want engineers who can own the lifecycle: write clean frontend UI in modern frameworks, design efficient relational and NoSQL schemas, write secure backend APIs, containerize applications with Docker, and deploy to AWS or Cloudflare.\n\n## 5. Navigating the Uncertain Future: Your 4-Step Survival Blueprint\n\nIf you are a student or someone looking to switch into tech today, you cannot rely on the strategies of 2018. Here is the modern blueprint to make yourself irreplaceable:\n\n### 1. Build Production-Grade Projects, Not Tutorial Clones\nStop building basic To-Do apps, clone calculators, or static portfolio templates that look like everyone else\'s.\nInstead, build software that solves a real problem:\n- Include authentication (JWT, OAuth 2.0, session management).\n- Integrate third-party webhooks (Stripe, Razorpay, Twilio, SendGrid).\n- Use a production database with proper indexing, migrations, and relational integrity.\n- Deploy it live on a domain with SSL, CI/CD pipelines, and health monitoring.\nWhen an interviewer can open your live URL, inspect your GitHub commit history, and review your database schema, you have instantly separated yourself from 95% of candidates.\n\n### 2. Learn to Pair-Program with AI (Become a 10x Builder)\nDo not shy away from AI coding tools. Master them. Learn how to prompt them for architectural reviews, use them to generate mock datasets, test edge cases, and accelerate your boilerplate work. But always verify, benchmark, and understand every single line of code that enters your codebase.\n\n### 3. Master Computer Science Fundamentals\nFrameworks change every three years. Languages evolve. But the foundational pillars remain constant:\n- **Data Structures & Algorithms:** Space and time complexity, arrays, trees, graphs, dynamic programming patterns.\n- **Databases & Systems:** Relational modeling (PostgreSQL/MySQL), ACID properties, indexing, caching strategies (Redis), and message queues.\n- **Networking & Security:** HTTP/HTTPS lifecycle, CORS, REST, GraphQL, WebSocket bidirectional streaming, API security.\n\n### 4. Cultivate Clear Technical Communication\nIn a world where remote and hybrid work is the standard, engineers who can write clear documentation, explain architectural trade-offs, and communicate with stakeholders command the highest compensation. Participate in open-source discussions, write engineering notes, and practice explaining your code aloud.\n\n## The TCM One Perspective\n\nAt TCM One (The Code Munk), we observed this transformation early. We realized that traditional academic syllabi and generic coaching institutes were still teaching 10-year-old curriculum designed for a bygone era of IT services.\n\nThat is why our learning philosophy is built on three pillars:\n- **Zero Fluff, Real Production Engineering:** We teach students to build, break, and deploy real production applications using the exact tools, architectures, and workflows used by high-tier product engineering teams.\n- **AI-Native Engineering:** We teach you how to leverage AI tools responsibly to multiply your engineering output without sacrificing core conceptual mastery.\n- **Deep Defense & Code Walkthroughs:** We prepare you to defend your code, explain system design trade-offs, and succeed in modern rigorous technical interviews.\n\nThe IT industry is not dying—it is undergoing a massive, necessary evolution toward quality, skill, and engineering craftsmanship. Those who adapt now will build the most rewarding careers of the next decade.','assets/it-industry-situation-2026.jpg','Industry Insights','IT Industry, Layoffs, AI Automation, Tech Careers, Hiring 2026, Full Stack, Software Engineering','published',23,'2026-10-16 12:00:03','2026-10-15 19:39:49','2026-10-16 12:00:03');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_courses`
--

DROP TABLE IF EXISTS `program_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_courses` (
  `program_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`program_id`,`course_id`),
  KEY `fk_pc_course` (`course_id`),
  CONSTRAINT `fk_pc_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pc_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_courses`
--

LOCK TABLES `program_courses` WRITE;
/*!40000 ALTER TABLE `program_courses` DISABLE KEYS */;
INSERT INTO `program_courses` VALUES (1,1),(1,2),(1,4),(2,2),(2,3);
/*!40000 ALTER TABLE `program_courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program_enrollments`
--

DROP TABLE IF EXISTS `program_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_enrollments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `status` enum('pending','active','completed','cancelled') NOT NULL DEFAULT 'active',
  `enrolled_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_pe_user_program` (`user_id`,`program_id`),
  KEY `fk_pe_program` (`program_id`),
  CONSTRAINT `fk_pe_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pe_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `program_enrollments`
--

LOCK TABLES `program_enrollments` WRITE;
/*!40000 ALTER TABLE `program_enrollments` DISABLE KEYS */;
/*!40000 ALTER TABLE `program_enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `programs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(180) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` enum('live_classes','learning_track','internship','summer_campus','bootcamp','bundle') NOT NULL DEFAULT 'live_classes',
  `icon` varchar(60) DEFAULT 'bi-stack',
  `thumbnail` varchar(255) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `mode` enum('online','offline','hybrid') NOT NULL DEFAULT 'online',
  `duration` varchar(60) DEFAULT NULL,
  `schedule` varchar(160) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `original_price` decimal(10,2) DEFAULT NULL,
  `total_seats` int(11) NOT NULL DEFAULT 0,
  `seats_left` int(11) NOT NULL DEFAULT 0,
  `highlights` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_programs_slug` (`slug`),
  KEY `idx_programs_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `programs`
--

LOCK TABLES `programs` WRITE;
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
INSERT INTO `programs` VALUES (1,'TCM Live Classes — Full Stack','tcm-live-classes-full-stack','Daily live online classes with mentors','Learn full stack development through daily live online classes, code-alongs and weekly project reviews with industry mentors.','live_classes','bi-broadcast',NULL,'beginner','online','4 Months','Mon–Fri, 7:00–8:30 PM',14999.00,24999.00,40,12,'Daily live sessions\n10+ real projects\n1:1 mentorship\nDoubt support on WhatsApp\nCompletion certificate','2026-07-01','2026-10-31',1,'published','2026-10-13 15:04:10','2026-10-13 15:04:10'),(2,'TCM Learning Track — Frontend','tcm-learning-track-frontend','Self-paced track with live doubt sessions','A structured self-paced learning track combining recorded courses with weekly live doubt-clearing sessions.','learning_track','bi-mortarboard',NULL,'beginner','hybrid','3 Months','Weekly live doubts (Sat)',7999.00,12999.00,100,64,'Recorded course library\nWeekly live doubts\nProject feedback\nCertificate','2026-07-05','2026-10-05',1,'published','2026-10-13 15:04:10','2026-10-13 15:04:10'),(3,'Summer Campus 2026','summer-campus-2026','6-week intensive on-campus + online bootcamp','An intensive summer program for college students: build, ship and present a capstone project in 6 weeks.','summer_campus','bi-sun',NULL,'beginner','hybrid','6 Weeks','Mon–Sat, 10 AM–1 PM',9999.00,15999.00,60,22,'Capstone project\nTeam-based learning\nIndustry guest talks\nCertificate + LOR','2026-05-15','2026-06-30',1,'published','2026-10-13 15:04:10','2026-10-13 15:04:10'),(4,'TCM Internship Program','tcm-internship-program','Work on real client projects with Cuboidsoft','A guided internship where you contribute to real projects in partnership with Cuboidsoft and earn an experience certificate.','internship','bi-briefcase',NULL,'intermediate','online','3 Months','Flexible, 20 hrs/week',0.00,NULL,20,5,'Real client projects\nMentor guidance\nExperience certificate\nLetter of recommendation\nStipend for top performers','2026-07-15','2026-10-15',1,'published','2026-10-13 15:04:10','2026-10-13 15:04:10');
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotional_offers`
--

DROP TABLE IF EXISTS `promotional_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promotional_offers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_type` enum('course','workshop','global') NOT NULL DEFAULT 'course',
  `item_id` bigint(20) unsigned DEFAULT NULL,
  `offer_title` varchar(190) NOT NULL,
  `offer_code` varchar(60) DEFAULT NULL,
  `discount_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `original_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `banner_text` varchar(255) DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_offers_active` (`is_active`,`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotional_offers`
--

LOCK TABLES `promotional_offers` WRITE;
/*!40000 ALTER TABLE `promotional_offers` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotional_offers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `key` varchar(120) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('contact_email','hello@thecodemunk.com'),('contact_phone','+91-00000-00000'),('courses_count','24'),('currency','INR'),('installment_enabled','1'),('installment_interest_rate','0'),('installment_interval_days','15'),('installment_max_installments','3'),('installment_min_amount','5000.00'),('installment_min_upfront_pct','40'),('installment_plans_available','2,3'),('installment_reminder_email_enabled','1'),('site_name','The Code Munk'),('site_tagline','Learn Coding With Confidence'),('students_count','1200'),('whatsapp_message','Hi The Code Munk! I am interested in'),('whatsapp_number','919999999999');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_profiles`
--

DROP TABLE IF EXISTS `student_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_profiles` (
  `user_id` bigint(20) unsigned NOT NULL,
  `headline` varchar(160) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(120) DEFAULT NULL,
  `college` varchar(160) DEFAULT NULL,
  `graduation_year` smallint(6) DEFAULT NULL,
  `experience_level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `goal` varchar(190) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_profiles`
--

LOCK TABLES `student_profiles` WRITE;
/*!40000 ALTER TABLE `student_profiles` DISABLE KEYS */;
INSERT INTO `student_profiles` VALUES (2,'Aspiring Full Stack Developer','College student passionate about building web apps and learning new tech.','Lucknow, India','Integral University',2027,'intermediate','Land a frontend developer internship','https://github.com/aarav','https://linkedin.com/in/aarav',NULL,NULL,'2026-10-13 15:04:09','2026-10-13 15:04:09'),(3,'TCM Student',NULL,NULL,NULL,NULL,'beginner',NULL,NULL,NULL,NULL,NULL,'2026-10-16 00:55:03','2026-10-16 00:55:03'),(4,'TCM Student',NULL,NULL,NULL,NULL,'beginner',NULL,NULL,NULL,NULL,NULL,'2026-10-16 01:21:24','2026-10-16 01:21:24'),(5,'TCM Student',NULL,NULL,NULL,NULL,'beginner',NULL,NULL,NULL,NULL,NULL,'2026-10-16 01:21:38','2026-10-16 01:21:38'),(8,'TCM Student',NULL,NULL,NULL,NULL,'beginner',NULL,NULL,NULL,NULL,NULL,'2026-10-16 11:07:32','2026-10-16 11:07:32');
/*!40000 ALTER TABLE `student_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `role` varchar(120) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'Riya Verma','Frontend Developer @ Startup','TCM helped me go from zero to building real projects. The live classes and mentorship made all the difference.',5,NULL,'active',1,'2026-10-13 15:04:10'),(2,'Karan Singh','CS Student','The DSA program got me interview-ready. I cleared two coding rounds within a month of finishing.',5,NULL,'active',2,'2026-10-13 15:04:10'),(3,'Priya Nair','Career Switcher','I moved from a non-IT job into tech thanks to the Career Transition Program. Highly recommend.',5,NULL,'active',3,'2026-10-13 15:04:10');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','suspended','pending') NOT NULL DEFAULT 'active',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `onboarded` tinyint(1) NOT NULL DEFAULT 0,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'TCM Admin','admin@thecodemunk.com','$2y$12$iCPmgLf8zY/sA/Yo4eEfeugvqcj1nHcE3Ht6qK1GPS/AkIY6AVT4u','admin',NULL,NULL,'active',1,1,NULL,'2026-10-13 15:04:09','2026-10-13 15:04:09'),(2,'Aarav Sharma','student@thecodemunk.com','$2y$10$aSWPanJl5ksZBC73K1Zk1.WGzSOJX/IGJgP6A544kZ1eF3rzIErCm','student',NULL,NULL,'active',1,1,'2026-10-15 20:25:11','2026-10-13 15:04:09','2026-10-15 20:25:11'),(3,'Ayushman Ayushman','cuboidsoft@gmail.com','$2y$10$05CBzzD6IIX0iK.sHtgE2Oy/1yRRVboUTiVvwZI5d3/oMjm4cVd.q','student',NULL,NULL,'active',1,0,NULL,'2026-10-16 00:55:03','2026-10-16 00:55:03'),(4,'Test Learner','testlearner@example.com','$2y$10$W0SuCJZiVsKrLFp6KYNr4eDG2BwtWQkt25giDX3Tkg3u2uqvdbMrK','student',NULL,NULL,'active',1,0,NULL,'2026-10-16 01:21:24','2026-10-16 01:21:24'),(5,'Free Learner','freelearner@example.com','$2y$10$smjL3p6xAOPhXmYcFhvU3elbLJAjRnrtnRjLtrQKZ5ZSwQCBDs0BG','student',NULL,NULL,'active',1,0,NULL,'2026-10-16 01:21:38','2026-10-16 01:21:38'),(7,'Test PayU Student','test_student_payu@thecodemunk.in','dummy','student',NULL,NULL,'active',1,0,NULL,'2026-10-16 10:47:26','2026-10-16 10:47:26'),(8,'The Code Munk','thecodemunk@gmail.com','$2y$10$WkxRpuSyJvjKrnwKj.YNJOCLl/RGNcxlyqFuhyc7Q.8dNBnNRXGn2','student',NULL,NULL,'active',1,0,NULL,'2026-10-16 11:07:32','2026-10-16 11:07:32'),(9,'Test Installment Student','test_inst_student@thecodemunk.in','dummy','student',NULL,NULL,'active',1,0,NULL,'2026-10-16 11:12:00','2026-10-16 11:12:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhook_audit_logs`
--

DROP TABLE IF EXISTS `webhook_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhook_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `http_method` varchar(10) NOT NULL DEFAULT 'POST',
  `ip_address` varchar(45) DEFAULT NULL,
  `headers` text DEFAULT NULL,
  `raw_payload` longtext DEFAULT NULL,
  `response_status` int(11) NOT NULL DEFAULT 200,
  `verification_status` enum('verified','hash_mismatch','api_mismatch','invalid_order','error') NOT NULL DEFAULT 'verified',
  `error_details` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_wal_event` (`event_type`),
  KEY `idx_wal_status` (`verification_status`),
  KEY `idx_wal_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhook_audit_logs`
--

LOCK TABLES `webhook_audit_logs` WRITE;
/*!40000 ALTER TABLE `webhook_audit_logs` DISABLE KEYS */;
INSERT INTO `webhook_audit_logs` VALUES (9,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TEST_FORGED_002\",\"amount\":\"99.00\",\"productinfo\":\"Test\",\"firstname\":\"Test\",\"email\":\"forged@test.com\",\"status\":\"success\",\"hash\":\"deadbeef1234567890abcdef\"}',400,'hash_mismatch','Cryptographic reverse hash verification failed. Provided hash: deadbeef1234567890abcdef','2026-10-16 10:47:26'),(10,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792127846_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_5684f27d\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-C76F38\",\"hash\":\"90ca8f19d7b9a0ed5a8c252c16f1df2c554a12813baedbd5e25d200b8a853799df2188b4b6895ab0d4bf1a69d2a9cd1d2a2bca682fdc4a4ef52c8c8a7060e08c\"}',200,'verified','Payment successfully confirmed for order #12. Receipt: TCM-REC-202610-00012','2026-10-16 10:47:26'),(11,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792127849_IDEMP\",\"amount\":\"199.00\",\"productinfo\":\"Idempotency Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_EXISTING\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-7B0C57\",\"hash\":\"de6abd08abc9a724a469d62e614b7d979157ac21a50f51b1d0a317c1c85637a85cf04dcf032a605ff5cd7f156c5ded3f4e62b06abbdb0b00745ecb7848d60a2d\"}',200,'verified','Idempotent: Order #13 (TCM-20261016-7B0C57) is already marked as paid. Duplicate webhook acknowledged.','2026-10-16 10:47:29'),(12,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792127849_OUTOFORDER\",\"udf5\":\"TCM-20261016-9F2A68\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}',200,'verified','Out-of-order protection: Received late failure webhook for already PAID order #14. Paid state preserved.','2026-10-16 10:47:29'),(13,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792127849_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-56B3A2\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}',200,'verified','Recorded payment failure for order #15: User cancelled payment on bank portal (Code: E000)','2026-10-16 10:47:29'),(14,'refund','/api/webhooks/payu/refund','POST','127.0.0.1','[]','{\"txnid\":\"TCM_REF_1792127849_TXN\",\"payu_refund_id\":\"PAYU_REF_ff669bec\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}',200,'verified','Recorded refund of ₹1000 for order #17','2026-10-16 10:47:29'),(15,'dispute','/api/webhooks/payu/dispute','POST','127.0.0.1','[]','{\"txnid\":\"TCM_DISP_1792127849_TXN\",\"dispute_id\":\"DISP_b68c593c\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}',200,'verified','Recorded payment dispute for order #18. Dispute ID: DISP_b68c593c','2026-10-16 10:47:29'),(16,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TEST_FORGED_002\",\"amount\":\"99.00\",\"productinfo\":\"Test\",\"firstname\":\"Test\",\"email\":\"forged@test.com\",\"status\":\"success\",\"hash\":\"deadbeef1234567890abcdef\"}',400,'hash_mismatch','Cryptographic reverse hash verification failed. Provided hash: deadbeef1234567890abcdef','2026-10-16 11:09:41'),(17,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129181_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_f0a15d4a\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-8064F0\",\"hash\":\"6824b399bdebf4f8e0c30224af51bc9a5a6e660977cc27a80338ef1669c520d1d9a322facd6a7055a40633b9ce452e31652528892b00e8709db8e2d4d616293b\"}',200,'verified','Payment successfully confirmed for order #21. Receipt: TCM-REC-202610-00021','2026-10-16 11:09:41'),(18,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129183_IDEMP\",\"amount\":\"199.00\",\"productinfo\":\"Idempotency Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_EXISTING\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-531513\",\"hash\":\"259a17ac98642449bd1d38d875c7ac723232209480d937c5bf5299f536fe6b9c3359dc95c970ea24eeda2ba79a655a256600c350fce07dc585ec85e5d841d7cf\"}',200,'verified','Idempotent: Order #22 (TCM-20261016-531513) is already marked as paid. Duplicate webhook acknowledged.','2026-10-16 11:09:43'),(19,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792129183_OUTOFORDER\",\"udf5\":\"TCM-20261016-C434B3\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}',200,'verified','Out-of-order protection: Received late failure webhook for already PAID order #23. Paid state preserved.','2026-10-16 11:09:43'),(20,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792129183_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-B7845D\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}',200,'verified','Recorded payment failure for order #24: User cancelled payment on bank portal (Code: E000)','2026-10-16 11:09:43'),(21,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TEST_FORGED_002\",\"amount\":\"99.00\",\"productinfo\":\"Test\",\"firstname\":\"Test\",\"email\":\"forged@test.com\",\"status\":\"success\",\"hash\":\"deadbeef1234567890abcdef\"}',400,'hash_mismatch','Cryptographic reverse hash verification failed. Provided hash: deadbeef1234567890abcdef','2026-10-16 11:10:25'),(22,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129225_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_331d77b8\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-6E9C24\",\"hash\":\"f55d413671c7ad42b9bd63b8dd4f8b4a150ed3e2357667ba744f17836a69f0c3e99587ced60d0c4d66db60cfc03b547130663c02ba7f9a59405edef8f93ebce0\"}',200,'verified','Payment successfully confirmed for order #26. Receipt: TCM-REC-202610-00026','2026-10-16 11:10:25'),(23,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129228_IDEMP\",\"amount\":\"199.00\",\"productinfo\":\"Idempotency Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_EXISTING\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-36A380\",\"hash\":\"b920abfbf835af4de7c5b59cfa28bf7885cdc153e4f8cf9caa74bf4b2b235e10b3510ddb6d9fe5a5639ea637cff709cad9c43ed44987d90a8c57be8e13ee9a14\"}',200,'verified','Idempotent: Order #27 (TCM-20261016-36A380) is already marked as paid. Duplicate webhook acknowledged.','2026-10-16 11:10:28'),(24,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792129228_OUTOFORDER\",\"udf5\":\"TCM-20261016-C8F0F8\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}',200,'verified','Out-of-order protection: Received late failure webhook for already PAID order #28. Paid state preserved.','2026-10-16 11:10:28'),(25,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792129228_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-50861D\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}',200,'verified','Recorded payment failure for order #29: User cancelled payment on bank portal (Code: E000)','2026-10-16 11:10:28'),(26,'refund','/api/webhooks/payu/refund','POST','127.0.0.1','[]','{\"txnid\":\"TCM_REF_1792129228_TXN\",\"payu_refund_id\":\"PAYU_REF_384e70c1\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}',200,'verified','Recorded refund of ₹1000 for order #31','2026-10-16 11:10:28'),(27,'dispute','/api/webhooks/payu/dispute','POST','127.0.0.1','[]','{\"txnid\":\"TCM_DISP_1792129228_TXN\",\"dispute_id\":\"DISP_99037ec9\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}',200,'verified','Recorded payment dispute for order #32. Dispute ID: DISP_99037ec9','2026-10-16 11:10:28'),(28,'installment_rescheduled','/admin/installments/reschedule','POST','127.0.0.1',NULL,'{\"schedule_id\":26,\"old_due\":\"2026-10-26\",\"new_due\":\"2026-11-10\",\"reason\":\"Student requested exam extension\",\"admin_id\":1}',200,'verified','Admin #1 rescheduled milestone #26 from 2026-10-26 to 2026-11-10','2026-10-16 11:15:58'),(29,'installment_rescheduled','/admin/installments/reschedule','POST','127.0.0.1',NULL,'{\"schedule_id\":35,\"old_due\":\"2026-10-26\",\"new_due\":\"2026-11-10\",\"reason\":\"Student requested exam extension\",\"admin_id\":1}',200,'verified','Admin #1 rescheduled milestone #35 from 2026-10-26 to 2026-11-10','2026-10-16 11:17:15'),(30,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TEST_FORGED_002\",\"amount\":\"99.00\",\"productinfo\":\"Test\",\"firstname\":\"Test\",\"email\":\"forged@test.com\",\"status\":\"success\",\"hash\":\"deadbeef1234567890abcdef\"}',400,'hash_mismatch','Cryptographic reverse hash verification failed. Provided hash: deadbeef1234567890abcdef','2026-10-16 11:17:50'),(31,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129670_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_25f1f8d4\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-0C29DC\",\"hash\":\"4aae71936a8d4a4dc9d26e0330ec3f4e6bbce7ee61f5e6c97d11aba977c8b4f59e4624c96d2936ad55a5a8d032657b2fda055059f3e9a96344020fab0b116e45\"}',200,'verified','Payment successfully confirmed for order #44. Receipt: TCM-REC-202610-00044','2026-10-16 11:17:50'),(32,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792129673_IDEMP\",\"amount\":\"199.00\",\"productinfo\":\"Idempotency Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_EXISTING\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-FED0E3\",\"hash\":\"ce8596f833fb612d54eb825b85c888d594c512c9b39390f567d879c1d1ba9c2148c04c83463724591074245db7776beae4911ccd2ff110e82ede7082dd187c30\"}',200,'verified','Idempotent: Order #45 (TCM-20261016-FED0E3) is already marked as paid. Duplicate webhook acknowledged.','2026-10-16 11:17:53'),(33,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792129673_OUTOFORDER\",\"udf5\":\"TCM-20261016-622CF9\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}',200,'verified','Out-of-order protection: Received late failure webhook for already PAID order #46. Paid state preserved.','2026-10-16 11:17:53'),(34,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792129673_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-96FF0F\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}',200,'verified','Recorded payment failure for order #47: User cancelled payment on bank portal (Code: E000)','2026-10-16 11:17:53'),(35,'refund','/api/webhooks/payu/refund','POST','127.0.0.1','[]','{\"txnid\":\"TCM_REF_1792129673_TXN\",\"payu_refund_id\":\"PAYU_REF_73cf8a83\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}',200,'verified','Recorded refund of ₹1000 for order #49','2026-10-16 11:17:53'),(36,'dispute','/api/webhooks/payu/dispute','POST','127.0.0.1','[]','{\"txnid\":\"TCM_DISP_1792129673_TXN\",\"dispute_id\":\"DISP_4d4b1b6f\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}',200,'verified','Recorded payment dispute for order #50. Dispute ID: DISP_4d4b1b6f','2026-10-16 11:17:53'),(37,'installment_rescheduled','/admin/installments/reschedule','POST','127.0.0.1',NULL,'{\"schedule_id\":46,\"old_due\":\"2026-10-26\",\"new_due\":\"2026-11-10\",\"reason\":\"Student requested exam extension\",\"admin_id\":1}',200,'verified','Admin #1 rescheduled milestone #46 from 2026-10-26 to 2026-11-10','2026-10-16 11:29:41'),(38,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TEST_FORGED_002\",\"amount\":\"99.00\",\"productinfo\":\"Test\",\"firstname\":\"Test\",\"email\":\"forged@test.com\",\"status\":\"success\",\"hash\":\"deadbeef1234567890abcdef\"}',400,'hash_mismatch','Cryptographic reverse hash verification failed. Provided hash: deadbeef1234567890abcdef','2026-10-16 11:29:54'),(39,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792130394_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_8aa4c536\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-AB6C20\",\"hash\":\"1e4e81803a47509eb23038da128760192b5e04f1772bd846edf02219adf5ea5a6eee13a471b57dadccde9ebfb46682b81a9c61542ad7e2aabda0bdd522549f39\"}',200,'verified','Payment successfully confirmed for order #55. Receipt: TCM-REC-202610-00055','2026-10-16 11:29:54'),(40,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792130397_IDEMP\",\"amount\":\"199.00\",\"productinfo\":\"Idempotency Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_EXISTING\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-4EDE27\",\"hash\":\"8f1a3d8a7bb6c8a85fe040b8f92dd8256064780a869e7d69ae288b54f21ee220659f12d2722778d73a1e47444ec863d340d8acdc0eae80f7e1162229311a8135\"}',200,'verified','Idempotent: Order #56 (TCM-20261016-4EDE27) is already marked as paid. Duplicate webhook acknowledged.','2026-10-16 11:29:57'),(41,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792130397_OUTOFORDER\",\"udf5\":\"TCM-20261016-97D8D1\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}',200,'verified','Out-of-order protection: Received late failure webhook for already PAID order #57. Paid state preserved.','2026-10-16 11:29:57'),(42,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792130397_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-7A7FDE\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}',200,'verified','Recorded payment failure for order #58: User cancelled payment on bank portal (Code: E000)','2026-10-16 11:29:57'),(43,'refund','/api/webhooks/payu/refund','POST','127.0.0.1','[]','{\"txnid\":\"TCM_REF_1792130397_TXN\",\"payu_refund_id\":\"PAYU_REF_97e5206a\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}',200,'verified','Recorded refund of ₹1000 for order #60','2026-10-16 11:29:57'),(44,'dispute','/api/webhooks/payu/dispute','POST','127.0.0.1','[]','{\"txnid\":\"TCM_DISP_1792130397_TXN\",\"dispute_id\":\"DISP_bba3c98f\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}',200,'verified','Recorded payment dispute for order #61. Dispute ID: DISP_bba3c98f','2026-10-16 11:29:57'),(45,'installment_rescheduled','/admin/installments/reschedule','POST','127.0.0.1',NULL,'{\"schedule_id\":57,\"old_due\":\"2026-10-26\",\"new_due\":\"2026-11-10\",\"reason\":\"Student requested exam extension\",\"admin_id\":1}',200,'verified','Admin #1 rescheduled milestone #57 from 2026-10-26 to 2026-11-10','2026-10-16 11:40:14'),(46,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TEST_FORGED_002\",\"amount\":\"99.00\",\"productinfo\":\"Test\",\"firstname\":\"Test\",\"email\":\"forged@test.com\",\"status\":\"success\",\"hash\":\"deadbeef1234567890abcdef\"}',400,'hash_mismatch','Cryptographic reverse hash verification failed. Provided hash: deadbeef1234567890abcdef','2026-10-16 11:40:30'),(47,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792131030_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_78e68bd9\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-6470A8\",\"hash\":\"dcbbf9e7a71b7c0f716b6aadc0b3768dd1e9af9164ffd2266311bdc39ba084e84bafd794d44cb19f5f4de69716654f3f1ea6d2c144be790ff116d7f6b0e39929\"}',200,'verified','Payment successfully confirmed for order #66. Receipt: TCM-REC-202610-00066','2026-10-16 11:40:30'),(48,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792131032_IDEMP\",\"amount\":\"199.00\",\"productinfo\":\"Idempotency Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_EXISTING\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-F7B9DA\",\"hash\":\"6621c66cc4eb950272980390f531068bc92567f7c932f024966b9adf7287ba9acd4012825719eb8ff2340b91241e0188d1e8dea3ab8e8053e1a20d1b80fa7b74\"}',200,'verified','Idempotent: Order #67 (TCM-20261016-F7B9DA) is already marked as paid. Duplicate webhook acknowledged.','2026-10-16 11:40:32'),(49,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792131032_OUTOFORDER\",\"udf5\":\"TCM-20261016-404DB1\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}',200,'verified','Out-of-order protection: Received late failure webhook for already PAID order #68. Paid state preserved.','2026-10-16 11:40:32'),(50,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792131032_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-C134C7\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}',200,'verified','Recorded payment failure for order #69: User cancelled payment on bank portal (Code: E000)','2026-10-16 11:40:32'),(51,'refund','/api/webhooks/payu/refund','POST','127.0.0.1','[]','{\"txnid\":\"TCM_REF_1792131032_TXN\",\"payu_refund_id\":\"PAYU_REF_5516ec21\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}',200,'verified','Recorded refund of ₹1000 for order #71','2026-10-16 11:40:32'),(52,'dispute','/api/webhooks/payu/dispute','POST','127.0.0.1','[]','{\"txnid\":\"TCM_DISP_1792131032_TXN\",\"dispute_id\":\"DISP_4634c831\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}',200,'verified','Recorded payment dispute for order #72. Dispute ID: DISP_4634c831','2026-10-16 11:40:32'),(53,'installment_rescheduled','/admin/installments/reschedule','POST','127.0.0.1',NULL,'{\"schedule_id\":70,\"old_due\":\"2026-10-26\",\"new_due\":\"2026-11-10\",\"reason\":\"Student requested exam extension\",\"admin_id\":1}',200,'verified','Admin #1 rescheduled milestone #70 from 2026-10-26 to 2026-11-10','2026-10-16 11:56:04'),(54,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TEST_FORGED_002\",\"amount\":\"99.00\",\"productinfo\":\"Test\",\"firstname\":\"Test\",\"email\":\"forged@test.com\",\"status\":\"success\",\"hash\":\"deadbeef1234567890abcdef\"}',400,'hash_mismatch','Cryptographic reverse hash verification failed. Provided hash: deadbeef1234567890abcdef','2026-10-16 11:56:18'),(55,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792131978_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_1c4b4379\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-3F4714\",\"hash\":\"f44a488ec39e0c7b344942d5caa064bf34685be9e415b3c38dd0c786037df41ac11fa6d3eae200a415d5d594fdfea344ac90e2d1f13870a32df3483b0d38903c\"}',200,'verified','Payment successfully confirmed for order #79. Receipt: TCM-REC-202610-00079','2026-10-16 11:56:22'),(56,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792131983_IDEMP\",\"amount\":\"199.00\",\"productinfo\":\"Idempotency Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_EXISTING\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-92E249\",\"hash\":\"13312f660ac6028fa2b5694846d4270351836fa18ba4c65930176e17ceaeb449c1583f907b1216a5ee946efc8d78a44123e7f81ff5f81b606f60872ea811c422\"}',200,'verified','Idempotent: Order #80 (TCM-20261016-92E249) is already marked as paid. Duplicate webhook acknowledged.','2026-10-16 11:56:23'),(57,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792131983_OUTOFORDER\",\"udf5\":\"TCM-20261016-40D769\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}',200,'verified','Out-of-order protection: Received late failure webhook for already PAID order #81. Paid state preserved.','2026-10-16 11:56:23'),(58,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792131983_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-8DF26B\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}',200,'verified','Recorded payment failure for order #82: User cancelled payment on bank portal (Code: E000)','2026-10-16 11:56:23'),(59,'refund','/api/webhooks/payu/refund','POST','127.0.0.1','[]','{\"txnid\":\"TCM_REF_1792131983_TXN\",\"payu_refund_id\":\"PAYU_REF_a8ba1ebf\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}',200,'verified','Recorded refund of ₹1000 for order #84','2026-10-16 11:56:24'),(60,'dispute','/api/webhooks/payu/dispute','POST','127.0.0.1','[]','{\"txnid\":\"TCM_DISP_1792131984_TXN\",\"dispute_id\":\"DISP_dbef6732\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}',200,'verified','Recorded payment dispute for order #85. Dispute ID: DISP_dbef6732','2026-10-16 11:56:24'),(61,'installment_rescheduled','/admin/installments/reschedule','POST','127.0.0.1',NULL,'{\"schedule_id\":81,\"old_due\":\"2026-10-26\",\"new_due\":\"2026-11-10\",\"reason\":\"Student requested exam extension\",\"admin_id\":1}',200,'verified','Admin #1 rescheduled milestone #81 from 2026-10-26 to 2026-11-10','2026-10-16 11:59:54'),(62,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TEST_FORGED_002\",\"amount\":\"99.00\",\"productinfo\":\"Test\",\"firstname\":\"Test\",\"email\":\"forged@test.com\",\"status\":\"success\",\"hash\":\"deadbeef1234567890abcdef\"}',400,'hash_mismatch','Cryptographic reverse hash verification failed. Provided hash: deadbeef1234567890abcdef','2026-10-16 11:59:58'),(63,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792132198_A\",\"amount\":\"99.00\",\"productinfo\":\"Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_c8c45d8b\",\"bank_ref_num\":\"BANK_REF_12345\",\"mode\":\"UPI\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-EED4DB\",\"hash\":\"6befcdbf628df6161c0deda499179a7d67ed80d6018c3b0e7317e90aef99b394b3eca2206483e40c9d318a32abe4a287d2cf7e7ab5e02a5436109fddb455b9d1\"}',200,'verified','Payment successfully confirmed for order #90. Receipt: TCM-REC-202610-00090','2026-10-16 11:59:59'),(64,'payment_success','/api/webhooks/payu/success','POST','127.0.0.1','[]','{\"key\":\"dTzDIh\",\"txnid\":\"TCM_TEST_1792132201_IDEMP\",\"amount\":\"199.00\",\"productinfo\":\"Idempotency Test Course\",\"firstname\":\"Student\",\"email\":\"test_student_payu@thecodemunk.in\",\"status\":\"success\",\"mihpayid\":\"MIH_EXISTING\",\"udf1\":\"course\",\"udf2\":\"13\",\"udf3\":\"\",\"udf4\":\"7\",\"udf5\":\"TCM-20261016-848FB0\",\"hash\":\"0a0428152cc8ccf7caa1411845810baf945b62ba09f41514a125d97de78c1f3139a37e2579cedf2182f05110a7f861c07d9523fe6b1a5aa81edbd1df0bbf6fff\"}',200,'verified','Idempotent: Order #91 (TCM-20261016-848FB0) is already marked as paid. Duplicate webhook acknowledged.','2026-10-16 12:00:01'),(65,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792132201_OUTOFORDER\",\"udf5\":\"TCM-20261016-72A3E0\",\"status\":\"failure\",\"error_Message\":\"Delayed bank timeout notification\"}',200,'verified','Out-of-order protection: Received late failure webhook for already PAID order #92. Paid state preserved.','2026-10-16 12:00:01'),(66,'payment_failed','/api/webhooks/payu/failed','POST','127.0.0.1','[]','{\"txnid\":\"TCM_TEST_1792132201_LEGIT_FAIL\",\"udf5\":\"TCM-20261016-7E9184\",\"status\":\"failure\",\"error_Message\":\"User cancelled payment on bank portal\"}',200,'verified','Recorded payment failure for order #93: User cancelled payment on bank portal (Code: E000)','2026-10-16 12:00:01'),(67,'refund','/api/webhooks/payu/refund','POST','127.0.0.1','[]','{\"txnid\":\"TCM_REF_1792132201_TXN\",\"payu_refund_id\":\"PAYU_REF_afd79be0\",\"amount\":\"1000.00\",\"status\":\"success\",\"reason\":\"Student requested cancellation within policy\"}',200,'verified','Recorded refund of ₹1000 for order #95','2026-10-16 12:00:01'),(68,'dispute','/api/webhooks/payu/dispute','POST','127.0.0.1','[]','{\"txnid\":\"TCM_DISP_1792132201_TXN\",\"dispute_id\":\"DISP_35114c6b\",\"dispute_type\":\"chargeback\",\"dispute_status\":\"open\",\"amount\":\"2500.00\",\"reason\":\"Cardholder unrecognized transaction\",\"due_date\":\"2026-10-23\"}',200,'verified','Recorded payment dispute for order #96. Dispute ID: DISP_35114c6b','2026-10-16 12:00:01');
/*!40000 ALTER TABLE `webhook_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workshop_leads`
--

DROP TABLE IF EXISTS `workshop_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workshop_leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `workshop_id` bigint(20) unsigned DEFAULT NULL,
  `workshop_slug` varchar(190) NOT NULL,
  `workshop_title` varchar(220) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `current_status` enum('student','working','other') NOT NULL DEFAULT 'student',
  `college` varchar(190) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `learning_goal` text DEFAULT NULL,
  `selected_workshop_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `lead_status` enum('new','contacted','interested','payment_pending','enrolled','dropped','not_interested','lost') NOT NULL DEFAULT 'new',
  `payment_status` enum('not_initiated','pending','paid','failed','refunded') NOT NULL DEFAULT 'not_initiated',
  `enrollment_status` enum('pending','confirmed','waitlist','cancelled') NOT NULL DEFAULT 'pending',
  `utm_source` varchar(120) DEFAULT NULL,
  `utm_medium` varchar(120) DEFAULT NULL,
  `utm_campaign` varchar(120) DEFAULT NULL,
  `utm_content` varchar(120) DEFAULT NULL,
  `landing_page` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `consent_timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ws_leads_status` (`lead_status`),
  KEY `idx_ws_leads_pay_status` (`payment_status`),
  KEY `idx_ws_leads_slug` (`workshop_slug`),
  KEY `idx_ws_leads_email` (`email`),
  KEY `idx_ws_leads_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workshop_leads`
--

LOCK TABLES `workshop_leads` WRITE;
/*!40000 ALTER TABLE `workshop_leads` DISABLE KEYS */;
INSERT INTO `workshop_leads` VALUES (2,10,'python-starter-workshop','Python Starter Workshop','Ayushman Ayushman','cuboidsoft@gmail.com','09238695500','student','','','',99.00,'new','not_initiated','pending','','','','','/tcm/','http://localhost/tcm/',NULL,'2026-10-16 00:37:26','2026-10-16 00:37:26','2026-10-16 00:37:26'),(3,10,'python-starter-workshop','Python Starter Workshop','Test Learner','testlearner@example.com','9876543210','student','','','',99.00,'new','not_initiated','pending','','','','','','',NULL,'2026-10-16 01:21:24','2026-10-16 01:21:24','2026-10-16 01:21:24'),(4,13,'find-your-tech-career-path-workshop','Find Your Tech Career Path','Free Learner','freelearner@example.com','9876543210','student','','','',0.00,'new','not_initiated','pending','','','','','','',NULL,'2026-10-16 01:21:38','2026-10-16 01:21:38','2026-10-16 01:21:38');
/*!40000 ALTER TABLE `workshop_leads` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-16 16:14:09
