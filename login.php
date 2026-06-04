<?php
ob_start();
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $err = "❌ Identifiants invalides.";
    }
}
include 'includes/header.php';
?>
<div style="max-width: 400px; margin: 60px auto; padding: 30px; background: #1e1e1f; border-radius: 8px; border: 1px solid #333;">
    <h2 style="color: #e63946; text-align: center; margin-bottom:20px;">🔑 Connexion</h2>
    <?php if($err): ?><p style="color:#e63946; font-weight:bold;"><?= $err ?></p><?php endif; ?>
    <form method="POST" style="display:flex; flex-direction:column; gap:15px;">
        <input type="email" name="email" placeholder="Email" required style="padding:12px; background:#131314; color:#fff; border:1px solid #444; border-radius:4px;">
        <input type="password" name="password" placeholder="Mot de passe" required style="padding:12px; background:#131314; color:#fff; border:1px solid #444; border-radius:4px;">
        <button type="submit" style="background:#e63946; color:white; padding:12px; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Se connecter</button>
    </form>
</div>
</body>
</html>