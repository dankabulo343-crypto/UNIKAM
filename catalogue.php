<?php
require_once 'config/db.php';
require_once 'config/track.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($_POST['ajouter_au_panier'])) {
    $id_produit = $_POST['produit_id'];
    $nom_produit = $_POST['produit_nom'];
    $prix_produit = floatval($_POST['produit_prix']);
    
    if (!isset($_SESSION['panier'])) { $_SESSION['panier'] = []; }
    $_SESSION['panier'][$id_produit] = [
        'nom' => $nom_produit,
        'prix' => $prix_produit,
        'quantite' => (isset($_SESSION['panier'][$id_produit]) ? $_SESSION['panier'][$id_produit]['quantite'] + 1 : 1)
    ];
    header('Location: catalogue.php');
    exit;
}

try {
    $articles = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $articles = [];
}

include 'includes/header.php';
?>
<div style="max-width: 1100px; margin: 40px auto; padding: 0 20px;">
    <h2 style="color: #e63946; border-bottom: 2px solid #e63946; padding-bottom: 10px; margin-bottom: 30px;">📦 Catalogue des Pièces (Prix en FC)</h2>
    
    <?php if(empty($articles)): ?>
        <p style="color:#aaa; font-style:italic;">Aucun article disponible pour le moment. Allez dans l'Espace Admin pour en ajouter.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">
            <?php foreach ($articles as $art): ?>
                <div style="background: #112240; border: 1px solid #233554; border-radius: 8px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <img src="assets/images/<?= htmlspecialchars($art['image']) ?>" style="width:100%; height:160px; object-fit:cover; border-radius:6px;" onerror="this.src='https://via.placeholder.com/280x160/0a192f/ffffff?text=Piece'">
                        <span style="background: #e63946; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; display:inline-block; margin-top:10px;"><?= htmlspecialchars($art['marque'] ?? 'Auto') ?></span>
                        <h3 style="color: #fff; margin: 10px 0; font-size: 18px;"><?= htmlspecialchars($art['nom']) ?></h3>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: bold; color: #19c37d; margin: 15px 0;"><?= number_format($art['prix'], 0, ',', ' ') ?> FC</div>
                        <form action="catalogue.php" method="POST">
                            <input type="hidden" name="produit_id" value="<?= $art['id'] ?>">
                            <input type="hidden" name="produit_nom" value="<?= htmlspecialchars($art['nom']) ?>">
                            <input type="hidden" name="produit_prix" value="<?= $art['prix'] ?>">
                            <button type="submit" name="ajouter_au_panier" style="width: 100%; background: transparent; border: 1px solid #e63946; color: #e63946; padding: 10px; border-radius: 4px; font-weight: bold; cursor: pointer;">🛒 Ajouter au panier</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>