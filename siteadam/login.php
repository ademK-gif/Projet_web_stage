<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header('Location: /siteadam/'.($_SESSION['role']==='company'?'entreprise':'etudiant').'/dashboard.php');
    exit;
}
require_once 'config/db.php';
$error = '';

if($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $user  = $pdo->prepare("SELECT u.*,sp.first_name,sp.last_name,cp.company_name FROM users u LEFT JOIN student_profiles sp ON sp.user_id=u.id LEFT JOIN company_profiles cp ON cp.user_id=u.id WHERE u.email=?");
    $user->execute([$email]);
    $u = $user->fetch();
    if($u && password_verify($pass, $u['password'])) {
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['role']    = $u['role'];
        $_SESSION['name']    = $u['role']==='company' ? $u['company_name'] : $u['first_name'].' '.$u['last_name'];
        header('Location: /siteadam/'.($u['role']==='company'?'entreprise':'etudiant').'/dashboard.php');
        exit;
    } else {
        $error = 'Email ou mot de passe incorrect.';
    }
}
$page_title = 'Connexion - JobLink';
include 'includes/header.php';
?>

<div class="auth-page" style="background:linear-gradient(135deg,#eef2ff,#fce7f3);min-height:calc(100vh - 64px)">
  <div class="auth-card">
    <h2>Bon retour ! 👋</h2>
    <p class="subtitle">Connectez-vous pour accéder à votre espace</p>

    <?php if($error): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if(isset($_GET['registered'])): ?><div class="alert alert-success">✅ Compte créé avec succès ! Connectez-vous.</div><?php endif; ?>

    <form method="POST">
      <div class="form-group"><label>Adresse email</label><input type="email" name="email" placeholder="vous@exemple.com" required autofocus></div>
      <div class="form-group"><label>Mot de passe</label><input type="password" name="password" placeholder="••••••••" required></div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Se connecter →</button>
    </form>

    <div class="divider-or">ou</div>
    <p style="text-align:center;font-size:.9rem;color:var(--muted)">Pas encore de compte ? <a href="register.php" style="color:var(--primary);font-weight:600">S'inscrire gratuitement</a></p>

    <div style="margin-top:2rem;padding:1rem;background:#f8fafc;border-radius:var(--radius);font-size:.82rem;color:var(--muted)">
      <strong>Comptes de démonstration :</strong><br>
      Étudiant : <code>etudiant@test.com</code> / <code>123456</code><br>
      Entreprise : <code>entreprise@test.com</code> / <code>123456</code>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
