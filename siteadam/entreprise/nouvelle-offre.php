<?php
$required_role = 'company';
require_once '../includes/auth.php';
$cp = $pdo->prepare("SELECT id FROM company_profiles WHERE user_id=?");
$cp->execute([$_SESSION['user_id']]);
$cid = $cp->fetchColumn();

$success = $error = '';
if($_SERVER['REQUEST_METHOD']==='POST') {
    $title = trim($_POST['title']??'');
    $desc  = trim($_POST['description']??'');
    $type  = $_POST['offer_type']??'stage';
    if(!$title || !$desc) { $error = 'Le titre et la description sont obligatoires.'; }
    else {
        $pdo->prepare("INSERT INTO job_offers (company_id,title,description,offer_type,sector,location,duration,remuneration) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$cid,$title,$desc,$type,$_POST['sector']??'',$_POST['location']??'',$_POST['duration']??'',$_POST['remuneration']??'']);
        header('Location: offres.php?created=1'); exit;
    }
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Publier une offre - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_company.php'; ?>
  <div class="dash-content">
    <div class="dash-header"><h1>➕ Publier une nouvelle offre</h1></div>
    <div class="dash-body">
      <div class="card" style="max-width:780px">
        <?php if($error): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
          <div class="form-group"><label>Titre du poste *</label><input type="text" name="title" placeholder="Ex: Développeur Web Full-Stack" required value="<?= htmlspecialchars($_POST['title']??'') ?>"></div>

          <div class="form-row">
            <div class="form-group"><label>Type d'offre *</label>
              <select name="offer_type">
                <option value="stage" <?= ($_POST['offer_type']??'')==='stage'?'selected':'' ?>>🎓 Stage</option>
                <option value="emploi" <?= ($_POST['offer_type']??'')==='emploi'?'selected':'' ?>>💼 Emploi</option>
              </select>
            </div>
            <div class="form-group"><label>Secteur</label>
              <select name="sector">
                <option value="">Choisir...</option>
                <?php foreach(['informatique','marketing','finance','design','rh','commerce','ingenierie'] as $s): ?>
                <option value="<?= $s ?>" <?= ($_POST['sector']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group"><label>📍 Localisation</label><input type="text" name="location" placeholder="Tunis, Sfax, Télétravail..." value="<?= htmlspecialchars($_POST['location']??'') ?>"></div>
            <div class="form-group"><label>⏱ Durée</label><input type="text" name="duration" placeholder="Ex: 3 mois, CDI, CDD 1 an..." value="<?= htmlspecialchars($_POST['duration']??'') ?>"></div>
          </div>

          <div class="form-group"><label>💰 Rémunération</label><input type="text" name="remuneration" placeholder="Ex: 400 DT/mois, À négocier..." value="<?= htmlspecialchars($_POST['remuneration']??'') ?>"></div>

          <div class="form-group"><label>Description du poste *</label>
            <textarea name="description" rows="8" placeholder="Décrivez le poste, les missions, les compétences requises et les avantages..." required><?= htmlspecialchars($_POST['description']??'') ?></textarea>
          </div>

          <div style="display:flex;gap:1rem">
            <button type="submit" class="btn btn-primary">🚀 Publier l'offre</button>
            <a href="offres.php" class="btn btn-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body></html>
