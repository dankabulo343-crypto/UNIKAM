<?php
session_start();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'add';

if ($id > 0) {
    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
    
    if ($action === 'add') {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
        // Déclenche l'alerte visuelle
        $_SESSION['flash_message'] = "🛒 Produit ajouté au panier avec succès !";
        header('Location: catalogue.php'); // Redirige vers le catalogue pour continuer les achats
        exit;
    } elseif ($action === 'update') {
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        if($qty > 0) { $_SESSION['cart'][$id] = $qty; } else { unset($_SESSION['cart'][$id]); }
        $_SESSION['flash_message'] = "🔄 Quantité mise à jour.";
    } elseif ($action === 'delete') {
        unset($_SESSION['cart'][$id]);
        $_SESSION['flash_message'] = "🗑️ Produit retiré du panier.";
    }
}
header('Location: cart.php');
exit;