<?php
session_start();
require_once 'config/db.php';
$id = intval($_GET['id'] ?? 0);
$offre = $pdo->prepare("SELECT jo.*,cp.company_name,cp.description as company_desc,cp.website_url,cp.sector as company_sector,cp.location as company_loc FROM job_offers jo JOIN company_profiles cp ON jo.company_id=cp.id WHERE jo.id=?");
$offre->execute([$id]);
$o = $offre->fetch();
if(!$o) { header('Location: /siteadam/offres.php'); exit; }

$already = false;
if(isset($_SESSION['user_id']) && $_SESSION['role']==='student') {
    $sp = $pdo->prepare("SELECT id FROM student_profiles WHERE user_id=?");
    $sp->execute([$_SESSION['user_id']]);
    $sid = $sp->fetchColumn();
    $chk = $pdo->prepare("SELECT id FROM applications WHERE offer_id=? AND student_id=?");
    $chk->execute([$id, $sid]);
    $already = (bool)$chk->fetch();
}

$success = '';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_SESSION['user_id']) && $_SESSION['role']==='student' && !$already) {
    $letter = trim($_POST['cover_letter'] ?? '');
    $stmt = $pdo->prepare("INSERT INTO applications (offer_id,student_id,cover_letter) VALUES (?,?,?)");
    $stmt->execute([$id, $sid, $letter]);
    $success = 'Candidature envoyée avec succès !';
    $already = true;
}

$page_title = htmlspecialchars($o['title']).' - JobLink';
include 'includes/header.php';
?>

<div class="offre-hero">
  <div class="container">
    <div class="company-logo-lg"><?= strtoupper(substr($o['company_name'],0,1)) ?></div>
    <div class="job-tags" style="margin-bottom:1rem">
      <span class="tag tag-type"><?= $o['offer_type']==='stage'?' Stage':' Emploi' ?></span>
      <?php if($o['location']): ?><span class="tag tag-location"> <?= htmlspecialchars($o['location']) ?></span><?php endif; ?>
      <?php if($o['remuneration']): ?><span class="tag tag-salary"> <?= htmlspecialchars($o['remuneration']) ?></span><?php endif; ?>
      <?php if($o['duration']): ?><span class="tag tag-duration"> <?= htmlspecialchars($o['duration']) ?></span><?php endif; ?>
    </div>
    <h1 style="font-size:2rem;margin-bottom:.5rem"><?= htmlspecialchars($o['title']) ?></h1>
    <p style="color:var(--muted);font-size:1.1rem"><?= htmlspecialchars($o['company_name']) ?> · Publiée le <?= date('d/m/Y', strtotime($o['created_at'])) ?></p>
  </div>
</div>

<div class="container">
  <div class="offre-layout">
    <div>
      <div class="card" style="margin-bottom:1.5rem">
        <h3 style="margin-bottom:1rem"> Description du poste</h3>
        <div style="line-height:1.8;color:var(--text)"><?= nl2br(htmlspecialchars($o['description'])) ?></div>
      </div>

      <?php if($success): ?>
        <div class="alert alert-success"> <?= $success ?></div>
      <?php elseif($already): ?>
        <div class="alert" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe"> Vous avez déjà postulé à cette offre.</div>
      <?php elseif(!isset($_SESSION['user_id'])): ?>
        <div class="card" style="text-align:center;padding:2rem">
          <div style="font-size:3rem;margin-bottom:1rem"></div>
          <h3>Connectez-vous pour postuler</h3>
          <p style="color:var(--muted);margin:.75rem 0 1.5rem">Créez un compte gratuit ou connectez-vous pour envoyer votre candidature.</p>
          <div style="display:flex;gap:1rem;justify-content:center">
            <a href="/siteadam/login.php" class="btn btn-primary">Se connecter</a>
            <a href="/siteadam/register.php?role=student" class="btn btn-secondary">S'inscrire</a>
          </div>
        </div>
      <?php elseif($_SESSION['role']==='student'): ?>
        <div class="card">
          <h3 style="margin-bottom:1.25rem"> Postuler à cette offre</h3>
          <form method="POST">
            <div class="form-group">
              <label>Lettre de motivation</label>
              <textarea name="cover_letter" placeholder="Décrivez votre motivation et vos compétences en lien avec cette offre..." rows="6"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer ma candidature 🚀</button>
          </form>
        </div>
      <?php endif; ?>
    </div>

    <div>
      <div class="offre-sidebar-card" style="margin-bottom:1.5rem">
        <h3 style="margin-bottom:1.25rem"> Détails de l'offre</h3>
        <ul class="info-list">
          <li><span class="info-icon"></span><div><strong>Type</strong><br><?= ucfirst($o['offer_type']) ?></div></li>
          <?php if($o['location']): ?><li><span class="info-icon"></span><div><strong>Lieu</strong><br><?= htmlspecialchars($o['location']) ?></div></li><?php endif; ?>
          <?php if($o['duration']): ?><li><span class="info-icon"></span><div><strong>Durée</strong><br><?= htmlspecialchars($o['duration']) ?></div></li><?php endif; ?>
          <?php if($o['remuneration']): ?><li><span class="info-icon"></span><div><strong>Rémunération</strong><br><?= htmlspecialchars($o['remuneration']) ?></div></li><?php endif; ?>
          <?php if($o['sector']): ?><li><span class="info-icon"></span><div><strong>Secteur</strong><br><?= htmlspecialchars($o['sector']) ?></div></li><?php endif; ?>
        </ul>
      </div>
      <div class="offre-sidebar-card">
        <h3 style="margin-bottom:1rem"> À propos de l'entreprise</h3>
        <div style="font-weight:700;margin-bottom:.35rem"><?= htmlspecialchars($o['company_name']) ?></div>
        <?php if($o['company_desc']): ?><p style="font-size:.88rem;color:var(--muted);line-height:1.6;margin-bottom:.75rem"><?= htmlspecialchars(substr($o['company_desc'],0,200)) ?>...</p><?php endif; ?>
        <?php if($o['website_url']): ?><a href="<?= htmlspecialchars($o['website_url']) ?>" target="_blank" class="btn btn-outline btn-sm"> Visiter le site</a><?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
