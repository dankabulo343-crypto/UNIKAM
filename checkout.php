<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/db.php';

// 1. SÉCURITÉ : Si l'utilisateur n'est pas connecté, on le renvoie vers le login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$erreur = "";
$succes = "";

// Exemple de simulation de panier si tu n'as pas encore de session panier active
// (À adapter selon la façon dont tu stockes tes pièces auto dans $_SESSION['panier'])
$total_panier = 0;
$liste_produits = "";

if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $item) {
        $total_panier += $item['prix'] * $item['quantite'];
        $liste_produits .= $item['nom'] . " (x" . $item['quantite'] . "), ";
    }
    $liste_produits = rtrim($liste_produits, ", ");
}

// 2. TRAITEMENT DU FORMULAIRE DE VALIDATION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_commande'])) {
    $nom_client = trim($_POST['nom_client'] ?? $_SESSION['user_name'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    
    // Récupération forcée des montants du formulaire ou de la session
    $total = isset($_POST['total_input']) ? floatval($_POST['total_input']) : $total_panier;

    if (!empty($nom_client) && !empty($tel) && !empty($adresse)) {
        try {
            // LIGNE 31 CORRIGÉE : user_id est passé de force et ne sera plus jamais NULL
            $sql = "INSERT INTO orders (user_id, client_name, nom_client, tel, adresse, ville, total_price, total, produits, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'En attente')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $user_id,       // user_id valide de la session
                $nom_client,    // client_name (anglais)
                $nom_client,    // nom_client (français)
                $tel,
                $adresse,
                $ville,
                $total,         // total_price
                $total,         // total
                $liste_produits
            ]);

            // On vide le panier après commande réussie
            if (isset($_SESSION['panier'])) { unset($_SESSION['panier']); }

            $succes = "✅ Commande enregistrée avec succès en Francs Congolais !";
            header("Refresh: 2; URL=espace_client.php"); // Redirige vers l'historique
        } catch (Exception $e) {
            $erreur = "Erreur SQL : " . $e->getMessage();
        }
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires (Nom, Téléphone, Adresse).";
    }
}

include 'includes/header.php';
?>

<div style="max-width: 600px; margin: 40px auto; padding: 25px; background: #1e1e1f; border-radius: 8px; border: 1px solid #333; font-family: sans-serif; color: #ececf1;">
    <h2 style="color: #e63946; border-bottom: 2px solid #e63946; padding-bottom: 10px; margin-bottom: 20px;">🛒 Validation de votre Commande</h2>

    <?php if (!empty($erreur)): ?>
        <div style="background: #e63946; color: white; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-weight: bold;"><?= $erreur ?></div>
    <?php endif; ?>

    <?php if (!empty($succes)): ?>
        <div style="background: #19c37d; color: black; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-weight: bold;"><?= $succes ?></div>
    <?php endif; ?>

    <form action="checkout.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label style="display:block; margin-bottom:5px; color:#aaa;">Nom Complet du destinataire :</label>
            <input type="text" name="nom_client" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" required style="width:100%; padding: 10px; background:#131314; border:1px solid #444; color:#fff; border-radius:4px; box-sizing:border-box;">
        </div>

        <div>
            <label style="display:block; margin-bottom:5px; color:#aaa;">Numéro de Téléphone :</label>
            <input type="text" name="tel" placeholder="Ex: 0970747412" required style="width:100%; padding: 10px; background:#131314; border:1px solid #444; color:#fff; border-radius:4px; box-sizing:border-box;">
        </div>

        <div>
            <label style="display:block; margin-bottom:5px; color:#aaa;">Adresse complète de livraison :</label>
            <input type="text" name="adresse" placeholder="Numéro, Avenue, Quartier..." required style="width:100%; padding: 10px; background:#131314; border:1px solid #444; color:#fff; border-radius:4px; box-sizing:border-box;">
        </div>

        <div>
            <label style="display:block; margin-bottom:5px; color:#aaa;">Ville :</label>
            <input type="text" name="ville" value="Kamina" style="width:100%; padding: 10px; background:#131314; border:1px solid #444; color:#fff; border-radius:4px; box-sizing:border-box;">
        </div>

        <input type="hidden" name="total_input" value="<?= $total_panier > 0 ? $total_panier : 79500 ?>">

        <div style="background:#131314; padding:15px; border-radius:6px; border:1px solid #444; margin-top:10px; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:16px; font-weight:bold;">Montant total à payer :</span>
            <span style="font-size:22px; font-weight:bold; color:#19c37d;"><?= number_format($total_panier > 0 ? $total_panier : 79500, 0, ',', ' ') ?> FC</span>
        </div>

        <button type="submit" name="valider_commande" style="background:#e63946; color:white; border:none; padding:12px; font-size:16px; font-weight:bold; border-radius:4px; cursor:pointer; margin-top:10px; transition: 0.2s;">
            Confirmé la commande
        </button>
    </form>
</div>

</body>
</html>