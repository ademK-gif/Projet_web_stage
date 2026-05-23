-- =============================================
-- JobLink - Base de données complète avec données de démonstration
-- Importer dans phpMyAdmin après avoir créé la BD "portail_emploi"
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- =============================================
-- TABLES
-- =============================================

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','company','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `student_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `company_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_company_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `job_offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `offer_type` enum('stage','emploi') NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `remuneration` varchar(100) DEFAULT NULL,
  `status` enum('active','closed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `fk_offer_company` FOREIGN KEY (`company_id`) REFERENCES `company_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('pending','reviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `offer_id` (`offer_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `fk_app_offer` FOREIGN KEY (`offer_id`) REFERENCES `job_offers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_app_student` FOREIGN KEY (`student_id`) REFERENCES `student_profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `location_or_link` varchar(255) NOT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `application_id` (`application_id`),
  CONSTRAINT `fk_int_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- DONNÉES DE DÉMONSTRATION
-- Mot de passe pour tous les comptes : 123456
-- Hash bcrypt de "123456"
-- =============================================

INSERT INTO `users` (`id`, `email`, `password`, `role`) VALUES
(1, 'etudiant@test.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
(2, 'ahmed@test.com',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
(3, 'fatma@test.com',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
(4, 'entreprise@test.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'company'),
(5, 'startup@test.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'company'),
(6, 'banque@test.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'company');

INSERT INTO `student_profiles` (`id`, `user_id`, `first_name`, `last_name`, `bio`, `sector`, `location`, `portfolio_url`) VALUES
(1, 1, 'Yessine', 'Ben Salah', 'Étudiant en 3ème année Informatique à l\'ISET Tunis. Passionné par le développement web et mobile, je cherche un stage de fin d\'études pour mettre en pratique mes compétences en PHP, JavaScript et React.', 'informatique', 'Tunis', 'https://github.com/yessine'),
(2, 2, 'Ahmed', 'Khalil', 'Étudiant en Marketing Digital à l\'ISG Tunis. Je maîtrise les réseaux sociaux, le SEO et la création de contenu. À la recherche d\'un stage de 3 mois.', 'marketing', 'Sfax', NULL),
(3, 3, 'Fatma', 'Mansour', 'Future ingénieure en Génie Civil à l\'ENIT. Rigoureuse et motivée, je recherche un stage d\'été dans le secteur de la construction ou du BTP.', 'ingenierie', 'Sousse', 'https://portfolio.fatma.tn');

INSERT INTO `company_profiles` (`id`, `user_id`, `company_name`, `description`, `website_url`, `sector`, `location`) VALUES
(1, 4, 'TechSolutions Tunisie', 'Entreprise spécialisée dans le développement de logiciels sur mesure, les applications web et mobiles, et la transformation digitale des entreprises tunisiennes. Fondée en 2015, nous comptons 45 collaborateurs.', 'https://techsolutions.tn', 'informatique', 'Tunis'),
(2, 5, 'DigitalBoost', 'Agence de marketing digital innovante offrant des services de SEO, publicité en ligne, gestion des réseaux sociaux et création de contenu. Nous accompagnons les PME tunisiennes dans leur croissance digitale.', 'https://digitalboost.tn', 'marketing', 'Tunis'),
(3, 6, 'Amen Bank', 'L\'une des principales banques privées de Tunisie, offrant des services financiers complets aux particuliers et aux entreprises. Nous recrutons régulièrement des talents pour renforcer nos équipes.', 'https://amenbank.com.tn', 'finance', 'Tunis');

INSERT INTO `job_offers` (`id`, `company_id`, `title`, `description`, `offer_type`, `sector`, `location`, `duration`, `remuneration`, `status`) VALUES
(1, 1, 'Développeur PHP/Laravel - Stage de fin d\'études', 'Nous recherchons un stagiaire développeur PHP passionné pour rejoindre notre équipe technique.\n\nMissions :\n- Développement et maintenance d\'applications web avec Laravel\n- Création d\'APIs RESTful\n- Intégration de maquettes HTML/CSS\n- Tests unitaires et débogage\n\nProfil recherché :\n- Étudiant en informatique (Bac+3 minimum)\n- Maîtrise de PHP et MySQL\n- Connaissances en Laravel appréciées\n- Rigoureux, curieux et autonome\n\nAvantages : encadrement personnalisé, environnement de travail moderne.', 'stage', 'informatique', 'Tunis', '3 mois', '400 DT/mois', 'active'),
(2, 1, 'Développeur React.js - CDI', 'TechSolutions Tunisie recherche un développeur React.js confirmé pour intégrer son équipe front-end.\n\nMissions :\n- Développement d\'interfaces utilisateurs modernes avec React\n- Collaboration avec l\'équipe back-end (Node.js/PHP)\n- Revue de code et mentorat de stagiaires\n\nProfil :\n- 2 ans d\'expérience minimum en React\n- Maîtrise de TypeScript, Redux, REST APIs\n- Connaissances en Next.js appréciées\n\nSalaire : selon profil et expérience.', 'emploi', 'informatique', 'Tunis', 'CDI', 'À négocier', 'active'),
(3, 2, 'Stage Marketing Digital & Réseaux Sociaux', 'DigitalBoost recherche un stagiaire passionné par le marketing digital pour renforcer son équipe créative.\n\nMissions :\n- Gestion et animation des comptes réseaux sociaux (Instagram, Facebook, LinkedIn)\n- Création de contenus visuels (Canva, Photoshop)\n- Suivi et analyse des performances (Google Analytics)\n- Rédaction d\'articles SEO\n\nProfil :\n- Étudiant en marketing, communication ou équivalent\n- Créatif, organisé et force de proposition\n- Maîtrise des outils digitaux', 'stage', 'marketing', 'Tunis', '2 mois', '300 DT/mois', 'active'),
(4, 2, 'Chef de Projet Digital - CDD', 'Nous recrutons un Chef de Projet Digital dynamique pour piloter nos campagnes clients.\n\nMissions :\n- Coordination des projets digitaux de A à Z\n- Relation client et suivi des livrables\n- Management d\'une équipe de 4 personnes\n- Reporting et analyse des KPIs\n\nProfil :\n- Bac+5 en marketing ou gestion\n- 3 ans d\'expérience en gestion de projets digitaux\n- Excellentes compétences en communication', 'emploi', 'marketing', 'Tunis', 'CDD 6 mois', '1800 DT/mois', 'active'),
(5, 3, 'Stage Analyste Financier', 'Amen Bank propose un stage enrichissant au sein de sa direction financière.\n\nMissions :\n- Analyse des états financiers et tableaux de bord\n- Participation à l\'élaboration du budget\n- Suivi des indicateurs de performance\n- Rédaction de rapports financiers\n\nProfil :\n- Étudiant en finance, comptabilité ou gestion (Bac+3 minimum)\n- Maîtrise d\'Excel avancé\n- Rigueur, discrétion et sens de l\'analyse\n- Bonne maîtrise du français et de l\'arabe', 'stage', 'finance', 'Tunis', '3 mois', '500 DT/mois', 'active'),
(6, 1, 'Ingénieur DevOps - CDI', 'Nous cherchons un Ingénieur DevOps pour moderniser notre infrastructure cloud.\n\nMissions :\n- Mise en place et gestion des pipelines CI/CD (GitLab CI)\n- Administration des serveurs Linux et conteneurs Docker/Kubernetes\n- Monitoring et optimisation des performances\n- Sécurisation de l\'infrastructure\n\nProfil :\n- Bac+5 en informatique\n- Expérience avec AWS ou Azure\n- Maîtrise de Docker, Kubernetes, Terraform', 'emploi', 'informatique', 'Tunis', 'CDI', '2500 DT/mois', 'active');

INSERT INTO `applications` (`id`, `offer_id`, `student_id`, `cover_letter`, `status`, `applied_at`) VALUES
(1, 1, 1, 'Je suis très motivé par ce poste de développeur PHP chez TechSolutions. Étudiant en 3ème année informatique, je maîtrise PHP, MySQL et ai travaillé sur plusieurs projets Laravel durant ma formation. Ce stage serait une opportunité idéale pour valider mes compétences en environnement professionnel.', 'reviewed', '2026-05-01 09:00:00'),
(2, 3, 2, 'Passionné par le marketing digital et les réseaux sociaux, je pense être le candidat idéal pour ce stage. J\'ai déjà géré les comptes Instagram de mon association étudiante et maîtrise les outils Canva et Google Analytics.', 'pending', '2026-05-03 14:30:00'),
(3, 5, 1, 'Bien que mon profil soit plutôt technique, je m\'intéresse également à la finance et souhaite découvrir l\'analyse financière. Je suis rigoureux et maîtrise Excel avancé.', 'pending', '2026-05-05 10:15:00'),
(4, 1, 3, 'En tant qu\'étudiante en génie civil, je souhaite élargir mes compétences vers l\'informatique. Je suis très motivée et apprenante rapide.', 'rejected', '2026-04-28 11:00:00');

INSERT INTO `messages` (`sender_id`, `receiver_id`, `content`, `is_read`, `sent_at`) VALUES
(4, 1, 'Bonjour Yessine, nous avons bien reçu votre candidature pour le poste de développeur PHP. Votre profil nous intéresse. Seriez-vous disponible pour un entretien la semaine prochaine ?', 1, '2026-05-02 10:00:00'),
(1, 4, 'Bonjour, merci pour votre retour ! Je suis tout à fait disponible la semaine prochaine. Quels créneaux vous conviennent le mieux ?', 1, '2026-05-02 11:30:00'),
(4, 1, 'Parfait ! Nous vous proposons le mardi 13 mai à 10h00 dans nos locaux à Tunis Lac. Confirmez-vous ce rendez-vous ?', 0, '2026-05-02 14:00:00');

INSERT INTO `interviews` (`application_id`, `scheduled_at`, `location_or_link`, `status`, `notes`) VALUES
(1, '2026-05-13 10:00:00', 'TechSolutions Tunisie - Les Berges du Lac, Tunis', 'scheduled', 'Apporter CV imprimé et portfolio. Entretien technique de 45 minutes suivi d\'un entretien RH.');
