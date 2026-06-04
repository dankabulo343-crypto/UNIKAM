<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/db.php';
require_once 'config/track.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_produit'])) {
    $nom = trim($_POST['nom_piece'] ?? '');
    $prix = floatval($_POST['prix_piece'] ?? 0);
    $marque = trim($_POST['marque_piece'] ?? '');
    $image = trim($_POST['image_piece'] ?? 'no-image.jpg');

    if (!empty($nom) && $prix > 0) {
    try {
        // Ajout explicite de subcategory_id à NULL pour respecter la contrainte
        $stmt = $pdo->prepare("INSERT INTO products (nom, prix, marque, image, subcategory_id) VALUES (?, ?, ?, ?, NULL)");
        $stmt->execute([$nom, $prix, $marque, $image]);
        $msg = "🎉 Article ajouté au catalogue avec succès !";
    } catch (Exception $e) {
        $msg = "❌ Erreur : " . $e->getMessage();
    }
}
}

try { $commandes = $pdo->query("SELECT * FROM commandes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){ $commandes = []; }
try { $visites = $pdo->query("SELECT * FROM visites ORDER BY id DESC LIMIT 15")->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){ $visites = []; }

include 'includes/header.php';
?>
<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <h2 style="color: #19c37d; border-bottom: 2px solid #19c37d; padding-bottom: 10px; margin-bottom: 30px;">🛠️ Administration Générale</h2>
    
    <?php if($msg): ?><div style="background: #112240; color: #19c37d; padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold;"><?= $msg ?></div><?php endif; ?>

    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 350px; background: #1e1e1f; padding: 25px; border-radius: 8px; border: 1px solid #333;">
            <h3 style="color: #e63946; margin: 0 0 15px 0;">➕ Ajouter une pièce</h3>
            <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                <input type="text" name="nom_piece" placeholder="Nom de la pièce" required style="padding: 10px; background: #131314; color: #fff; border: 1px solid #444; border-radius: 4px;">
                <input type="text" name="marque_piece" placeholder="Marque (ex: Toyota)" style="padding: 10px; background: #131314; color: #fff; border: 1px solid #444; border-radius: 4px;">
                <input type="number" name="prix_piece" placeholder="Prix (en FC)" required style="padding: 10px; background: #131314; color: #fff; border: 1px solid #444; border-radius: 4px;">
                <input type="text" name="image_piece" placeholder="Image (ex: filtre.jpg)" style="padding: 10px; background: #131314; color: #fff; border: 1px solid #444; border-radius: 4px;">
                <button type="submit" name="ajouter_produit" style="background: #e63946; color: white; padding: 12px; border: none; font-weight: bold; border-radius: 4px; cursor: pointer;">💾 Publier l'article</button>
            </form>
        </div>

        <div style="flex: 1.5; min-width: 450px; display: flex; flex-direction: column; gap: 25px;">
            <div style="background: #1e1e1f; padding: 20px; border-radius: 8px; border: 1px solid #333;">
                <h3 style="color: #fff; margin:0 0 10px 0;">🛒 Toutes les Commandes</h3>
                <table style="width: 100%; border-collapse: collapse; background: #131314;">
                    <tr style="background:#2d2d30; color:#fff;"><th style="padding:8px;">ID</th><th style="padding:8px;">Total</th></tr>
                    <?php foreach($commandes as $c): ?>
                        <tr style="border-bottom:1px solid #2d2d30;"><td style="padding:8px;">#<?= $c['id'] ?></td><td style="padding:8px; color:#19c37d; font-weight:bold;"><?= number_format($c['total'], 0, ',', ' ') ?> FC</td></tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div style="background: #1e1e1f; padding: 20px; border-radius: 8px; border: 1px solid #333;">
                <h3 style="color: #fff; margin:0 0 10px 0;">👁️ Personnes ayant consulté la page (IP)</h3>
                <table style="width: 100%; border-collapse: collapse; background: #131314;">
                    <tr style="background:#2d2d30; color:#fff;"><th style="padding:8px;">Adresse IP</th><th style="padding:8px;">Page</th><th style="padding:8px;">Heure</th></tr>
                    <?php foreach($visites as $v): ?>
                        <tr style="border-bottom:1px solid #2d2d30;"><td style="padding:8px; color:#3498db; font-family:monospace;"><?= htmlspecialchars($v['ip_address']) ?></td><td style="padding:8px; color:#fff;"><?= htmlspecialchars($v['page_visited']) ?></td><td style="padding:8px; color:#aaa; font-size:12px;"><?= $v['visited_at'] ?></td></tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>