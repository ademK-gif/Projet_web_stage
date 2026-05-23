<?php
$required_role = 'student';
require_once '../includes/auth.php';

// Get student profile
$sp = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id=?");
$sp->execute([$_SESSION['user_id']]);
$profile = $sp->fetch();
$sid = $profile['id'];

// Stats
$total_apps  = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE student_id=?"); $total_apps->execute([$sid]);
$pending     = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE student_id=? AND status='pending'"); $pending->execute([$sid]);
$accepted    = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE student_id=? AND status='accepted'"); $accepted->execute([$sid]);
$interviews  = $pdo->prepare("SELECT COUNT(*) FROM interviews i JOIN applications a ON i.application_id=a.id WHERE a.student_id=? AND i.status='scheduled'"); $interviews->execute([$sid]);

// Recent applications
$recent = $pdo->prepare("SELECT a.*,jo.title,jo.offer_type,cp.company_name FROM applications a JOIN job_offers jo ON a.offer_id=jo.id JOIN company_profiles cp ON jo.company_id=cp.id WHERE a.student_id=? ORDER BY a.applied_at DESC LIMIT 5");
$recent->execute([$sid]);
$recent_apps = $recent->fetchAll();

// Recommended offers (same sector)
$recs = $pdo->prepare("SELECT jo.*,cp.company_name FROM job_offers jo JOIN company_profiles cp ON jo.company_id=cp.id WHERE jo.status='active' AND (jo.sector=? OR jo.sector IS NULL) AND jo.id NOT IN (SELECT offer_id FROM applications WHERE student_id=?) ORDER BY RAND() LIMIT 3");
$recs->execute([$profile['sector'] ?? '', $sid]);
$recommended = $recs->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tableau de bord Étudiant - JobLink</title>
<link rel="stylesheet" href="/siteadam/css/style.css">
</head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_student.php'; ?>
  <div class="dash-content">
    <div class="dash-header">
      <h1>👋 Bonjour, <?= htmlspecialchars($profile['first_name'] ?? $_SESSION['name']) ?> !</h1>
      <div style="display:flex;gap:.75rem">
        <a href="/siteadam/offres.php" class="btn btn-primary btn-sm">🔍 Trouver une offre</a>
        <div class="nav-avatar"><?= strtoupper(substr($_SESSION['name'],0,1)) ?></div>
      </div>
    </div>

    <div class="dash-body">
      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">📋</div>
          <div><div class="stat-val"><?= $total_apps->fetchColumn() ?></div><div class="stat-lbl">Candidatures envoyées</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon orange">⏳</div>
          <div><div class="stat-val"><?= $pending->fetchColumn() ?></div><div class="stat-lbl">En attente</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">✅</div>
          <div><div class="stat-val"><?= $accepted->fetchColumn() ?></div><div class="stat-lbl">Acceptées</div></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple">📅</div>
          <div><div class="stat-val"><?= $interviews->fetchColumn() ?></div><div class="stat-lbl">Entretiens planifiés</div></div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 380px;gap:1.5rem">
        <!-- RECENT APPLICATIONS -->
        <div class="table-wrap">
          <div class="table-head">
            <h3>📋 Dernières candidatures</h3>
            <a href="/siteadam/etudiant/candidatures.php" class="btn btn-outline btn-sm">Voir tout</a>
          </div>
          <?php if(empty($recent_apps)): ?>
          <div class="empty-state" style="padding:2rem">
            <div class="empty-icon">📭</div>
            <h3>Aucune candidature</h3>
            <p>Commencez à postuler dès maintenant !</p>
            <a href="/siteadam/offres.php" class="btn btn-primary btn-sm" style="margin-top:1rem">Voir les offres</a>
          </div>
          <?php else: ?>
          <table>
            <thead><tr><th>Poste</th><th>Entreprise</th><th>Type</th><th>Statut</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach($recent_apps as $a): ?>
            <tr>
              <td><strong><?= htmlspecialchars($a['title']) ?></strong></td>
              <td><?= htmlspecialchars($a['company_name']) ?></td>
              <td><span class="tag tag-type" style="font-size:.75rem"><?= $a['offer_type']==='stage'?'🎓 Stage':'💼 Emploi' ?></span></td>
              <td><span class="badge badge-<?= $a['status'] ?>"><?= ['pending'=>'En attente','reviewed'=>'En cours','accepted'=>'Accepté','rejected'=>'Refusé'][$a['status']] ?></span></td>
              <td style="color:var(--muted);font-size:.82rem"><?= date('d/m/Y',strtotime($a['applied_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>

        <!-- RECOMMENDATIONS -->
        <div>
          <div class="card" style="margin-bottom:0">
            <h3 style="margin-bottom:1rem">🤖 Offres recommandées</h3>
            <?php if(empty($recommended)): ?>
            <p style="color:var(--muted);font-size:.9rem">Complétez votre profil pour recevoir des recommandations personnalisées.</p>
            <?php else: ?>
            <?php foreach($recommended as $r): ?>
            <a href="/siteadam/offre-detail.php?id=<?= $r['id'] ?>" style="display:block;padding:.9rem;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:.75rem;transition:var(--transition)" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
              <div style="font-weight:600;margin-bottom:.25rem"><?= htmlspecialchars($r['title']) ?></div>
              <div style="font-size:.82rem;color:var(--muted)"><?= htmlspecialchars($r['company_name']) ?> · <?= $r['location'] ?? 'N/A' ?></div>
              <div class="job-tags" style="margin-top:.5rem">
                <span class="tag tag-type" style="font-size:.75rem"><?= $r['offer_type']==='stage'?'🎓 Stage':'💼 Emploi' ?></span>
                <?php if($r['remuneration']): ?><span class="tag tag-salary" style="font-size:.75rem">💰 <?= htmlspecialchars($r['remuneration']) ?></span><?php endif; ?>
              </div>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>
            <a href="/siteadam/offres.php" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:.5rem">Voir toutes les offres →</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
