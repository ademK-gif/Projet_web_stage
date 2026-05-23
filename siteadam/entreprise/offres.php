<?php
$required_role = 'company';
require_once '../includes/auth.php';
$cp = $pdo->prepare("SELECT id FROM company_profiles WHERE user_id=?");
$cp->execute([$_SESSION['user_id']]);
$cid = $cp->fetchColumn();

// Toggle status
if(isset($_GET['toggle'])) {
    $oid = intval($_GET['toggle']);
    $cur = $pdo->prepare("SELECT status FROM job_offers WHERE id=? AND company_id=?");
    $cur->execute([$oid,$cid]);
    $cur_status = $cur->fetchColumn();
    $new = $cur_status==='active' ? 'closed' : 'active';
    $pdo->prepare("UPDATE job_offers SET status=? WHERE id=? AND company_id=?")->execute([$new,$oid,$cid]);
    header('Location: offres.php'); exit;
}
// Delete
if(isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM job_offers WHERE id=? AND company_id=?")->execute([intval($_GET['delete']),$cid]);
    header('Location: offres.php'); exit;
}

$offers = $pdo->prepare("SELECT jo.*,(SELECT COUNT(*) FROM applications WHERE offer_id=jo.id) as nb_apps FROM job_offers jo WHERE company_id=? ORDER BY created_at DESC");
$offers->execute([$cid]);
$all_offers = $offers->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mes Offres - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_company.php'; ?>
  <div class="dash-content">
    <div class="dash-header">
      <h1>📢 Mes offres publiées</h1>
      <a href="nouvelle-offre.php" class="btn btn-primary btn-sm">➕ Nouvelle offre</a>
    </div>
    <div class="dash-body">
      <?php if(empty($all_offers)): ?>
      <div class="empty-state">
        <div class="empty-icon">📋</div>
        <h3>Aucune offre publiée</h3>
        <p>Commencez par publier votre première offre de stage ou d'emploi.</p>
        <a href="nouvelle-offre.php" class="btn btn-primary" style="margin-top:1rem">➕ Publier une offre</a>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Titre</th><th>Type</th><th>Lieu</th><th>Candidatures</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach($all_offers as $o): ?>
          <tr>
            <td><strong><?= htmlspecialchars($o['title']) ?></strong><br><small style="color:var(--muted)"><?= htmlspecialchars($o['sector']??'') ?></small></td>
            <td><span class="tag tag-type" style="font-size:.75rem"><?= $o['offer_type']==='stage'?'🎓 Stage':'💼 Emploi' ?></span></td>
            <td style="font-size:.85rem"><?= htmlspecialchars($o['location']??'-') ?></td>
            <td><a href="candidatures.php?offer=<?= $o['id'] ?>" style="color:var(--primary);font-weight:700"><?= $o['nb_apps'] ?> candidat(s)</a></td>
            <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status']==='active'?'✅ Active':'🔒 Fermée' ?></span></td>
            <td style="font-size:.82rem;color:var(--muted)"><?= date('d/m/Y',strtotime($o['created_at'])) ?></td>
            <td>
              <div style="display:flex;gap:.5rem">
                <a href="modifier-offre.php?id=<?= $o['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
                <a href="?toggle=<?= $o['id'] ?>" class="btn btn-sm" style="background:<?= $o['status']==='active'?'#fef9c3;color:#854d0e':'#dcfce7;color:#166534' ?>"><?= $o['status']==='active'?'🔒':'✅' ?></a>
                <a href="?delete=<?= $o['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette offre ?')">🗑</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body></html>
