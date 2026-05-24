<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header('Location: /siteadam/'.($_SESSION['role']==='company'?'entreprise':'etudiant').'/dashboard.php');
    exit;
}
require_once 'config/db.php';
$error = '';
$role = $_GET['role'] ?? 'student';

if($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $role  = $_POST['role'] ?? 'student';
    $fname = trim($_POST['first_name'] ?? '');
    $lname = trim($_POST['last_name'] ?? '');
    $cname = trim($_POST['company_name'] ?? '');

    if(!$email || !$pass || strlen($pass)<6) {
        $error = 'Veuillez remplir tous les champs. Mot de passe minimum 6 caractères.';
    } elseif($pdo->query("SELECT id FROM users WHERE email='".addslashes($email)."'")->fetch()) {
        $error = 'Cet email est déjà utilisé.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email,password,role) VALUES (?,?,?)");
        $stmt->execute([$email, $hash, $role]);
        $uid = $pdo->lastInsertId();

        if($role === 'student') {
            $s = $pdo->prepare("INSERT INTO student_profiles (user_id,first_name,last_name) VALUES (?,?,?)");
            $s->execute([$uid, $fname, $lname]);
        } else {
            $c = $pdo->prepare("INSERT INTO company_profiles (user_id,company_name) VALUES (?,?)");
            $c->execute([$uid, $cname]);
        }

        $_SESSION['user_id'] = $uid;
        $_SESSION['role']    = $role;
        $_SESSION['name']    = $role==='student' ? "$fname $lname" : $cname;
        header('Location: /siteadam/'.($role==='company'?'entreprise':'etudiant').'/dashboard.php');
        exit;
    }
}
$page_title = 'Inscription - JobLink';
include 'includes/header.php';
?>

<div class="auth-page" style="background:linear-gradient(135deg,#eef2ff,#fce7f3);min-height:calc(100vh - 64px)">
  <div class="auth-card" style="max-width:520px">
    <h2>Créer un compte </h2>
    <p class="subtitle">Rejoignez des milliers d'étudiants et d'entreprises</p>

    <?php if($error): ?><div class="alert alert-error"> <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST">
      <input type="hidden" name="role" id="role-input" value="<?= htmlspecialchars($role) ?>">

      <div class="role-selector">
        <div class="role-card <?= $role==='student'?'active':'' ?>" onclick="setRole('student',this)">
          <div class="role-icon"></div>
          <h4>Étudiant</h4>
          <p style="font-size:.8rem;color:var(--muted)">Je cherche un stage/emploi</p>
        </div>
        <div class="role-card <?= $role==='company'?'active':'' ?>" onclick="setRole('company',this)">
          <div class="role-icon"></div>
          <h4>Entreprise</h4>
          <p style="font-size:.8rem;color:var(--muted)">Je publie des offres</p>
        </div>
      </div>

      <div id="fields-student" style="display:<?= $role==='student'?'block':'none' ?>">
        <div class="form-row">
          <div class="form-group"><label>Prénom</label><input type="text" name="first_name" placeholder="Ahmed"></div>
          <div class="form-group"><label>Nom</label><input type="text" name="last_name" placeholder="Ben Ali"></div>
        </div>
      </div>
      <div id="fields-company" style="display:<?= $role==='company'?'block':'none' ?>">
        <div class="form-group"><label>Nom de l'entreprise</label><input type="text" name="company_name" placeholder="Tech Solutions SARL"></div>
      </div>

      <div class="form-group"><label>Adresse email</label><input type="email" name="email" placeholder="vous@exemple.com" required></div>
      <div class="form-group"><label>Mot de passe</label><input type="password" name="password" placeholder="Minimum 6 caractères" required></div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:.5rem">Créer mon compte →</button>
    </form>

    <div class="divider-or">ou</div>
    <p style="text-align:center;font-size:.9rem;color:var(--muted)">Déjà un compte ? <a href="login.php" style="color:var(--primary);font-weight:600">Se connecter</a></p>
  </div>
</div>

<script>
function setRole(role, el) {
  document.getElementById('role-input').value = role;
  document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('fields-student').style.display = role==='student' ? 'block' : 'none';
  document.getElementById('fields-company').style.display = role==='company' ? 'block' : 'none';
}
</script>
<?php include 'includes/footer.php'; ?>
