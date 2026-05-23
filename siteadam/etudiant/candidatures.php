<?php
$required_role = 'student';
require_once '../includes/auth.php';
$sp = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id=?");
$sp->execute([$_SESSION['user_id']]);
$profile = $sp->fetch();
$sid = $profile['id'];

$apps = $pdo->prepare("SELECT a.*,jo.title,jo.offer_type,jo.location,cp.company_name FROM applications a JOIN job_offers jo ON a.offer_id=jo.id JOIN company_profiles cp ON jo.company_id=cp.id WHERE a.student_id=? ORDER BY a.applied_at DESC");
$apps->execute([$sid]);
$applications = $apps->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mes Candidatures - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_student.php'; ?>
  <div class="dash-content">
    <div class="dash-header"><h1>📋 Mes Candidatures</h1>
      <div style="color:var(--muted);font-size:.9rem"><?= count($applications) ?> candidature(s) au total</div>
    </div>
    <div class="dash-body">
      <!-- Status Summary -->
      <?php
        $counts = ['pending'=>0,'reviewed'=>0,'accepted'=>0,'rejected'=>0];
        foreach($applications as $a) $counts[$a['status']]++;
      ?>
      <div class="stats-grid" style="margin-bottom:1.5rem">
        <div class="stat-card"><div class="stat-icon orange">⏳</div><div><div class="stat-val"><?= $counts['pending'] ?></div><div class="stat-lbl">En attente</div></div></div>
        <div class="stat-card"><div class="stat-icon blue">👀</div><div><div class="stat-val"><?= $counts['reviewed'] ?></div><div class="stat-lbl">En cours d'examen</div></div></div>
        <div class="stat-card"><div class="stat-icon green">✅</div><div><div class="stat-val"><?= $counts['accepted'] ?></div><div class="stat-lbl">Acceptées</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#fee2e2;color:#991b1b">❌</div><div><div class="stat-val"><?= $counts['rejected'] ?></div><div class="stat-lbl">Refusées</div></div></div>
      </div>

      <div class="table-wrap">
        <div class="table-head"><h3>Toutes mes candidatures</h3></div>
        <?php if(empty($applications)): ?>
        <div class="empty-state"><div class="empty-icon">📭</div><h3>Aucune candidature</h3><p>Postulez à des offres pour les voir ici.</p><a href="/siteadam/offres.php" class="btn btn-primary" style="margin-top:1rem">🔍 Parcourir les offres</a></div>
        <?php else: ?>
        <table>
          <thead><tr><th>Poste</th><th>Entreprise</th><th>Type</th><th>Lieu</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
          <?php foreach($applications as $a): ?>
          <tr>
            <td><strong><?= htmlspecialchars($a['title']) ?></strong></td>
            <td><?= htmlspecialchars($a['company_name']) ?></td>
            <td><span class="tag tag-type" style="font-size:.75rem"><?= $a['offer_type']==='stage'?'🎓 Stage':'💼 Emploi' ?></span></td>
            <td style="color:var(--muted);font-size:.85rem"><?= htmlspecialchars($a['location'] ?? '-') ?></td>
            <td><span class="badge badge-<?= $a['status'] ?>"><?= ['pending'=>'⏳ En attente','reviewed'=>'👀 En examen','accepted'=>'✅ Accepté','rejected'=>'❌ Refusé'][$a['status']] ?></span></td>
            <td style="color:var(--muted);font-size:.82rem"><?= date('d/m/Y',strtotime($a['applied_at'])) ?></td>
            <td><a href="/siteadam/offre-detail.php?id=<?= $a['offer_id'] ?>" class="btn btn-outline btn-sm">Voir</a></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body></html>
