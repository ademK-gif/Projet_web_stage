<?php
// includes/sidebar_company.php
?>
<aside class="sidebar">
  <div class="sidebar-logo">JobLink</div>
  <nav class="sidebar-nav">
    <div class="sidebar-section">Principal</div>
    <a href="/siteadam/entreprise/dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])==='dashboard.php'?'active':'' ?>">
      <span class="nav-icon">📊</span> Tableau de bord
    </a>
    <div class="sidebar-section">Offres</div>
    <a href="/siteadam/entreprise/offres.php" class="<?= basename($_SERVER['PHP_SELF'])==='offres.php'?'active':'' ?>">
      <span class="nav-icon">📢</span> Mes offres
    </a>
    <a href="/siteadam/entreprise/nouvelle-offre.php" class="<?= basename($_SERVER['PHP_SELF'])==='nouvelle-offre.php'?'active':'' ?>">
      <span class="nav-icon">➕</span> Publier une offre
    </a>
    <div class="sidebar-section">Recrutement</div>
    <a href="/siteadam/entreprise/candidatures.php" class="<?= basename($_SERVER['PHP_SELF'])==='candidatures.php'?'active':'' ?>">
      <span class="nav-icon">👥</span> Candidatures
    </a>
    <a href="/siteadam/entreprise/agenda.php" class="<?= basename($_SERVER['PHP_SELF'])==='agenda.php'?'active':'' ?>">
      <span class="nav-icon">📅</span> Agenda entretiens
    </a>
    <div class="sidebar-section">Communication</div>
    <a href="/siteadam/entreprise/messages.php" class="<?= basename($_SERVER['PHP_SELF'])==='messages.php'?'active':'' ?>">
      <span class="nav-icon">💬</span> Messages
    </a>
    <div class="sidebar-section">Compte</div>
    <a href="/siteadam/entreprise/profil.php" class="<?= basename($_SERVER['PHP_SELF'])==='profil.php'?'active':'' ?>">
      <span class="nav-icon">🏢</span> Profil entreprise
    </a>
    <a href="/siteadam/logout.php" style="color:rgba(239,68,68,.8)">
      <span class="nav-icon">🚪</span> Déconnexion
    </a>
  </nav>
</aside>
