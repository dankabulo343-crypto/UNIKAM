<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'includes/header.php';

// On récupère le montant du panier (ou 0 si le panier est vide)
$montant_total = $_SESSION['total_panier'] ?? 25000; // Exemple par défaut de 25 000 FC
$message = "";
$etape_validation = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['initier_paiement'])) {
        $telephone = htmlspecialchars($_POST['telephone']);
        $operateur = htmlspecialchars($_POST['operateur']);
        
        if (!empty($telephone) && strlen($telephone) >= 9) {
            $etape_validation = true;
            $message = "📱 Requête de paiement envoyée ! Veuillez vérifier le téléphone lié au numéro $telephone. Un message push USSD de confirmation a été envoyé via $operateur.";
        } else {
            $message = "❌ Veuillez entrer un numéro de téléphone valide.";
        }
    }
    
    if (isset($_POST['confirmer_code'])) {
        // Simulation de la validation du paiement
        $message = "🎉 Paiement de " . number_format($montant_total, 0, ',', ' ') . " FC reçu avec succès via Mobile Money ! Votre commande est en cours de traitement pour la gare de Kamina.";
        // Ici, tu pourrais vider le panier en base de données
        unset($_SESSION['panier']);
        $etape_validation = false;
    }
}
?>

<div style="max-width: 600px; margin: 40px auto; padding: 0 20px; font-family: 'Segoe UI', sans-serif;">
    <h2 style="color: #fff; border-bottom: 2px solid #e63946; padding-bottom: 10px; margin-bottom: 30px;">💳 Finaliser votre commande cher CLIENT</h2>

    <?php if (!empty($message)): ?>
        <div style="background: #112240; color: #19c37d; padding: 15px; margin-bottom: 20px; border-radius: 6px; border: 1px solid #19c37d; font-weight: bold;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div style="background: #1e1e1f; border: 1px solid #333; border-radius: 8px; padding: 25px;">
        <h3 style="color: #fff; margin-top: 0;">Montant à payer : <span style="color: #e63946;"><?= number_format($montant_total, 0, ',', ' ') ?> FC</span></h3>
        
        <?php if (!$etape_validation): ?>
            <form method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">1. Choisissez votre réseau Mobile Money :</label>
                    <select name="operateur" style="width: 100%; padding: 12px; background: #131314; color: #fff; border: 1px solid #444; border-radius: 4px; font-size: 15px;">
                        <option value="M-Pesa">🔴 Vodacom M-Pesa</option>
                        <option value="Airtel Money">🔴 Airtel Money</option>
                        <option value="Orange Money">🟠 Orange Money</option>
                    </select>
                </div>

                <div>
                    <label style="color: #aaa; display: block; margin-bottom: 8px;">2. Numéro de téléphone du compte :</label>
                    <input type="text" name="telephone" placeholder="Ex: 0820000000" required style="width: 100%; padding: 12px; background: #131314; color: #fff; border: 1px solid #444; border-radius: 4px; box-sizing: border-box; font-size: 15px;">
                </div>

                <button type="submit" name="initier_paiement" style="background: #e63946; color: white; padding: 14px; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px;">
                    🚀 Lancer le paiement mobile
                </button>
            </form>

        <?php else: ?>
            <div style="text-align: center; margin: 20px 0;">
                <div style="border: 4px solid #f3f3f3; border-top: 4px solid #e63946; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 15px auto;"></div>
                <p style="color: #aaa; font-style: italic;">En attente de la saisie de votre code PIN sur votre téléphone portable...</p>
            </div>

            <form method="POST" style="text-align: center;">
                <input type="hidden" name="telephone" value="<?= htmlspecialchars($_POST['telephone']) ?>">
                <input type="hidden" name="operateur" value="<?= htmlspecialchars($_POST['operateur']) ?>">
                
                <button type="submit" name="confirmer_code" style="background: #19c37d; color: #0a192f; padding: 12px 30px; border: none; font-weight: bold; border-radius: 4px; cursor: pointer; font-size: 15px;">
                    ✅ Simuler la validation du PIN (Succès)
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
</body>
</html>