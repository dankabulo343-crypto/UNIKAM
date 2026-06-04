<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/db.php';

// Sécurité de base : décommenter si tu as une gestion de rôles fonctionnelle
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//     header('Location: login.php');
//     exit;
// }

// 1. Récupération des statistiques du jour (Date d'aujourd'hui)
$aujourdhui = date('Y-m-d');
$totalVentesJour = 0;
$nbCommandesJour = 0;

try {
    // Calcul du chiffre d'affaires du jour
    $stmtSum = $pdo->prepare("SELECT SUM(total_price) as total FROM orders WHERE DATE(created_at) = ?");
    $stmtSum->execute([$aujourdhui]);
    $resSum = $stmtSum->fetch();
    $totalVentesJour = $resSum['total'] ?? 0;

    // Nombre de commandes du jour
    $stmtCount = $pdo->prepare("SELECT COUNT(*) as nb FROM orders WHERE DATE(created_at) = ?");
    $stmtCount->execute([$aujourdhui]);
    $resCount = $stmtCount->fetch();
    $nbCommandesJour = $resCount['nb'] ?? 0;

    // RÉCUPÉRATION : On charge toutes les commandes
$commandesDuJour = [];
try {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // CORRECTION FORCEE AVANT AFFICHAGE
    foreach ($resultats as $index => $cmd) {
        // Si la base donne 'status' mais que le vieux code HTML cherche 'statut', on duplique la case
        if (isset($cmd['status']) && !isset($cmd['statut'])) {
            $cmd['statut'] = $cmd['status'];
        }
        $commandesDuJour[$index] = $cmd;
    }
} catch (Exception $e) {
    $erreur = "Erreur de chargement : " . $e->getMessage();
}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Administrateur - Gestion Stock & Commandes</title>
    <style>
        :root {
            --admin-bg: #131314;
            --admin-card: #1e1e1f;
            --admin-red: #e63946;
            --admin-green: #19c37d;
            --admin-text: #ececf1;
            --admin-border: #333;
        }

        body {
            background-color: var(--admin-bg);
            color: var(--admin-text);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .admin-title {
            color: var(--admin-red);
            border-bottom: 2px solid var(--admin-red);
            padding-bottom: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-action-top {
            background: var(--admin-red);
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        /* Grille de statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #aaa;
            font-size: 16px;
        }

        .stat-card .valeur {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }

        .valeur.vert { color: var(--admin-green); }
        .valeur.rouge { color: var(--admin-red); }

        /* Sections Tableaux */
        .admin-section {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 40px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .admin-section h2 {
            margin-top: 0;
            font-size: 20px;
            color: #fff;
            border-left: 4px solid var(--admin-red);
            padding-left: 10px;
            margin-bottom: 20px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .admin-table th, .admin-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--admin-border);
        }

        .admin-table th {
            background: #18181a;
            color: #aaa;
            font-weight: 600;
        }

        .admin-table tr:hover {
            background: #252526;
        }

        /* Badges de statut */
        .badge-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-attente { background: #f39c12; color: #000; }
        .status-paye { background: var(--admin-green); color: #000; }
        .status-livre { background: #3498db; color: #fff; }

        /* Mini-vignette photo */
        .mini-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #555;
            background: #131314;
        }
    </style>
</head>
<body>

<div class="admin-container">
    
    <div class="admin-title">
        <h1>🛠️ Espace Super-Administrateur</h1>
        <a href="ajouter_piece.php" class="btn-action-top">+ Ajouter une pièce</a>
    </div>

    <?php if(isset($erreurBDD)): ?>
        <div style="background:#e63946; color:#fff; padding:15px; border-radius:6px; margin-bottom:20px;"><?= $erreurBDD ?></div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Commandes de la journée</h3>
            <p class="valeur rouge"><?= $nbCommandesJour ?></p>
        </div>
        <div class="stat-card">
            <h3>Chiffre d'Affaires du Jour</h3>
            <p class="valeur vert"><?= number_format($totalVentesJour, 2, ',', ' ') ?> €</p>
        </div>
        <div class="stat-card">
            <h3>Date du Suivi</h3>
            <p class="valeur" style="color:#3498db; font-size:24px; margin-top:8px;"><?= date('d/m/2026') ?></p>
        </div>
    </div>

    <div class="admin-section">
        <h2>📦 Commandes reçues aujourd'hui</h2>
        <?php if(empty($commandesDuJour)): ?>
            <p style="color:#aaa; font-style:italic;">Aucune commande enregistrée pour le moment aujourd'hui.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <td style="padding: 15px;">
                        <?php 
                            // On nettoie le statut actuel pour éviter les erreurs
                            $st = trim($cmd['status'] ?? $cmd['statut'] ?? 'En attente');
                            
                            // Couleur du badge selon le texte
                            $badgeColor = '#f39c12'; // Orange pour En attente
                            if ($st === 'Payée' || $st === 'Livré' || $st === 'Livrée') { $badgeColor = '#19c37d'; } // Vert
                            if ($st === 'Expédié' || $st === 'En cours') { $badgeColor = '#3498db'; } // Bleu
                        ?>
                        <span style="padding: 5px 10px; border-radius: 4px; font-weight: bold; background: <?= $badgeColor ?>; color: #000; font-size: 12px; display: inline-block;">
                            <?= htmlspecialchars($st) ?>
                        </span>
                    </td>

                    <td style="padding: 15px;">
                        <form action="admin.php" method="POST" style="display: flex; gap: 6px; align-items: center; margin: 0;">
                            <input type="hidden" name="commande_id" value="<?= $cmd['id'] ?>">
                            
                            <select name="nouveau_statut" style="padding: 6px; background: #1e1e1f; border: 1px solid #444; color: #fff; border-radius: 4px; font-size: 13px; cursor: pointer;">
                                <option value="En attente" <?= ($st === 'En attente') ? 'selected' : '' ?>>En attente</option>
                                <option value="En cours" <?= ($st === 'En cours') ? 'selected' : '' ?>>En cours</option>
                                <option value="Expédié" <?= ($st === 'Expédié') ? 'selected' : '' ?>>Expédié</option>
                                <option value="Livré" <?= ($st === 'Livré' || $st === 'Livrée') ? 'selected' : '' ?>>Livré</option>
                            </select>
                            
                            <button type="submit" name="update_status" style="background: #e63946; color: white; border: none; padding: 7px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px;">OK</button>
                        </form>
                    </td>
                </thead>
                <tbody>
                    <?php foreach($commandesDuJour as $cmd): ?>
                        <?php 
                            $statusClass = 'status-attente';
                            if($cmd['status'] == 'Payée') $statusClass = 'status-paye';
                            if($cmd['status'] == 'Livrée') $statusClass = 'status-livre';
                        ?>
                        <tr>
                            <td><b>#<?= $cmd['id'] ?></b></td>
                            <td><?= htmlspecialchars($cmd['client_name']) ?></td>
                            <td style="font-weight:bold; color:var(--admin-green);"><?= number_format($cmd['total_price'], 2, ',', ' ') ?> €</td>
                            <td><?= date('H:i', strtotime($cmd['created_at'])) ?></td>
                            <td><span class="badge-status <?= $statusClass ?>"><?= $cmd['status'] ?></span></td>
                        </tr>
                    <?php endcode; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="admin-section">
        <h2>🔧 Inventaire Général & Gestion des Pièces en Stock</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Nom de la pièce</th>
                    <th>Prix Public</th>
                    <th>Fichier Image assigné</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($piecesEnStock as $p): ?>
                    <?php 
                        $imgFile = !empty($p['image']) ? $p['image'] : 'default.jpg';
                    ?>
                    <tr>
                        <td><img src="assets/images/<?= $imgFile ?>" class="mini-photo" alt="Pièce"></td>
                        <td><b><?= htmlspecialchars($p['nom']) ?></b></td>
                        <td style="color:var(--admin-green); font-weight:bold;"><?= number_format($p['prix'], 2, ',', ' ') ?> €</td>
                        <td style="font-family:monospace; font-size:13px; color:#aaa;"><?= $imgFile ?></td>
                        <td>
                            <a href="ajouter_piece.php" style="color:#3498db; text-decoration:none; margin-right:15px; font-size:14px;">📝 Modifier</a>
                            <span style="color:#555;">|</span>
                            <a href="#" style="color:var(--admin-red); text-decoration:none; margin-left:15px; font-size:14px;" onclick="alert('Fonctionnalité de suppression liée à votre base de données')">❌ Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>