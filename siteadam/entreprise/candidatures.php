<?php
$required_role = 'company';
require_once '../includes/auth.php';
$cp = $pdo->prepare("SELECT id FROM company_profiles WHERE user_id=?");
$cp->execute([$_SESSION['user_id']]);
$cid = $cp->fetchColumn();

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['app_id'])) {
    $pdo->prepare("UPDATE applications SET status=? WHERE id=? AND offer_id IN (SELECT id FROM job_offers WHERE company_id=?)")
        ->execute([$_POST['status'], $_POST['app_id'], $cid]);
    header('Location: candidatures.php'.(!empty($_GET['offer'])?'?offer='.$_GET['offer']:'')); exit;
}

$filter_offer = intval($_GET['offer'] ?? 0);
$filter_status = $_GET['status'] ?? '';
$active_app = intval($_GET['app'] ?? 0);

$where = ["jo.company_id=?"];
$params = [$cid];
if($filter_offer) { $where[] = "jo.id=?"; $params[] = $filter_offer; }
if($filter_status) { $where[] = "a.status=?"; $params[] = $filter_status; }

$stmt = $pdo->prepare("SELECT a.*,jo.title as offer_title,sp.first_name,sp.last_name,sp.bio,sp.sector,sp.location,sp.cv_path,sp.portfolio_url FROM applications a JOIN job_offers jo ON a.offer_id=jo.id JOIN student_profiles sp ON a.student_id=sp.id WHERE ".implode(' AND ',$where)." ORDER BY a.applied_at DESC");
$stmt->execute($params);
$apps = $stmt->fetchAll();

$offers_list = $pdo->prepare("SELECT id,title FROM job_offers WHERE company_id=? ORDER BY title");
$offers_list->execute([$cid]);
$offers_select = $offers_list->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Candidatures - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_company.php'; ?>
  <div class="dash-content">
    <div class="dash-header"><h1> Gestion des candidatures</h1><span style="color:var(--muted)"><?= count($apps) ?> candidature(s)</span></div>
    <div class="dash-body">

      <div class="filters-bar" style="margin-bottom:1.5rem">
        <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
          <div class="filter-group">
            <label>Filtrer par offre</label>
            <select name="offer" onchange="this.form.submit()">
              <option value="">Toutes les offres</option>
              <?php foreach($offers_select as $o): ?>
              <option value="<?= $o['id'] ?>" <?= $filter_offer==$o['id']?'selected':'' ?>><?= htmlspecialchars($o['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label>Statut</label>
            <select name="status" onchange="this.form.submit()">
              <option value="">Tous</option>
              <option value="pending" <?= $filter_status==='pending'?'selected':'' ?>> En attente</option>
              <option value="reviewed" <?= $filter_status==='reviewed'?'selected':'' ?>> En examen</option>
              <option value="accepted" <?= $filter_status==='accepted'?'selected':'' ?>> Accepté</option>
              <option value="rejected" <?= $filter_status==='rejected'?'selected':'' ?>> Refusé</option>
            </select>
          </div>
          <a href="candidatures.php" class="btn btn-outline btn-sm">Réinitialiser</a>
        </form>
      </div>

      <?php if(empty($apps)): ?>
      <div class="empty-state"><div class="empty-icon"></div><h3>Aucune candidature</h3><p>Vos candidatures apparaîtront ici.</p></div>
      <?php else: ?>
      <div style="display:grid;grid-template-columns:1fr 380px;gap:1.5rem">
        <div class="table-wrap">
          <table>
            <thead><tr><th>Candidat</th><th>Poste</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($apps as $a): ?>
            <tr style="<?= $active_app==$a['id']?'background:#f0f9ff':'' ?>">
              <td>
                <div style="font-weight:600"><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></div>
                <div style="font-size:.8rem;color:var(--muted)"><?= htmlspecialchars($a['sector']??'') ?></div>
              </td>
              <td style="font-size:.88rem"><?= htmlspecialchars($a['offer_title']) ?></td>
              <td><span class="badge badge-<?= $a['status'] ?>"><?= ['pending'=>' Nouveau','reviewed'=>' En examen','accepted'=>' Accepté','rejected'=>' Refusé'][$a['status']] ?></span></td>
              <td style="font-size:.82rem;color:var(--muted)"><?= date('d/m/Y',strtotime($a['applied_at'])) ?></td>
              <td><a href="?app=<?= $a['id'] ?><?= $filter_offer?'&offer='.$filter_offer:'' ?>" class="btn btn-outline btn-sm">Voir</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <?php $sel = null; foreach($apps as $a) if($a['id']==$active_app) { $sel=$a; break; } ?>
        <?php if($sel): ?>
        <div class="card">
          <div class="profile-avatar" style="width:60px;height:60px;font-size:1.5rem;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;border-radius:50%;margin-bottom:1rem">
            <?= strtoupper(substr($sel['first_name'],0,1)) ?>
          </div>
          <h3><?= htmlspecialchars($sel['first_name'].' '.$sel['last_name']) ?></h3>
          <p style="color:var(--muted);font-size:.88rem;margin:.25rem 0"><?= htmlspecialchars($sel['sector']??'') ?> · <?= htmlspecialchars($sel['location']??'') ?></p>

          <?php if($sel['bio']): ?>
          <div style="margin:1rem 0;padding:1rem;background:#f8fafc;border-radius:var(--radius);font-size:.88rem;line-height:1.6"><?= nl2br(htmlspecialchars(substr($sel['bio'],0,300))) ?></div>
          <?php endif; ?>

          <?php if($sel['cover_letter']): ?>
          <h4 style="margin-bottom:.5rem;font-size:.9rem"> Lettre de motivation</h4>
          <div style="padding:1rem;background:#fafafa;border-radius:var(--radius);font-size:.85rem;line-height:1.7;margin-bottom:1rem;border:1px solid var(--border)"><?= nl2br(htmlspecialchars($sel['cover_letter'])) ?></div>
          <?php endif; ?>

          <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem">
            <?php if($sel['cv_path']): ?><a href="/siteadam/uploads/<?= htmlspecialchars($sel['cv_path']) ?>" class="btn btn-outline btn-sm" target="_blank"> CV</a><?php endif; ?>
            <?php if($sel['portfolio_url']): ?><a href="<?= htmlspecialchars($sel['portfolio_url']) ?>" class="btn btn-outline btn-sm" target="_blank"> Portfolio</a><?php endif; ?>
            <a href="/siteadam/entreprise/messages.php?with=<?= $pdo->query("SELECT user_id FROM student_profiles WHERE id=".$sel['student_id'])->fetchColumn() ?>" class="btn btn-outline btn-sm">Message</a>
          </div>

          <form method="POST">
            <input type="hidden" name="app_id" value="<?= $sel['id'] ?>">
            <div class="form-group"><label>Changer le statut</label>
              <select name="status">
                <option value="pending" <?= $sel['status']==='pending'?'selected':'' ?>> En attente</option>
                <option value="reviewed" <?= $sel['status']==='reviewed'?'selected':'' ?>>En examen</option>
                <option value="accepted" <?= $sel['status']==='accepted'?'selected':'' ?>>Accepter</option>
                <option value="rejected" <?= $sel['status']==='rejected'?'selected':'' ?>> Refuser</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Mettre à jour</button>
          </form>
        </div>
        <?php else: ?>
        <div class="card" style="display:flex;align-items:center;justify-content:center;text-align:center;color:var(--muted)">
          <div><div style="font-size:3rem;margin-bottom:1rem"></div><p>Sélectionnez une candidature<br>pour voir les détails.</p></div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</body></html>
