<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/db.php';
require_once 'config/track.php';

// Action de vider le panier
if (isset($_GET['action']) && $_GET['action'] === 'vider') {
    unset($_SESSION['panier']);
    unset($_SESSION['total_panier']);
    header('Location: panier.php');
    exit;
}

// Validation initiale avant d'accéder au paiement mobile
$msg_commande = "";
if (isset($_POST['proceder_paiement'])) {
    if (!isset($_SESSION['user_id'])) {
        $msg_commande = "❌ Vous devez être connecté (onglet Connexion) pour valider une commande et payer.";
    } elseif (empty($_SESSION['panier'])) {
        $msg_commande = "❌ Votre panier est vide.";
    } else {
        // Redirection directe vers la passerelle mobile money M-Pesa / Airtel Money
        header('Location: paiement.php');
        exit;
    }
}

include 'includes/header.php';
?>
<div style="max-width: 900px; margin: 40px auto; padding: 0 20px; font-family: 'Segoe UI', sans-serif;">
    <h2 style="color: #19c37d; border-bottom: 2px solid #19c37d; padding-bottom: 10px; margin-bottom: 30px;">🛒 Votre Panier d'Achat</h2>

    <?php if(!empty($msg_commande)): ?>
        <div style="background: #112240; padding: 15px; border-left: 4px solid #e63946; margin-bottom: 20px; color:#fff; font-weight:bold;">
            <?= $msg_commande ?>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['panier'])): ?>
        <div style="background: #1e1e1f; padding: 30px; text-align: center; border-radius: 8px; border: 1px solid #333;">
            <p style="color: #aaa; font-style: italic; font-size: 16px;">Votre panier est actuellement vide.</p>
            <a href="catalogue.php" style="display:inline-block; background:#e63946; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin-top:15px; font-weight:bold;">← Retourner au catalogue</a>
        </div>
    <?php else: ?>
        <div style="background: #1e1e1f; border-radius: 8px; border: 1px solid #333; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; background: #1e1e1f; text-align: left;">
                <tr style="background: #2d2d30; color: white;">
                    <th style="padding: 15px;">Désignation</th>
                    <th style="padding: 15px;">Prix Unitaire</th>
                    <th style="padding: 15px;">Quantité</th>
                    <th style="padding: 15px;">Sous-total</th>
                </tr>
                <?php 
                $grand_total = 0;
                foreach ($_SESSION['panier'] as $id => $item): 
                    $subtotal = $item['prix'] * $item['quantite'];
                    $grand_total += $subtotal;
                ?>
                    <tr style="border-bottom: 1px solid #333;">
                        <td style="padding: 15px; color: #fff; font-weight: bold;"><?= htmlspecialchars($item['nom']) ?></td>
                        <td style="padding: 15px; color: #aaa;"><?= number_format($item['prix'], 0, ',', ' ') ?> FC</td>
                        <td style="padding: 15px; color: #fff;"><?= $item['quantite'] ?></td>
                        <td style="padding: 15px; color: #19c37d; font-weight: bold;"><?= number_format($subtotal, 0, ',', ' ') ?> FC</td>
                    </tr>
                <?php endforeach; ?>
                <?php 
                // Stockage du total en session pour la page paiement.php
                $_SESSION['total_panier'] = $grand_total; 
                ?>
            </table>
            
            <div style="padding: 25px; background: #131314; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <span style="color: #aaa; font-size: 16px;">Montant Total à Payer :</span>
                    <div style="font-size: 28px; font-weight: bold; color: #19c37d; margin-top: 5px;"><?= number_format($grand_total, 0, ',', ' ') ?> FC</div>
                </div>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <a href="panier.php?action=vider" style="background: transparent; border: 1px solid #e63946; color: #e63946; padding: 12px 20px; text-decoration: none; font-weight: bold; border-radius: 4px;">🗑️ Vider</a>
                    <form method="POST" style="margin: 0;">
                        <button type="submit" name="proceder_paiement" style="background: #19c37d; color: #0a192f; border: none; padding: 13px 30px; font-weight: bold; border-radius: 4px; cursor: pointer; font-size: 15px;">💳 Commander et Payer</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>