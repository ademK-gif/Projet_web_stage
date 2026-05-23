<?php
require_once 'config/db.php';
$page_title = 'Entreprises partenaires - JobLink';
$q = trim($_GET['q'] ?? '');
$sector = $_GET['sector'] ?? '';

$where = ['1=1'];
$params = [];
if($q) { $where[] = "cp.company_name LIKE ?"; $params[] = "%$q%"; }
if($sector) { $where[] = "cp.sector=?"; $params[] = $sector; }

$stmt = $pdo->prepare("SELECT cp.*, (SELECT COUNT(*) FROM job_offers jo WHERE jo.company_id=cp.id AND jo.status='active') as nb_offres FROM company_profiles cp WHERE ".implode(' AND ',$where)." ORDER BY cp.company_name");
$stmt->execute($params);
$companies = $stmt->fetchAll();

include 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1>🏢 Entreprises partenaires</h1>
    <p><?= count($companies) ?> entreprises recrutent actuellement sur JobLink</p>
  </div>
</section>

<div class="section" style="padding-top:2rem">
  <div class="container">
    <div class="filters-bar">
      <form method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
        <div class="filter-group" style="flex:2">
          <label>Rechercher une entreprise</label>
          <input type="text" name="q" placeholder="Nom de l'entreprise..." value="<?= htmlspecialchars($q) ?>">
        </div>
        <div class="filter-group">
          <label>Secteur</label>
          <select name="sector">
            <option value="">Tous les secteurs</option>
            <?php foreach(['informatique','marketing','finance','design','rh','commerce','ingenierie'] as $s): ?>
            <option value="<?= $s ?>" <?= $sector===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="entreprises.php" class="btn btn-outline">Réinitialiser</a>
      </form>
    </div>

    <?php if(empty($companies)): ?>
    <div class="empty-state">
      <div class="empty-icon">🏭</div>
      <h3>Aucune entreprise trouvée</h3>
      <p>Essayez d'autres critères de recherche.</p>
    </div>
    <?php else: ?>
    <div class="company-grid">
      <?php foreach($companies as $c): ?>
      <div class="company-card">
        <div style="width:64px;height:64px;border-radius:var(--radius);background:linear-gradient(135deg,var(--primary-light),#fce7f3);display:flex;align-items:center;justify-content:center;font-size:1.75rem;font-weight:800;color:var(--primary);margin:0 auto 1rem">
          <?= strtoupper(substr($c['company_name'],0,1)) ?>
        </div>
        <h3 style="font-size:1rem;margin-bottom:.35rem"><?= htmlspecialchars($c['company_name']) ?></h3>
        <?php if($c['sector']): ?><p style="font-size:.8rem;color:var(--muted);margin-bottom:.75rem">🏭 <?= ucfirst(htmlspecialchars($c['sector'])) ?></p><?php endif; ?>
        <?php if($c['location']): ?><p style="font-size:.8rem;color:var(--muted);margin-bottom:.75rem">📍 <?= htmlspecialchars($c['location']) ?></p><?php endif; ?>
        <div style="background:var(--primary-light);color:var(--primary);border-radius:50px;padding:.3rem .8rem;font-size:.8rem;font-weight:600;display:inline-block;margin-bottom:1rem">
          <?= $c['nb_offres'] ?> offre(s) active(s)
        </div>
        <?php if($c['nb_offres'] > 0): ?>
        <br><a href="offres.php?company=<?= $c['id'] ?>" class="btn btn-primary btn-sm">Voir les offres →</a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
