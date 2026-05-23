<?php
session_start();
$page_title = $page_title ?? 'JobLink - Plateforme Emploi & Stage';
$page_desc = $page_desc ?? 'Plateforme de rencontre entre étudiants et entreprises pour stages et emplois.';
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
<link rel="stylesheet" href="/siteadam/css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💼</text></svg>">
</head>
<body>

<nav class="navbar">
  <div class="container">
    <a href="/siteadam/index.php" class="nav-logo">Job<span style="color:var(--secondary)">Link</span></a>
    <ul class="nav-links">
      <li><a href="/siteadam/offres.php">Offres</a></li>
      <li><a href="/siteadam/entreprises.php">Entreprises</a></li>
      <?php if(isset($_SESSION['user_id'])): ?>
        <?php $dash = $_SESSION['role']==='company' ? 'entreprise/dashboard.php' : 'etudiant/dashboard.php'; ?>
        <li><a href="/siteadam/<?= $dash ?>" class="btn btn-outline btn-sm">Mon espace</a></li>
        <li><div class="nav-avatar"><?= strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)) ?></div></li>
        <li><a href="/siteadam/logout.php" style="font-size:.85rem;color:var(--danger)">Déconnexion</a></li>
      <?php else: ?>
        <li><a href="/siteadam/login.php" class="btn btn-outline btn-sm">Connexion</a></li>
        <li><a href="/siteadam/register.php" class="btn btn-primary btn-sm">Inscription gratuite</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
