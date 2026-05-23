<?php
// includes/sidebar_student.php
?>
<aside class="sidebar">
  <div class="sidebar-logo">JobLink</div>
  <nav class="sidebar-nav">
    <div class="sidebar-section">Principal</div>
    <a href="/siteadam/etudiant/dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])==='dashboard.php'?'active':'' ?>">
      <span class="nav-icon">📊</span> Tableau de bord
    </a>
    <a href="/siteadam/offres.php">
      <span class="nav-icon">🔍</span> Chercher des offres
    </a>
    <a href="/siteadam/etudiant/candidatures.php" class="<?= basename($_SERVER['PHP_SELF'])==='candidatures.php'?'active':'' ?>">
      <span class="nav-icon">📋</span> Mes candidatures
    </a>
    <div class="sidebar-section">Mon Profil</div>
    <a href="/siteadam/etudiant/profil.php" class="<?= basename($_SERVER['PHP_SELF'])==='profil.php'?'active':'' ?>">
      <span class="nav-icon">👤</span> Mon profil
    </a>
    <div class="sidebar-section">Communication</div>
    <a href="/siteadam/etudiant/messages.php" class="<?= basename($_SERVER['PHP_SELF'])==='messages.php'?'active':'' ?>">
      <span class="nav-icon">💬</span> Messages
    </a>
    <div class="sidebar-section" style="margin-top:auto"></div>
    <a href="/siteadam/logout.php" style="color:rgba(239,68,68,.8)">
      <span class="nav-icon">🚪</span> Déconnexion
    </a>
  </nav>
</aside>
