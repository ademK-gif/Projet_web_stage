<?php
$required_role = 'company';
require_once '../includes/auth.php';
$cp = $pdo->prepare("SELECT id FROM company_profiles WHERE user_id=?");
$cp->execute([$_SESSION['user_id']]);
$cid = $cp->fetchColumn();

$id = intval($_GET['id'] ?? 0);
$offre = $pdo->prepare("SELECT * FROM job_offers WHERE id=? AND company_id=?");
$offre->execute([$id,$cid]);
$o = $offre->fetch();
if(!$o) { header('Location: offres.php'); exit; }

$success = $error = '';
if($_SERVER['REQUEST_METHOD']==='POST') {
    $title = trim($_POST['title']??'');
    $desc  = trim($_POST['description']??'');
    if(!$title||!$desc) { $error='Titre et description obligatoires.'; }
    else {
        $pdo->prepare("UPDATE job_offers SET title=?,description=?,offer_type=?,sector=?,location=?,duration=?,remuneration=? WHERE id=? AND company_id=?")
            ->execute([$title,$desc,$_POST['offer_type']??'stage',$_POST['sector']??'',$_POST['location']??'',$_POST['duration']??'',$_POST['remuneration']??'',$id,$cid]);
        header('Location: offres.php?updated=1'); exit;
    }
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Modifier l'offre - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_company.php'; ?>
  <div class="dash-content">
    <div class="dash-header"><h1>✏️ Modifier l'offre</h1><a href="offres.php" class="btn btn-outline btn-sm">← Retour</a></div>
    <div class="dash-body">
      <?php if($error): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <div class="card" style="max-width:780px">
        <form method="POST">
          <div class="form-group"><label>Titre du poste *</label><input type="text" name="title" value="<?= htmlspecialchars($o['title']) ?>" required></div>
          <div class="form-row">
            <div class="form-group"><label>Type d'offre</label>
              <select name="offer_type">
                <option value="stage" <?= $o['offer_type']==='stage'?'selected':'' ?>>🎓 Stage</option>
                <option value="emploi" <?= $o['offer_type']==='emploi'?'selected':'' ?>>💼 Emploi</option>
              </select>
            </div>
            <div class="form-group"><label>Secteur</label>
              <select name="sector">
                <option value="">Choisir...</option>
                <?php foreach(['informatique','marketing','finance','design','rh','commerce','ingenierie'] as $s): ?>
                <option value="<?= $s ?>" <?= ($o['sector']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>📍 Localisation</label><input type="text" name="location" value="<?= htmlspecialchars($o['location']??'') ?>"></div>
            <div class="form-group"><label>⏱ Durée</label><input type="text" name="duration" value="<?= htmlspecialchars($o['duration']??'') ?>"></div>
          </div>
          <div class="form-group"><label>💰 Rémunération</label><input type="text" name="remuneration" value="<?= htmlspecialchars($o['remuneration']??'') ?>"></div>
          <div class="form-group"><label>Description *</label><textarea name="description" rows="8" required><?= htmlspecialchars($o['description']) ?></textarea></div>
          <div style="display:flex;gap:1rem">
            <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
            <a href="offres.php" class="btn btn-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body></html>
