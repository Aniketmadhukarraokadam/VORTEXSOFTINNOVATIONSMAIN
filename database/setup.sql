-- ============================================================
--  VORTEXSOFT INNOVATIONS — MySQL Database Setup Script
--  Database: u696371114_vortexsoftcom
--  Run this on Hostinger via phpMyAdmin or MySQL CLI
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ────────────────────────────────────────────────────────────
-- 1. CONTACT INQUIRIES
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_inquiries` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(120) NOT NULL,
  `email`        VARCHAR(180) NOT NULL,
  `phone`        VARCHAR(30)  DEFAULT NULL,
  `service`      VARCHAR(120) DEFAULT 'General Inquiry',
  `company`      VARCHAR(120) DEFAULT NULL,
  `message`      TEXT         NOT NULL,
  `ip_address`   VARCHAR(45)  DEFAULT NULL,
  `user_agent`   VARCHAR(255) DEFAULT NULL,
  `source_page`  VARCHAR(255) DEFAULT NULL,
  `is_read`      TINYINT(1)   NOT NULL DEFAULT 0,
  `is_replied`   TINYINT(1)   NOT NULL DEFAULT 0,
  `notes`        TEXT         DEFAULT NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email`      (`email`),
  INDEX `idx_is_read`    (`is_read`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 2. JOB APPLICATIONS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `job_applications` (
  `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `job_title`       VARCHAR(200)  NOT NULL,
  `department`      VARCHAR(100)  DEFAULT NULL,
  `applicant_name`  VARCHAR(120)  NOT NULL,
  `email`           VARCHAR(180)  NOT NULL,
  `phone`           VARCHAR(30)   DEFAULT NULL,
  `current_location`VARCHAR(120)  DEFAULT NULL,
  `experience_years`DECIMAL(4,1)  DEFAULT NULL,
  `current_company` VARCHAR(120)  DEFAULT NULL,
  `notice_period`   VARCHAR(60)   DEFAULT NULL,
  `expected_ctc`    VARCHAR(60)   DEFAULT NULL,
  `resume_filename` VARCHAR(255)  DEFAULT NULL,
  `resume_path`     VARCHAR(500)  DEFAULT NULL,
  `cover_letter`    TEXT          DEFAULT NULL,
  `linkedin_url`    VARCHAR(300)  DEFAULT NULL,
  `portfolio_url`   VARCHAR(300)  DEFAULT NULL,
  `ip_address`      VARCHAR(45)   DEFAULT NULL,
  `status`          ENUM('new','reviewed','shortlisted','interview','offered','rejected','withdrawn')
                                  NOT NULL DEFAULT 'new',
  `admin_notes`     TEXT          DEFAULT NULL,
  `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email`      (`email`),
  INDEX `idx_status`     (`status`),
  INDEX `idx_job_title`  (`job_title`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 2.1 JOB OPENINGS / POSTINGS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `jobs` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(200) NOT NULL,
  `department`       VARCHAR(100) NOT NULL,
  `type`             VARCHAR(60)  NOT NULL DEFAULT 'Full Time',
  `location`         VARCHAR(120) NOT NULL,
  `experience_range` VARCHAR(60)  DEFAULT NULL,
  `skills_json`      TEXT         DEFAULT NULL,
  `description`      TEXT         NOT NULL,
  `is_urgent`        TINYINT(1)   NOT NULL DEFAULT 0,
  `is_active`        TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`       INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_is_active`  (`is_active`),
  INDEX `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 3. BLOG POSTS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(300) NOT NULL,
  `slug`         VARCHAR(320) NOT NULL,
  `excerpt`      VARCHAR(500) DEFAULT NULL,
  `content`      LONGTEXT     NOT NULL,
  `author`       VARCHAR(100) NOT NULL DEFAULT 'Vortexsoft Team',
  `author_role`  VARCHAR(100) DEFAULT NULL,
  `cover_image`  VARCHAR(500) DEFAULT NULL,
  `category`     VARCHAR(100) NOT NULL DEFAULT 'General',
  `tags`         VARCHAR(500) DEFAULT NULL,
  `meta_title`   VARCHAR(300) DEFAULT NULL,
  `meta_desc`    VARCHAR(500) DEFAULT NULL,
  `views`        INT UNSIGNED NOT NULL DEFAULT 0,
  `is_published` TINYINT(1)   NOT NULL DEFAULT 0,
  `is_featured`  TINYINT(1)   NOT NULL DEFAULT 0,
  `published_at` DATETIME     DEFAULT NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug`         (`slug`),
  INDEX `idx_category`         (`category`),
  INDEX `idx_is_published`     (`is_published`),
  INDEX `idx_published_at`     (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 4. NEWSLETTER SUBSCRIBERS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`          VARCHAR(180) NOT NULL,
  `name`           VARCHAR(120) DEFAULT NULL,
  `ip_address`     VARCHAR(45)  DEFAULT NULL,
  `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
  `unsubscribe_token` VARCHAR(64) DEFAULT NULL,
  `subscribed_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at`DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 5. ADMIN USERS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`     VARCHAR(60)  NOT NULL,
  `password_hash`VARCHAR(255) NOT NULL,
  `email`        VARCHAR(180) NOT NULL,
  `full_name`    VARCHAR(120) DEFAULT NULL,
  `role`         ENUM('super_admin','admin','viewer') NOT NULL DEFAULT 'admin',
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `last_login`   DATETIME     DEFAULT NULL,
  `login_count`  INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 6. SYSTEM SETTINGS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `system_settings` (
  `setting_key`   VARCHAR(100) NOT NULL,
  `setting_value` TEXT         DEFAULT NULL,
  `updated_at`    DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 7. AI GENERATION AUDIT LOG
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `ai_generation_logs` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `topic`          VARCHAR(500)  NOT NULL,
  `target_keyword` VARCHAR(255)  NOT NULL,
  `admin_id`       INT UNSIGNED  DEFAULT NULL,
  `admin_username` VARCHAR(120)  DEFAULT NULL,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_admin_id`  (`admin_id`),
  INDEX `idx_created_at`(`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Audit log for AI blog generation batches — for cost tracking';

-- ────────────────────────────────────────────────────────────
-- 8. SEED INITIAL DATA
-- ────────────────────────────────────────────────────────────

-- Admin Accounts
REPLACE INTO `admin_users` (`id`, `username`, `password_hash`, `email`, `full_name`, `role`, `is_active`) VALUES
(1, 'admin@vortexsoftinnovations.in', '$2y$12$8IpMP6IJeshSPurTe5.baubMZF5rGtkdX4KDIAWiwN6tSSGiwR5SW', 'admin@vortexsoftinnovations.in', 'Super Admin', 'super_admin', 1),
(2, 'Aniket@vortexsoftinnovations.in', '$2y$12$AmQgvWVp/eQKMCnDzD3TK.a.0GwTVdoRcE4rS6i0VnA9cAWdP5Xta', 'Aniket@vortexsoftinnovations.in', 'Aniket Kadam', 'admin', 1);

-- Default Settings
REPLACE INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Vortexsoft Innovations Pvt. Ltd.'),
('contact_email', 'info@vortexsoftinnovations.in'),
('careers_email', 'careers@vortexsoftinnovations.in'),
('contact_phone', '+91 8308906690'),
('office_address', '125 Ranganath Complex, Madiwala, Bengaluru, Karnataka 560068');

-- SEO-Optimized Corporate Blog Posts
REPLACE INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `content`, `author`, `category`, `cover_image`, `meta_title`, `meta_desc`, `tags`, `is_published`, `is_featured`, `published_at`) VALUES
(1, 'The Definitive Guide to US Title Search & Settlement Abstracting in 2026', 'us-title-search-abstracting-guide-2026', 'An exhaustive 2026 operational guide to US residential and commercial title examination, 40-year deed chains, municipal lien audits, and how outsourcing cuts turnaround to under 48 hours with 99.8% accuracy.', '<p>US title search and settlement abstracting forms the legal and financial backbone of every real estate transaction. Learn how Vortexsoft delivers 24-48 hour title commitments across all 50 US states with certified SoftPro and Qualia examiners.</p>', 'Vortexsoft Title Pod', 'Real Estate BPO', '/uploads/blog/us-title-search-abstracting.jpg', 'US Title Search & Settlement Abstracting Guide 2026 | Vortexsoft', 'Learn how US title agencies and underwriters streamline 40-year deed chain searches, mortgage lien verifications, and settlement abstracting with 99.8% SLA accuracy.', 'US Title Search, Title Abstracting, Deed Chain Search, Real Estate BPO', 1, 1, '2026-10-24 10:30:00'),
(2, 'AI-Driven Document Extraction & RPA: Revolutionizing Real Estate & Mortgage Operations', 'ai-document-extraction-mortgage-real-estate', 'Discover how intelligent document processing (IDP) and robotic process automation (RPA) automate commercial lease abstraction, loan packet indexing, and deed parsing with 85% reduced processing times.', '<p>Intelligent Document Processing (IDP) combined with RPA is rewriting the economics of commercial real estate and mortgage loan origination with zero-touch automated classification.</p>', 'Vortexsoft AI Labs', 'AI & Data', '/uploads/blog/ai-document-extraction-workflows.jpg', 'AI Document Extraction in Real Estate & Mortgage BPO | Vortexsoft', 'Discover how intelligent document processing (IDP) and robotic process automation (RPA) automate commercial lease abstraction, loan packet indexing, and deed parsing.', 'AI Document Extraction, Mortgage Automation, Intelligent Document Processing, Real Estate RPA', 1, 1, '2026-10-22 14:15:00'),
(3, 'Medical Coding & Billing Accuracy: Reducing Claims Denials to Below 2% in US Healthcare RCM', 'medical-coding-billing-reducing-denials-rcm', 'Master modern healthcare revenue cycle management (RCM). Proven methodologies to eliminate front-end registration errors, accelerate prior authorization, and achieve 98%+ clean claims pass rates.', '<p>In US healthcare revenue cycle management, claim denials drain provider operating margins. Learn how Vortexsoft AAPC-certified coders achieve 98%+ clean claim pass rates.</p>', 'Healthcare RCM Practice Group', 'Healthcare BPO', '/uploads/blog/medical-coding-rcm-accuracy.jpg', 'Medical Coding & Denial Management in US Healthcare RCM | Vortexsoft', 'Master revenue cycle management strategies that eliminate coding errors, accelerate insurance prior authorization, and consistently achieve 98%+ clean claim pass rates.', 'Medical Coding, Medical Billing, Healthcare RCM, Claims Denials, AAPC Coders', 1, 1, '2026-10-20 09:00:00'),
(4, 'Architecting Enterprise Microservices with Java Spring Boot & Python FastAPI in High-Scale Systems', 'enterprise-microservices-java-python-scalability', 'Deep-dive architectural blueprint on building resilient enterprise microservices using Java Spring Boot, Python FastAPI, Docker containerization, and distributed database sharding.', '<p>Explore how polyglot microservices combining Java Spring Boot and Python FastAPI handle high-throughput transactions and real-time AI inference at scale.</p>', 'Vortexsoft Cloud Architecture Team', 'Technology', '/uploads/blog/enterprise-microservices-cloud.jpg', 'Enterprise Microservices with Java & Python: Scalability Best Practices', 'Explore architecture blueprints for building low-latency, distributed microservices using Java Spring Boot, Python FastAPI, Docker containers, and PostgreSQL sharding.', 'Microservices, Java Spring Boot, Python FastAPI, Docker, Kubernetes', 1, 1, '2026-10-18 11:45:00'),
(5, 'Precision Computer Vision & Video Annotation: Ground Truth Data for Autonomous AI', 'computer-vision-video-annotation-autonomous-ai', 'How multi-sensor LiDAR fusion, polygon video segmentation, and multi-tier QA pipelines ensure 99.8% ground truth accuracy for autonomous mobility and robotics.', '<p>High-volume, pixel-perfect data annotation is the catalyst transforming raw sensor inputs into reliable autonomous artificial intelligence.</p>', 'AI Data Annotation Practice', 'AI & Data', '/uploads/blog/ai-data-annotation-training.jpg', 'Computer Vision & Video Annotation for Autonomous AI | Vortexsoft', 'How multi-sensor LiDAR fusion, polygon video tracking, and multi-tier QA pipelines ensure 99.8% ground truth accuracy for autonomous mobility and robotics.', 'Computer Vision, Data Annotation, LiDAR 3D, Video Tracking, Autonomous Vehicles', 1, 1, '2026-10-15 16:20:00'),
(6, 'Digital Publishing Automation: ePUB3, XML JATS/BITS, and WCAG 2.1 AA Compliance', 'digital-publishing-epub3-jats-wcag-accessibility', 'A comprehensive operational guide to converting raw manuscripts into fully accessible ePUB3, JATS/BITS XML, and Section 508 / PDF-UA compliant digital publications with automated typesetting.', '<p>Academic and STM journal publishers face rising accessibility and metadata mandates. Discover how automated XML workflows ensure full compliance.</p>', 'Publishing & Prepress Unit', 'Publishing', '/uploads/blog/digital-publishing-accessibility.jpg', 'Digital Publishing Automation: ePUB3, JATS XML & WCAG 2.1 AA | Vortexsoft', 'A comprehensive operational guide to converting raw manuscripts into fully accessible ePUB3, JATS/BITS XML, and Section 508 / PDF-UA compliant digital publications.', 'Digital Publishing, ePUB3, JATS XML, BITS XML, S1000D, WCAG 2.1 AA', 1, 1, '2026-10-12 13:10:00');

-- Active Job Openings (23 Corporate Positions with Focused Titles)
REPLACE INTO `jobs` (`id`, `title`, `department`, `type`, `location`, `experience_range`, `skills_json`, `description`, `is_urgent`, `is_active`, `sort_order`) VALUES
(1, 'Java Developer', 'IT & Software', 'Full Time', 'Bengaluru / Pune / Remote', '2–5 years', 'Java, Spring Boot, Microservices, Hibernate, REST APIs, SQL, Docker', 'Design, build, and maintain robust enterprise backend microservices and RESTful APIs using modern Java and Spring Boot. Optimize high-volume database queries and collaborate on cloud-native deployments.', 1, 1, 1),
(2, 'Python Developer', 'IT & Software', 'Full Time', 'Bengaluru / Remote', '2–5 years', 'Python, Django, FastAPI, PostgreSQL, REST APIs, Celery, Docker', 'Develop scalable backend applications, automated data pipelines, and high-performance REST APIs. Collaborate with product teams to build clean architectures with Python, FastAPI, and PostgreSQL.', 1, 1, 2),
(3, 'Data Engineer', 'IT & Software', 'Full Time', 'Bengaluru / Remote', '2–5 years', 'Python, SQL, Apache Spark, Airflow, ETL Pipelines, Snowflake, AWS', 'Design, construct, and operationalize resilient batch and real-time ETL pipelines. Manage data ingestion, transformation workflows, and cloud data warehouses ensuring high data reliability.', 1, 1, 3),
(4, 'React Developer', 'IT & Software', 'Full Time', 'Bengaluru / Pune / Remote', '2–4 years', 'React.js, TypeScript, Redux Toolkit, Tailwind CSS, Next.js, REST APIs', 'Build responsive, accessible, high-performance web applications and enterprise dashboards using React.js and modern front-end state architectures.', 1, 1, 4),
(5, 'DevOps Engineer', 'IT & Software', 'Full Time', 'Bengaluru / Remote', '2–5 years', 'AWS, Docker, Kubernetes, CI/CD, Terraform, GitHub Actions, Linux', 'Architect resilient CI/CD release pipelines, manage container orchestration with Kubernetes, and maintain cloud infrastructure on AWS with automated telemetry.', 1, 1, 5),
(6, 'AI Engineer', 'IT & Software', 'Full Time', 'Bengaluru / Remote', '2–5 years', 'PyTorch, LLMs, LangChain, Hugging Face, Computer Vision, FastAPI', 'Design and implement production-grade generative AI pipelines, computer vision models, and fine-tune large language models for enterprise clients.', 1, 1, 6),
(7, 'QA Engineer', 'IT & Software', 'Full Time', 'Pune / Bengaluru', '1–4 years', 'Selenium, Cypress, Manual Testing, API Testing, Postman, JIRA, SQL', 'Execute rigorous end-to-end regression, automated browser testing, and API verification to guarantee zero-defect software releases.', 0, 1, 7),
(8, 'HR Executive', 'Human Resources', 'Full Time', 'Pune / Bengaluru', '1–4 years', 'Talent Acquisition, IT Recruitment, Onboarding, Employee Engagement, HR Operations', 'Manage end-to-end talent acquisition for engineering, AI, and BPO divisions. Coordinate interviews, campus drives, new hire onboarding, HR policies, and employee engagement programs.', 0, 1, 8),
(9, 'IT Recruiter', 'Human Resources', 'Full Time', 'Bengaluru / Pune', '1–4 years', 'Technical Sourcing, LinkedIn Recruiter, Candidate Screening, Offer Negotiation', 'Source, engage, and recruit top technical talent across software engineering, cloud, data science, and AI disciplines for high-growth client engagements.', 0, 1, 9),
(10, 'Accounts Executive', 'Finance & Accounting', 'Full Time', 'Pune HQ', '1–4 years', 'Tally Prime, QuickBooks, GST Filing, TDS, Invoicing, Bank Reconciliation', 'Manage day-to-day corporate financial transactions, accounts payable, receivables, statutory tax filings, payroll disbursements, and vendor reconciliations.', 0, 1, 10),
(11, 'Associate Process Analyst', 'Business Operations', 'Full Time', 'Pune / Bengaluru', '1–3 years', 'Process Mapping, Data Analysis, MIS Reporting, Workflow Optimization, Advanced Excel', 'Analyze business operations workflows, map process lifecycles, and identify operational bottlenecks. Create comprehensive MIS reporting dashboards and optimize client turnaround times.', 0, 1, 11),
(12, 'Operations Coordinator', 'Business Operations', 'Full Time', 'Pune HQ', '1–3 years', 'SLA Tracking, Client Reporting, Shift Management, Workflow Coordination', 'Monitor real-time operational delivery queues, track adherence to stringent client SLAs, and coordinate daily shift handovers across multidisciplinary BPO teams.', 0, 1, 12),
(13, 'Title Search Executive', 'Real Estate BPO', 'Full Time', 'Pune HQ', '1–4 years', '40-Year Title Searches, Deed Chains, Mortgage Liens, Tax Searches, SoftPro, Qualia', 'Conduct detailed title searches and examinations on US county property records. Verify ownership deed chains, identify open liens and taxes, and prepare commitment packages for US title settlement clients.', 1, 1, 13),
(14, 'Lease Analyst', 'Real Estate BPO', 'Full Time', 'Pune / Bengaluru', '1–4 years', 'Commercial Lease Abstraction, Rent Roll Audit, CAM Reconciliation, MRI, Yardi', 'Abstract complex commercial lease agreements, extract critical financial terms, dates, and CAM escalation clauses into industry ERP platforms like Yardi and MRI.', 1, 1, 14),
(15, 'Tax Search Analyst', 'Real Estate BPO', 'Full Time', 'Pune HQ', '1–3 years', 'Property Tax Research, Municipal Liens, Tax Certificates, County Portal Audits', 'Audit municipal property tax records, delinquent assessment rolls, and municipal utility liens across 3,000+ US county assessor portals.', 0, 1, 15),
(16, 'BPO Executive', 'Customer Operations', 'Full Time', 'Pune / Bengaluru', '0–3 years', 'Voice & Non-Voice Support, Customer Engagement, CRM Systems, Email Support, Active Listening', 'Provide first-line client communication and transactional support across inbound, outbound, and digital channels. Resolve customer inquiries with precision while maintaining stellar SLA metrics.', 0, 1, 16),
(17, 'Team Leader', 'Customer Operations', 'Full Time', 'Pune / Bengaluru', '3–6 years', 'Team Management, Quality Auditing, SLA Adherence, Escalation Handling, Coaching', 'Lead, mentor, and coach frontline operational teams. Drive productivity, monitor KPI scorecards, handle critical client escalations, and champion quality improvement.', 1, 1, 17),
(18, 'KPO Analyst', 'Research & Analytics', 'Full Time', 'Bengaluru / Pune', '1–4 years', 'Market Research, Financial Analysis, Business Intelligence, Data Synthesis, Advanced Excel', 'Deliver high-value secondary research, industry benchmarking, and financial analysis for global clients. Synthesize complex data sets into actionable presentation decks and strategic intelligence reports.', 0, 1, 18),
(19, 'Data Analyst', 'Research & Analytics', 'Full Time', 'Bengaluru / Pune', '1–4 years', 'SQL, Power BI, Tableau, Python, Data Cleansing, Dashboard Reporting', 'Extract, transform, and analyze enterprise data to build intuitive interactive dashboards and deliver strategic insights for executive decision-makers.', 1, 1, 19),
(20, 'Medical Coder', 'Healthcare BPO', 'Full Time', 'Bengaluru / Remote', '1–4 years', 'CPC / COC Certified, ICD-10-CM, CPT-4, HCPCS, E&M Coding, HIPAA Compliance', 'Review clinical documentation and provider charts to assign accurate diagnostic and procedural codes according to US healthcare standards. Maintain >96% accuracy and ensure strict HIPAA adherence.', 1, 1, 20),
(21, 'Medical Biller', 'Healthcare BPO', 'Full Time', 'Bengaluru / Remote', '1–4 years', 'Charge Entry, Payment Posting, Claim Scrubbing, CMS-1500, UB-04, Kareo', 'Handle claims scrubbing, electronic submission via clearinghouses, charge entry, payment posting, and insurance verification for US hospital networks.', 1, 1, 21),
(22, 'AR Caller', 'Healthcare BPO', 'Full Time', 'Bengaluru / Pune', '1–4 years', 'Denial Management, Payer Follow-up, Claim Appeals, Insurance Verification, EOB Review', 'Proactively follow up with commercial and government payers on aged accounts receivable, investigate claim denial reasons, and file appeals to recover revenue.', 1, 1, 22),
(23, 'XML Typesetter', 'Publishing & Prepress', 'Full Time', 'Pune / Remote', '1–4 years', 'JATS XML, BITS XML, InDesign Prepress, MathML, ePUB3, WCAG 2.1 AA', 'Format and convert complex STM academic manuscripts into validated JATS/BITS XML, accessible ePUB3 files, and camera-ready InDesign print layouts.', 0, 1, 23);

SET FOREIGN_KEY_CHECKS = 1;

