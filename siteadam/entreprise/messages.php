<?php
$required_role = 'company';
require_once '../includes/auth.php';

if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['content']) && !empty($_POST['receiver_id'])) {
    $pdo->prepare("INSERT INTO messages (sender_id,receiver_id,content) VALUES (?,?,?)")
        ->execute([$_SESSION['user_id'], $_POST['receiver_id'], trim($_POST['content'])]);
}

$convs = $pdo->prepare("SELECT DISTINCT u.id,CONCAT(sp.first_name,' ',sp.last_name) as name, (SELECT content FROM messages WHERE (sender_id=? AND receiver_id=u.id) OR (sender_id=u.id AND receiver_id=?) ORDER BY sent_at DESC LIMIT 1) as last_msg FROM messages m JOIN users u ON (u.id=m.sender_id OR u.id=m.receiver_id) JOIN student_profiles sp ON sp.user_id=u.id WHERE (m.sender_id=? OR m.receiver_id=?) AND u.id!=? GROUP BY u.id");
$convs->execute([$_SESSION['user_id'],$_SESSION['user_id'],$_SESSION['user_id'],$_SESSION['user_id'],$_SESSION['user_id']]);
$conversations = $convs->fetchAll();

$active_id = intval($_GET['with'] ?? ($conversations[0]['id'] ?? 0));
$msgs = $active_name = [];
if($active_id) {
    $pdo->prepare("UPDATE messages SET is_read=1 WHERE receiver_id=? AND sender_id=?")->execute([$_SESSION['user_id'],$active_id]);
    $m = $pdo->prepare("SELECT * FROM messages WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY sent_at");
    $m->execute([$_SESSION['user_id'],$active_id,$active_id,$_SESSION['user_id']]);
    $msgs = $m->fetchAll();
    $an = $pdo->prepare("SELECT CONCAT(sp.first_name,' ',sp.last_name) FROM student_profiles sp JOIN users u ON sp.user_id=u.id WHERE u.id=?");
    $an->execute([$active_id]);
    $active_name = $an->fetchColumn() ?: 'Candidat';
}
?>
<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Messages - JobLink</title><link rel="stylesheet" href="/siteadam/css/style.css"></head>
<body>
<div class="dash-layout">
  <?php include '../includes/sidebar_company.php'; ?>
  <div class="dash-content">
    <div class="dash-header"><h1>💬 Messagerie</h1></div>
    <div class="dash-body">
      <div class="msg-layout">
        <div class="msg-sidebar">
          <div style="padding:1rem;border-bottom:1px solid var(--border);font-weight:700;font-size:.9rem">Candidats</div>
          <?php if(empty($conversations)): ?>
          <div style="padding:1.5rem;text-align:center;color:var(--muted);font-size:.88rem">Aucune conversation.<br>Acceptez des candidatures pour discuter.</div>
          <?php else: ?>
          <?php foreach($conversations as $c): ?>
          <a href="?with=<?= $c['id'] ?>" class="msg-item <?= $active_id==$c['id']?'active':'' ?>">
            <div class="msg-item-name"><?= htmlspecialchars($c['name']) ?></div>
            <div class="msg-item-preview"><?= htmlspecialchars(substr($c['last_msg']??'',0,40)) ?></div>
          </a>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="msg-body">
          <?php if($active_id && !empty($msgs)): ?>
          <div class="msg-header-bar">💬 <?= htmlspecialchars($active_name) ?></div>
          <div class="msg-messages" id="msgs">
            <?php foreach($msgs as $m): ?>
            <div class="msg-bubble <?= $m['sender_id']==$_SESSION['user_id']?'msg-sent':'msg-recv' ?>">
              <?= htmlspecialchars($m['content']) ?>
              <div style="font-size:.7rem;opacity:.6;margin-top:.3rem"><?= date('H:i d/m',strtotime($m['sent_at'])) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <form class="msg-input-bar" method="POST">
            <input type="hidden" name="receiver_id" value="<?= $active_id ?>">
            <input type="text" name="content" placeholder="Écrire un message..." autocomplete="off" required>
            <button type="submit" class="btn btn-primary btn-sm">Envoyer ➤</button>
          </form>
          <?php else: ?>
          <div style="flex:1;display:flex;align-items:center;justify-content:center;text-align:center;color:var(--muted)">
            <div><div style="font-size:3rem;margin-bottom:1rem">💬</div><p>Sélectionnez une conversation<br>pour écrire un message.</p></div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script>const msgs=document.getElementById('msgs');if(msgs)msgs.scrollTop=msgs.scrollHeight;</script>
</body></html>
