<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/db.php';
require_once 'config/track.php';

$user_id = $_SESSION['user_id'] ?? 1; // ID 1 par défaut si déconnecté pour le test

try {
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $commandes = [];
}

include 'includes/header.php';
?>
<div style="max-width: 900px; margin: 40px auto; padding: 20px;">
    <div style="background: #112240; padding: 25px; border-radius: 8px; border-left: 4px solid #e63946; margin-bottom: 30px;">
        <h2 style="margin: 0; color: #fff;">📋 Mon Espace Personnel</h2>
        <p style="color: #aaa; margin: 5px 0 0 0;">Visualisez l'état de vos demandes et factures en Francs Congolais.</p>
    </div>

    <h3 style="color: #e63946; margin-bottom: 15px;">Historique des Commandes</h3>
    <?php if (empty($commandes)): ?>
        <p style="color: #aaa; font-style: italic;">Aucune commande enregistrée pour votre compte.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; background: #1e1e1f; border-radius: 6px; overflow: hidden;">
            <tr style="background: #2d2d30; text-align: left; color: white;">
                <th style="padding: 15px;">Numéro</th>
                <th style="padding: 15px;">Statut</th>
                <th style="padding: 15px;">Total Payé</th>
            </tr>
            <?php foreach ($commandes as $com): ?>
                <tr style="border-bottom: 1px solid #333;">
                    <td style="padding: 15px; color: #3498db; font-weight: bold;">#<?= $com['id'] ?></td>
                    <td style="padding: 15px;"><span style="background: #e63946; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px;"><?= htmlspecialchars($com['statut']) ?></span></td>
                    <td style="padding: 15px; color: #19c37d; font-weight: bold;"><?= number_format($com['total'], 0, ',', ' ') ?> FC</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>