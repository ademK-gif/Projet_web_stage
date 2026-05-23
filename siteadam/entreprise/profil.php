<?php
$required_role = 'company';
require_once '../includes/auth.php';
$cp = $pdo->prepare("SELECT * FROM company_profiles WHERE user_id=?");
$cp->execute([$_SESSION['user_id']]);
$company = $cp->fetch();

$success = '';
if($_SERVER['REQUEST_METHOD']==='POST') {
    $fields = ['company_name','description','website_url','sector','location'];
    $sets=[]; $vals=[];
    foreach($fields as $f) { $sets[]="$f=?"; $vals[]=trim($_POST[$f]??''); }
    if(isset($_FILES['logo']) && $_FILES['logo']['error']===0) {
        $ext = pathinfo($_FILES['logo']['name'],PATHINFO_EXTENSION);
        $fname='logo_'.$company['id'].'.'.$ext;
        move_uploaded_file($_FILES['logo']['tmp_name'],'../uploads/'.$fname);
        $sets[]="logo_path=?"; $vals[]=$fname;
    }
    $vals[]=$company['id'];
    $pdo->prepare("UPDATE company_profiles SET ".implode(',',$sets)." WHERE id=?")->execute($vals);
    $_SESSION['name'] = trim($_POST['company_name']);
    $cp->execute([$_SESSION['user_id']]); $company=$cp->fetch();
    $success='Profil mis à jour !';
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profil Entreprise - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_company.php'; ?>
  <div class="dash-content">
    <div class="dash-header"><h1>🏢 Profil Entreprise</h1></div>
    <div class="dash-body">
      <?php if($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>
      <div class="profile-header">
        <div class="profile-avatar"><?= strtoupper(substr($company['company_name'],0,1)) ?></div>
        <h2><?= htmlspecialchars($company['company_name']) ?></h2>
        <p><?= htmlspecialchars($company['sector']??'Secteur non défini') ?> · <?= htmlspecialchars($company['location']??'Localisation non définie') ?></p>
        <?php if($company['website_url']): ?><a href="<?= htmlspecialchars($company['website_url']) ?>" target="_blank" style="color:rgba(255,255,255,.8);font-size:.9rem;margin-top:.5rem;display:inline-block">🌐 <?= htmlspecialchars($company['website_url']) ?></a><?php endif; ?>
      </div>
      <div class="card" style="max-width:700px">
        <form method="POST" enctype="multipart/form-data">
          <div class="form-group"><label>Nom de l'entreprise</label><input type="text" name="company_name" value="<?= htmlspecialchars($company['company_name']) ?>" required></div>
          <div class="form-row">
            <div class="form-group"><label>Secteur d'activité</label>
              <select name="sector">
                <option value="">Choisir...</option>
                <?php foreach(['informatique','marketing','finance','design','rh','commerce','ingenierie'] as $s): ?>
                <option value="<?= $s ?>" <?= ($company['sector']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label>Ville / Localisation</label><input type="text" name="location" value="<?= htmlspecialchars($company['location']??'') ?>" placeholder="Tunis..."></div>
          </div>
          <div class="form-group"><label>Site web</label><input type="url" name="website_url" value="<?= htmlspecialchars($company['website_url']??'') ?>" placeholder="https://www.monentreprise.tn"></div>
          <div class="form-group"><label>Description de l'entreprise</label><textarea name="description" rows="5" placeholder="Présentez votre entreprise, votre culture, vos valeurs..."><?= htmlspecialchars($company['description']??'') ?></textarea></div>
          <div class="form-group"><label>Logo (image)</label><input type="file" name="logo" accept="image/*">
            <?php if($company['logo_path']): ?><small style="color:var(--success)">✅ Logo actuel : <?= htmlspecialchars($company['logo_path']) ?></small><?php endif; ?>
          </div>
          <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body></html>
