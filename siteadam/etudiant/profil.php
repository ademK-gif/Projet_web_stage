<?php
$required_role = 'student';
require_once '../includes/auth.php';
$sp = $pdo->prepare("SELECT * FROM student_profiles WHERE user_id=?");
$sp->execute([$_SESSION['user_id']]);
$profile = $sp->fetch();

$success = '';
if($_SERVER['REQUEST_METHOD']==='POST') {
    $fields = ['first_name','last_name','bio','portfolio_url','sector','location'];
    $sets = []; $vals = [];
    foreach($fields as $f) { $sets[] = "$f=?"; $vals[] = trim($_POST[$f] ?? ''); }
    if(isset($_FILES['cv_file']) && $_FILES['cv_file']['error']===0) {
        $ext = pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION);
        $fname = 'cv_'.$profile['id'].'.'.$ext;
        move_uploaded_file($_FILES['cv_file']['tmp_name'], '../uploads/'.$fname);
        $sets[] = "cv_path=?"; $vals[] = $fname;
    }
    $vals[] = $profile['id'];
    $pdo->prepare("UPDATE student_profiles SET ".implode(',',$sets)." WHERE id=?")->execute($vals);
    $sp->execute([$_SESSION['user_id']]);
    $profile = $sp->fetch();
    $_SESSION['name'] = $profile['first_name'].' '.$profile['last_name'];
    $success = 'Profil mis à jour avec succès !';
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mon Profil - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_student.php'; ?>
  <div class="dash-content">
    <div class="dash-header"><h1> Mon Profil</h1></div>
    <div class="dash-body">
      <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

      <div class="profile-header">
        <div class="profile-avatar"><?= strtoupper(substr($profile['first_name'] ?? 'E', 0,1)) ?></div>
        <h2><?= htmlspecialchars(($profile['first_name']??'').' '.($profile['last_name']??'')) ?></h2>
        <p><?= htmlspecialchars($profile['sector'] ?? 'Secteur non défini') ?> · <?= htmlspecialchars($profile['location'] ?? 'Localisation non définie') ?></p>
      </div>

      <div class="card">
        <form method="POST" enctype="multipart/form-data">
          <div class="form-row">
            <div class="form-group"><label>Prénom</label><input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name']??'') ?>"></div>
            <div class="form-group"><label>Nom</label><input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name']??'') ?>"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Secteur</label>
              <select name="sector">
                <option value="">Choisir...</option>
                <?php foreach(['informatique','marketing','finance','design','rh','commerce','ingenierie'] as $s): ?>
                <option value="<?= $s ?>" <?= ($profile['sector']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label>Ville / Localisation</label><input type="text" name="location" value="<?= htmlspecialchars($profile['location']??'') ?>" placeholder="Tunis, Sfax..."></div>
          </div>
          <div class="form-group"><label>Bio / Présentation</label><textarea name="bio" rows="4" placeholder="Décrivez votre parcours, vos compétences et vos objectifs..."><?= htmlspecialchars($profile['bio']??'') ?></textarea></div>
          <div class="form-row">
            <div class="form-group"><label>URL Portfolio / LinkedIn</label><input type="url" name="portfolio_url" value="<?= htmlspecialchars($profile['portfolio_url']??'') ?>" placeholder="https://..."></div>
            <div class="form-group"><label>CV (PDF)</label><input type="file" name="cv_file" accept=".pdf,.doc,.docx">
              <?php if($profile['cv_path']): ?><small style="color:var(--success)">CV actuel : <?= htmlspecialchars($profile['cv_path']) ?></small><?php endif; ?>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body></html>
