<?php
require_once 'config/db.php';
$page_title = 'JobLink - Trouvez votre Stage ou Emploi';
include 'includes/header.php';

// Offres récentes
$offres = $pdo->query("SELECT jo.*, cp.company_name, cp.sector as company_sector FROM job_offers jo JOIN company_profiles cp ON jo.company_id=cp.id WHERE jo.status='active' ORDER BY jo.created_at DESC LIMIT 6")->fetchAll();
?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="hero-badge">🚀 +500 offres disponibles dès aujourd'hui</div>
    <h1 class="fade-up">Lancez votre carrière<br>avec les meilleures entreprises</h1>
    <p class="fade-up delay-1">Trouvez le stage ou l'emploi idéal parmi des centaines d'offres exclusives. Postulez en un clic.</p>

    <form class="search-box fade-up delay-2" action="offres.php" method="GET">
      <input type="text" name="q" placeholder="Poste, compétence, entreprise...">
      <div class="divider"></div>
      <select name="sector">
        <option value="">Tous les secteurs</option>
        <option value="informatique">Informatique / Tech</option>
        <option value="marketing">Marketing</option>
        <option value="finance">Finance</option>
        <option value="design">Design / UX</option>
        <option value="rh">Ressources Humaines</option>
        <option value="commerce">Commerce</option>
      </select>
      <div class="divider"></div>
      <select name="type">
        <option value="">Stage & Emploi</option>
        <option value="stage">Stage</option>
        <option value="emploi">Emploi</option>
      </select>
      <button type="submit" class="btn btn-primary">🔍 Rechercher</button>
    </form>

    <div class="hero-stats fade-up delay-3">
      <div class="stat-item"><div class="stat-num">1 250+</div><div class="stat-label">Étudiants inscrits</div></div>
      <div class="stat-item"><div class="stat-num">340+</div><div class="stat-label">Entreprises partenaires</div></div>
      <div class="stat-item"><div class="stat-num">580+</div><div class="stat-label">Offres publiées</div></div>
      <div class="stat-item"><div class="stat-num">95%</div><div class="stat-label">Taux de satisfaction</div></div>
    </div>
  </div>
</section>

<!-- OFFRES RÉCENTES -->
<section class="section" style="background:#fff">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">🔥 Nouvelles offres</div>
      <h2>Offres récentes</h2>
      <p>Les dernières opportunités publiées par nos entreprises partenaires</p>
    </div>

    <?php if(empty($offres)): ?>
    <div class="empty-state">
      <div class="empty-icon">📋</div>
      <h3>Aucune offre pour le moment</h3>
      <p>Revenez bientôt, des offres sont publiées chaque jour !</p>
    </div>
    <?php else: ?>
    <div class="jobs-grid">
      <?php foreach($offres as $o): ?>
      <a href="offre-detail.php?id=<?= $o['id'] ?>" class="job-card">
        <div class="job-card-header">
          <div class="company-logo"><?= strtoupper(substr($o['company_name'],0,1)) ?></div>
          <div>
            <div class="job-title"><?= htmlspecialchars($o['title']) ?></div>
            <div class="company-name"><?= htmlspecialchars($o['company_name']) ?></div>
          </div>
        </div>
        <div class="job-tags">
          <span class="tag tag-type"><?= $o['offer_type']==='stage' ? '🎓 Stage' : '💼 Emploi' ?></span>
          <?php if($o['location']): ?><span class="tag tag-location">📍 <?= htmlspecialchars($o['location']) ?></span><?php endif; ?>
          <?php if($o['remuneration']): ?><span class="tag tag-salary">💰 <?= htmlspecialchars($o['remuneration']) ?></span><?php endif; ?>
          <?php if($o['duration']): ?><span class="tag tag-duration">⏱ <?= htmlspecialchars($o['duration']) ?></span><?php endif; ?>
        </div>
        <p style="font-size:.88rem;color:var(--muted);line-height:1.5"><?= htmlspecialchars(substr($o['description'],0,120)) ?>...</p>
        <div class="job-card-footer">
          <span class="job-date">🕒 <?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
          <span class="btn btn-primary btn-sm">Postuler →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div style="text-align:center;margin-top:2.5rem">
      <a href="offres.php" class="btn btn-secondary">Voir toutes les offres →</a>
    </div>
  </div>
</section>

<!-- FONCTIONNALITÉS -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">✨ Pourquoi JobLink ?</div>
      <h2>Tout ce dont vous avez besoin</h2>
      <p>Une plateforme complète pour réussir votre insertion professionnelle</p>
    </div>
    <div class="features-grid">
      <div class="card feature-card fade-up">
        <div class="feature-icon">🔍</div>
        <h3>Recherche avancée</h3>
        <p>Filtrez les offres par secteur, localisation, durée et rémunération pour trouver exactement ce que vous cherchez.</p>
      </div>
      <div class="card feature-card fade-up delay-1">
        <div class="feature-icon">📄</div>
        <h3>Profil & CV en ligne</h3>
        <p>Créez un profil complet avec votre CV, portfolio et compétences. Soyez visible auprès des recruteurs.</p>
      </div>
      <div class="card feature-card fade-up delay-2">
        <div class="feature-icon">📊</div>
        <h3>Suivi des candidatures</h3>
        <p>Suivez l'état de toutes vos candidatures en temps réel depuis un tableau de bord intuitif.</p>
      </div>
      <div class="card feature-card fade-up delay-3">
        <div class="feature-icon">💬</div>
        <h3>Messagerie interne</h3>
        <p>Communiquez directement avec les recruteurs via notre messagerie sécurisée intégrée.</p>
      </div>
      <div class="card feature-card fade-up">
        <div class="feature-icon">🤖</div>
        <h3>Recommandations IA</h3>
        <p>Notre algorithme vous suggère automatiquement les offres les plus adaptées à votre profil.</p>
      </div>
      <div class="card feature-card fade-up delay-1">
        <div class="feature-icon">📅</div>
        <h3>Agenda d'entretiens</h3>
        <p>Planifiez et gérez vos entretiens directement sur la plateforme. Ne ratez plus aucune opportunité.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section" style="background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;text-align:center">
  <div class="container">
    <h2 style="color:#fff;font-size:2.25rem;margin-bottom:1rem">Prêt à démarrer ?</h2>
    <p style="opacity:.9;font-size:1.1rem;margin-bottom:2rem">Rejoignez +1250 étudiants qui ont trouvé leur voie grâce à JobLink.</p>
    <div class="hero-btns">
      <a href="register.php?role=student" class="btn" style="background:#fff;color:var(--primary)">👨‍🎓 Je suis étudiant</a>
      <a href="register.php?role=company" class="btn" style="background:rgba(255,255,255,.15);color:#fff;border:2px solid rgba(255,255,255,.5)">🏢 Je suis recruteur</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
