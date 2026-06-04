<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- SÉCURITÉ ANTI-DOUBLON ---
// Si la barre a déjà été affichée une fois durant l'exécution de la page, on arrête tout ici.
if (defined('NAVBAR_AFFICHED')) {
    return; 
}
// Sinon, on marque que la barre est affichée pour la première fois
define('NAVBAR_AFFICHED', true);


// Calcul du nombre total d'articles dans le panier
$total_articles_panier = 0;
if (isset($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $total_articles_panier += $item['quantite'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SNCC Parts - Boutique en ligne</title>
    <style>
        body { background-color: #0a192f !important; color: #ececf1; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; }
        .main-navbar { background: #1e1e1f; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e63946; box-sizing: border-box; }
        .brand-logo { color: #fff; font-size: 22px; font-weight: bold; text-decoration: none; }
        .brand-logo span { color: #e63946; }
        .nav-links { display: flex; gap: 20px; align-items: center; }
        .nav-item { color: #ececf1; text-decoration: none; font-size: 15px; font-weight: 500; transition: color 0.2s; }
        .nav-item:hover { color: #e63946; }
        .btn-panier { background: #233554; border: 1px solid #19c37d; color: #19c37d; padding: 8px 14px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px; }
        .btn-panier span { background: #19c37d; color: #0a192f; padding: 1px 6px; border-radius: 50%; font-size: 12px; margin-left: 5px; }
        .btn-ia { background: #e63946; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; font-size: 14px; }
        .btn-ia:hover { background: #c32f3a; }
    </style>
</head>
<body>
<nav class="main-navbar">
    <a href="index.php" class="brand-logo">🚗 GROUPE<span> 2</span></a>
    <div class="nav-links">
        <a href="index.php" class="nav-item">Accueil</a>
        <a href="catalogue.php" class="nav-item">📦 Catalogue</a>
        <a href="espace_client.php" class="nav-item">📋 Mes Commandes</a>
        <a href="panier.php" class="btn-panier">🛒 Mon Panier <span><?= $total_articles_panier ?></span></a>
        <a href="ia.php" class="btn-ia">💬 Assistant I.A.</a>
        
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="admin.php" class="nav-item" style="color: #19c37d; font-weight: bold;">🛠️ Admin</a>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="nav-item" style="color: #e63946; font-weight: bold;">❌ Déconnexion</a>
        <?php else: ?>
            <a href="register.php" class="nav-item" style="color: #3498db; font-weight: bold;">📝 Inscription</a>
            <a href="login.php" class="nav-item" style="color: #19c37d; font-weight: bold;">🔑 Connexion</a>
        <?php endif; ?>
    </div>
</nav>