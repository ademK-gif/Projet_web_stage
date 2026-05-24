<?php
$required_role = 'company';
require_once '../includes/auth.php';
$cp = $pdo->prepare("SELECT id FROM company_profiles WHERE user_id=?");
$cp->execute([$_SESSION['user_id']]);
$cid = $cp->fetchColumn();

$success = $error = '';


if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_interview'])) {
    $aid = intval($_POST['application_id']);
    $dt  = $_POST['scheduled_at'] ?? '';
    $loc = trim($_POST['location_or_link'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    if(!$aid || !$dt || !$loc) { $error = 'Veuillez remplir tous les champs obligatoires.'; }
    else {
        $chk = $pdo->prepare("SELECT a.id FROM applications a JOIN job_offers jo ON a.offer_id=jo.id WHERE a.id=? AND jo.company_id=?");
        $chk->execute([$aid,$cid]);
        if($chk->fetch()) {
            $pdo->prepare("INSERT INTO interviews (application_id,scheduled_at,location_or_link,notes) VALUES (?,?,?,?)")->execute([$aid,$dt,$loc,$notes]);
            $pdo->prepare("UPDATE applications SET status='reviewed' WHERE id=?")->execute([$aid]);
            $success = 'Entretien planifié avec succès !';
        }
    }
}

if(isset($_GET['cancel'])) {
    $pdo->prepare("UPDATE interviews SET status='cancelled' WHERE id=? AND application_id IN (SELECT a.id FROM applications a JOIN job_offers jo ON a.offer_id=jo.id WHERE jo.company_id=?)")->execute([intval($_GET['cancel']),$cid]);
    header('Location: agenda.php'); exit;
}

$ints = $pdo->prepare("SELECT i.*,jo.title,sp.first_name,sp.last_name,a.id as app_id FROM interviews i JOIN applications a ON i.application_id=a.id JOIN job_offers jo ON a.offer_id=jo.id JOIN student_profiles sp ON a.student_id=sp.id WHERE jo.company_id=? ORDER BY i.scheduled_at DESC");
$ints->execute([$cid]);
$interviews = $ints->fetchAll();

$candidates = $pdo->prepare("SELECT a.id,sp.first_name,sp.last_name,jo.title FROM applications a JOIN job_offers jo ON a.offer_id=jo.id JOIN student_profiles sp ON a.student_id=sp.id WHERE jo.company_id=? AND a.status IN ('pending','reviewed') ORDER BY sp.last_name");
$candidates->execute([$cid]);
$cands = $candidates->fetchAll();
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Agenda Entretiens - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_company.php'; ?>
  <div class="dash-content">
    <div class="dash-header"><h1> Agenda des entretiens</h1></div>
    <div class="dash-body">
      <?php if($success): ?><div class="alert alert-success"> <?= $success ?></div><?php endif; ?>
      <?php if($error): ?><div class="alert alert-error"> <?= htmlspecialchars($error) ?></div><?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem">
        <div>
          <?php
          $upcoming = array_filter($interviews, fn($i) => $i['status']==='scheduled' && $i['scheduled_at'] >= date('Y-m-d H:i:s'));
          $past     = array_filter($interviews, fn($i) => $i['status']!=='scheduled' || $i['scheduled_at'] < date('Y-m-d H:i:s'));
          ?>
          <h3 style="margin-bottom:1rem;font-size:1rem"> Entretiens à venir (<?= count($upcoming) ?>)</h3>
          <?php if(empty($upcoming)): ?>
          <div class="card" style="text-align:center;padding:2rem;color:var(--muted);margin-bottom:1.5rem">
            <div style="font-size:2.5rem;margin-bottom:.75rem"></div>
            <p>Aucun entretien planifié prochainement.</p>
          </div>
          <?php else: ?>
          <?php foreach($upcoming as $i): ?>
          <div class="agenda-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start">
              <div>
                <div style="font-weight:700"><?= htmlspecialchars($i['first_name'].' '.$i['last_name']) ?></div>
                <div style="color:var(--muted);font-size:.85rem;margin:.2rem 0"><?= htmlspecialchars($i['title']) ?></div>
                <div style="color:var(--primary);font-weight:600;font-size:.9rem"> <?= date('d/m/Y à H:i',strtotime($i['scheduled_at'])) ?></div>
                <div style="color:var(--muted);font-size:.85rem;margin-top:.25rem"> <?= htmlspecialchars($i['location_or_link']) ?></div>
                <?php if($i['notes']): ?><div style="font-size:.82rem;color:var(--muted);margin-top:.35rem;font-style:italic"><?= htmlspecialchars($i['notes']) ?></div><?php endif; ?>
              </div>
              <a href="?cancel=<?= $i['id'] ?>" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;font-size:.8rem" onclick="return confirm('Annuler cet entretien ?')"> Annuler</a>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>

          <?php if(!empty($past)): ?>
          <h3 style="margin:1.5rem 0 1rem;font-size:1rem"> Historique (<?= count($past) ?>)</h3>
          <?php foreach($past as $i): ?>
          <div class="agenda-card" style="border-color:var(--border);opacity:.7">
            <div style="font-weight:600;font-size:.9rem"><?= htmlspecialchars($i['first_name'].' '.$i['last_name']) ?> — <?= htmlspecialchars($i['title']) ?></div>
            <div style="color:var(--muted);font-size:.82rem"><?= date('d/m/Y à H:i',strtotime($i['scheduled_at'])) ?> · <span class="badge badge-<?= $i['status'] ?>"><?= ucfirst($i['status']) ?></span></div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="card" style="height:fit-content;position:sticky;top:80px">
          <h3 style="margin-bottom:1.25rem"> Planifier un entretien</h3>
          <form method="POST">
            <input type="hidden" name="add_interview" value="1">
            <div class="form-group"><label>Candidat *</label>
              <select name="application_id" required>
                <option value="">Sélectionner...</option>
                <?php foreach($cands as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?> — <?= htmlspecialchars($c['title']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label> Date & heure *</label><input type="datetime-local" name="scheduled_at" required></div>
            <div class="form-group"><label> Lieu / Lien *</label><input type="text" name="location_or_link" placeholder="Bureau, Zoom, Google Meet..." required></div>
            <div class="form-group"><label> Notes (optionnel)</label><textarea name="notes" rows="3" placeholder="Instructions, documents à apporter..."></textarea></div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"> Planifier l'entretien</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body></html>
