<?php
$required_role = 'company';
require_once '../includes/auth.php';

$cp = $pdo->prepare("SELECT * FROM company_profiles WHERE user_id=?");
$cp->execute([$_SESSION['user_id']]);
$company = $cp->fetch();
$cid = $company['id'];

$total_offers  = $pdo->prepare("SELECT COUNT(*) FROM job_offers WHERE company_id=?"); $total_offers->execute([$cid]);
$active_offers = $pdo->prepare("SELECT COUNT(*) FROM job_offers WHERE company_id=? AND status='active'"); $active_offers->execute([$cid]);
$total_apps    = $pdo->prepare("SELECT COUNT(*) FROM applications a JOIN job_offers jo ON a.offer_id=jo.id WHERE jo.company_id=?"); $total_apps->execute([$cid]);
$new_apps      = $pdo->prepare("SELECT COUNT(*) FROM applications a JOIN job_offers jo ON a.offer_id=jo.id WHERE jo.company_id=? AND a.status='pending'"); $new_apps->execute([$cid]);

$recent = $pdo->prepare("SELECT a.*,jo.title,sp.first_name,sp.last_name,sp.sector FROM applications a JOIN job_offers jo ON a.offer_id=jo.id JOIN student_profiles sp ON a.student_id=sp.id WHERE jo.company_id=? ORDER BY a.applied_at DESC LIMIT 6");
$recent->execute([$cid]);
$recent_apps = $recent->fetchAll();

$upcoming = $pdo->prepare("SELECT i.*,jo.title,sp.first_name,sp.last_name FROM interviews i JOIN applications a ON i.application_id=a.id JOIN job_offers jo ON a.offer_id=jo.id JOIN student_profiles sp ON a.student_id=sp.id WHERE jo.company_id=? AND i.status='scheduled' AND i.scheduled_at >= NOW() ORDER BY i.scheduled_at LIMIT 5");
$upcoming->execute([$cid]);
$upcoming_interviews = $upcoming->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tableau de bord Entreprise - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_company.php'; ?>
  <div class="dash-content">
    <div class="dash-header">
      <h1> <?= htmlspecialchars($company['company_name']) ?></h1>
      <a href="/siteadam/entreprise/nouvelle-offre.php" class="btn btn-primary btn-sm"> Publier une offre</a>
    </div>
    <div class="dash-body">

      <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon blue"></div><div><div class="stat-val"><?= $total_offers->fetchColumn() ?></div><div class="stat-lbl">Offres publiées</div></div></div>
        <div class="stat-card"><div class="stat-icon green"></div><div><div class="stat-val"><?= $active_offers->fetchColumn() ?></div><div class="stat-lbl">Offres actives</div></div></div>
        <div class="stat-card"><div class="stat-icon purple"></div><div><div class="stat-val"><?= $total_apps->fetchColumn() ?></div><div class="stat-lbl">Total candidatures</div></div></div>
        <div class="stat-card"><div class="stat-icon orange"></div><div><div class="stat-val"><?= $new_apps->fetchColumn() ?></div><div class="stat-lbl">Nouvelles candidatures</div></div></div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem">

        <div class="table-wrap">
          <div class="table-head">
            <h3>Dernières candidatures</h3>
            <a href="/siteadam/entreprise/candidatures.php" class="btn btn-outline btn-sm">Voir tout</a>
          </div>
          <?php if(empty($recent_apps)): ?>
          <div class="empty-state" style="padding:2rem">
            <div class="empty-icon"></div>
            <h3>Aucune candidature</h3>
            <p>Publiez des offres pour recevoir des candidatures.</p>
            <a href="/siteadam/entreprise/nouvelle-offre.php" class="btn btn-primary btn-sm" style="margin-top:1rem">Publier une offre</a>
          </div>
          <?php else: ?>
          <table>
            <thead><tr><th>Candidat</th><th>Poste</th><th>Secteur</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($recent_apps as $a): ?>
            <tr>
              <td><strong><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></strong></td>
              <td><?= htmlspecialchars($a['title']) ?></td>
              <td style="font-size:.82rem;color:var(--muted)"><?= htmlspecialchars($a['sector']??'-') ?></td>
              <td><span class="badge badge-<?= $a['status'] ?>"><?= ['pending'=>'⏳ Nouveau','reviewed'=>'👀 En examen','accepted'=>'✅ Accepté','rejected'=>'❌ Refusé'][$a['status']] ?></span></td>
              <td style="font-size:.82rem;color:var(--muted)"><?= date('d/m/Y',strtotime($a['applied_at'])) ?></td>
              <td><a href="/siteadam/entreprise/candidatures.php?app=<?= $a['id'] ?>" class="btn btn-outline btn-sm">Gérer</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>

        <div class="card">
          <h3 style="margin-bottom:1rem"> Prochains entretiens</h3>
          <?php if(empty($upcoming_interviews)): ?>
          <div style="text-align:center;padding:1.5rem 0;color:var(--muted)">
            <div style="font-size:2.5rem;margin-bottom:.75rem"></div>
            <p style="font-size:.88rem">Aucun entretien planifié.</p>
            <a href="/siteadam/entreprise/agenda.php" class="btn btn-outline btn-sm" style="margin-top:.75rem">Planifier</a>
          </div>
          <?php else: ?>
          <?php foreach($upcoming_interviews as $i): ?>
          <div class="agenda-card">
            <div style="font-weight:700;font-size:.92rem"><?= htmlspecialchars($i['first_name'].' '.$i['last_name']) ?></div>
            <div style="color:var(--muted);font-size:.82rem;margin:.25rem 0"><?= htmlspecialchars($i['title']) ?></div>
            <div style="color:var(--primary);font-size:.85rem;font-weight:600"><?= date('d/m/Y à H:i',strtotime($i['scheduled_at'])) ?></div>
            <div style="color:var(--muted);font-size:.82rem"> <?= htmlspecialchars($i['location_or_link']) ?></div>
          </div>
          <?php endforeach; ?>
          <a href="/siteadam/entreprise/agenda.php" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:.5rem">Voir l'agenda complet</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body></html>
