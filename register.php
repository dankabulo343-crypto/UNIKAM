<?php
ob_start();
require_once 'config/db.php';

$err = ""; $suc = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (!empty($nom) && !empty($email) && !empty($pass)) {
        try {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, 'client')");
            $stmt->execute([$nom, $email, $hash]);
            $suc = "🎉 Inscription réussie ! Redirection...";
            header("Refresh: 1; URL=login.php");
        } catch (Exception $e) {
            $err = "Erreur : " . $e->getMessage();
        }
    } else {
        $err = "Veuillez remplir tous les champs.";
    }
}
include 'includes/header.php';
?>
<div style="max-width: 400px; margin: 60px auto; padding: 30px; background: #1e1e1f; border-radius: 8px; border: 1px solid #333;">
    <h2 style="color: #e63946; text-align: center; margin-bottom:20px;">📝 Créer un Compte</h2>
    <?php if($err): ?><p style="color:#e63946; font-weight:bold;"><?= $err ?></p><?php endif; ?>
    <?php if($suc): ?><p style="color:#19c37d; font-weight:bold;"><?= $suc ?></p><?php endif; ?>
    <form method="POST" style="display:flex; flex-direction:column; gap:15px;">
        <input type="text" name="nom" placeholder="Nom Complet" required style="padding:12px; background:#131314; color:#fff; border:1px solid #444; border-radius:4px;">
        <input type="email" name="email" placeholder="Adresse Email" required style="padding:12px; background:#131314; color:#fff; border:1px solid #444; border-radius:4px;">
        <input type="password" name="password" placeholder="Mot de passe" required style="padding:12px; background:#131314; color:#fff; border:1px solid #444; border-radius:4px;">
        <button type="submit" style="background:#e63946; color:white; padding:12px; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">S'inscrire</button>
    </form>
</div>
</body>
</html>