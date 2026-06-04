<<?php
require_once 'config/db.php';
include 'includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0;

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();
}
?>
<h2>Votre Panier</h2>
<?php if (empty($products)): ?>
    <p>Votre panier est vide. <a href="catalogue.php" style="color:var(--primary); text-decoration:underline;">Visitez le catalogue</a>.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $p): 
                $subtotal = $p['prix'] * $cart[$p['id']];
                $total += $subtotal;
            ?>
            <tr>
                <td data-label="Produit"><?= htmlspecialchars($p['nom']) ?></td>
                <td data-label="Prix"><?= number_format($p['prix'], 2, ',', ' ') ?> €</td>
                <td data-label="Quantité">
                    <form action="add_to_cart.php?id=<?= $p['id'] ?>&action=update" method="POST" style="background:none; border:none; padding:0; display:inline-flex; gap:5px;">
                        <input type="number" name="qty" value="<?= $cart[$p['id']] ?>" min="1" style="width:60px; padding:5px; text-align:center;">
                        <button type="submit" class="btn" style="padding:5px 10px; font-size:12px;">OK</button>
                    </form>
                </td>
                <td data-label="Total"><?= number_format($subtotal, 2, ',', ' ') ?> €</td>
                <td data-label="Actions">
                    <a href="add_to_cart.php?id=<?= $p['id'] ?>&action=delete" class="btn btn-danger" style="padding:5px 10px; font-size:12px;">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align:right; font-weight:bold; font-size:18px;">TOTAL DU PANIER :</td>
                <td colspan="2" style="color:var(--primary); font-weight:bold; font-size:18px;"><?= number_format($total, 2, ',', ' ') ?> €</td>
            </tr>
        </tbody>
    </table>
    <div style="margin-top:20px; text-align:right;">
        <a href="checkout.php" class="btn" style="padding:15px 30px;">Passer la commande</a>
    </div>
<?php endif; ?>
</body>
</html>