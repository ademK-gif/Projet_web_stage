<?php
// Fichier d'authentification réutilisable
session_start();
if(!isset($_SESSION['user_id'])) { header('Location: /siteadam/login.php'); exit; }
if(isset($required_role) && $_SESSION['role'] !== $required_role) { header('Location: /siteadam/login.php'); exit; }
require_once __DIR__.'/../config/db.php';
