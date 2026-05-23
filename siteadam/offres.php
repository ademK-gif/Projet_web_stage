<?php
require_once 'config/db.php';
$page_title = 'Offres de Stage & Emploi - JobLink';

// Filters
$q       = trim($_GET['q'] ?? '');
$sector  = $_GET['sector'] ?? '';
$type    = $_GET['type'] ?? '';
$loc     = trim($_GET['location'] ?? '');
$rem     = $_GET['remuneration'] ?? '';

$where = ["jo.status='active'"];
$params = [];

if($q)      { $where[] = "(jo.title LIKE ? OR jo.description LIKE ? OR cp.company_name LIKE ?)"; $params = array_merge($params, ["%$q%","%$q%","%$q%"]); }
if($sector) { $where[] = "jo.sector=?"; $params[] = $sector; }
if($type)   { $where[] = "jo.offer_type=?"; $params[] = $type; }
if($loc)    { $where[] = "jo.location LIKE ?"; $params[] = "%$loc%"; }

$sql = "SELECT jo.*, cp.company_name FROM job_offers jo JOIN company_profiles cp ON jo.company_id=cp.id WHERE ".implode(' AND ',$where)." ORDER BY jo.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$offres = $stmt->fetchAll();

include 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1>🔍 Offres de Stage & Emploi</h1>
    <p>Découvrez <?= count($offres) ?> opportunités adaptées à votre profil</p>
  </div>
</section>

<div class="section" style="padding-top:2rem">
  <div class="container">

    <!-- FILTERS -->
    <div class="filters-bar">
      <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
        <div class="filter-group" style="flex:2;min-width:200px">
          <label>Recherche</label>
          <input type="text" name="q" placeholder="Poste, entreprise..." value="<?= htmlspecialchars($q) ?>">
        </div>
        <div class="filter-group">
          <label>Secteur</label>
          <select name="sector">
            <option value="">Tous</option>
            <?php foreach(['informatique','marketing','finance','design','rh','commerce','ingenierie'] as $s): ?>
            <option value="<?= $s ?>" <?= $sector===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-group">
          <label>Type</label>
          <select name="type">
            <option value="">Tous</option>
            <option value="stage" <?= $type==='stage'?'selected':'' ?>>Stage</option>
            <option value="emploi" <?= $type==='emploi'?'selected':'' ?>>Emploi</option>
          </select>
        </div>
        <div class="filter-group">
          <label>Ville</label>
          <input type="text" name="location" placeholder="Tunis, Sfax..." value="<?= htmlspecialchars($loc) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="offres.php" class="btn btn-outline">Réinitialiser</a>
      </form>
    </div>

    <!-- RESULTS -->
    <?php if(empty($offres)): ?>
    <div class="empty-state">
      <div class="empty-icon">😕</div>
      <h3>Aucune offre trouvée</h3>
      <p>Modifiez vos critères de recherche ou revenez plus tard.</p>
      <a href="offres.php" class="btn btn-primary" style="margin-top:1rem">Voir toutes les offres</a>
    </div>
    <?php else: ?>
    <div style="margin-bottom:1.5rem;color:var(--muted);font-size:.9rem"><strong><?= count($offres) ?></strong> offre(s) trouvée(s)</div>
    <div class="jobs-grid">
      <?php foreach($offres as $o): ?>
      <a href="offre-detail.php?id=<?= $o['id'] ?>" class="job-card">
        <div class="job-card-header">
          <div class="company-logo"><?= strtoupper(substr($o['company_name'],0,1)) ?></div>
          <div>
            <div class="job-title"><?= htmlspecialchars($o['title']) ?></div>
            <div class="company-name"><?= htmlspecialchars($o['company_name']) ?></div>
          </div>
        </div>
        <div class="job-tags">
          <span class="tag tag-type"><?= $o['offer_type']==='stage'?'🎓 Stage':'💼 Emploi' ?></span>
          <?php if($o['location']): ?><span class="tag tag-location">📍 <?= htmlspecialchars($o['location']) ?></span><?php endif; ?>
          <?php if($o['remuneration']): ?><span class="tag tag-salary">💰 <?= htmlspecialchars($o['remuneration']) ?></span><?php endif; ?>
          <?php if($o['duration']): ?><span class="tag tag-duration">⏱ <?= htmlspecialchars($o['duration']) ?></span><?php endif; ?>
        </div>
        <p style="font-size:.88rem;color:var(--muted);line-height:1.5"><?= htmlspecialchars(substr($o['description'],0,100)) ?>...</p>
        <div class="job-card-footer">
          <span class="job-date"><?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
          <span class="btn btn-primary btn-sm">Voir →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
