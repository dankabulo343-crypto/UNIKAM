<?php
// Désactiver l'affichage des erreurs brutes pour ne pas casser le format JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Récupération du message envoyé par l'interface
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($data['message']) ? trim($data['message']) : '';

if (empty($userMessage)) {
    echo json_encode(['reply' => "Je n'ai pas bien reçu votre message..."]);
    exit;
}

$lowerMessage = mb_strtolower($userMessage, 'UTF-8');
$reply = "";

// 2. Réponses rapides automatiques (Garanties sans bug)
if (preg_match('/(bonjour|salut|hello|hey)/i', $lowerMessage)) {
    $reply = "Bonjour ! Je suis l'I.A. interne du magasin. Que recherchez-vous comme pièce aujourd'hui ? Spécifiez la marque ou le modèle !";
} elseif (preg_match('/(horaire|ouvert|ferme)/i', $lowerMessage)) {
    $reply = "Le magasin est ouvert du lundi au samedi de 8h00 à 18h00.";
}

// 3. Recherche dans la base de données si aucune réponse rapide
if (empty($reply)) {
    try {
        if (file_exists('config/db.php')) {
            include_once 'config/db.php';
        }
        
        // Si la connexion PDO existe, on effectue la recherche
        if (isset($pdo)) {
            $search = '%' . $lowerMessage . '%';
            
            // Requête robuste sur le nom ou la description des pièces
            $sql = "SELECT * FROM products WHERE LOWER(nom) LIKE ? OR LOWER(description) LIKE ? LIMIT 3";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$search, $search]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($products)) {
                $reply = "Voici les composants correspondants trouvés dans notre stock actuel :<br><br>";
                foreach ($products as $p) {
                    // Gestion de la photo de la pièce
                    $imgName = (!empty($p['image'])) ? $p['image'] : 'default.jpg';
                    $imgPath = 'assets/images/' . $imgName;

                    $reply .= "
                    <div style='display: flex; align-items: center; gap: 15px; margin-bottom: 12px; padding: 12px; background: #2d2f36; border: 1px solid #444; border-radius: 8px;'>
                        <img src='{$imgPath}' alt='Photo' style='width: 65px; height: 65px; object-fit: cover; border-radius: 6px; background: #222; border: 1px solid #555;'>
                        <div style='flex: 1;'>
                            <strong style='color: #19c37d; font-size: 15px;'>" . htmlspecialchars($p['nom']) . "</strong><br>
                            <span style='color: #ececf1; font-size: 13px;'>Prix : " . number_format($p['prix'], 2, ',', ' ') . " €</span><br>
                            <a href='add_to_cart.php?id=" . $p['id'] . "' style='display: inline-block; margin-top: 6px; color: #000; background: #19c37d; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: bold;'>Ajouter au panier</a>
                        </div>
                    </div>";
                }
            }
        }
    } catch (Exception $e) {
        $reply = "Une erreur technique est survenue lors de l'analyse de notre stock.";
    }
}

// 4. Message de secours si rien n'est trouvé
if (empty($reply)) {
    $reply = "Je n'ai pas trouvé de correspondance exacte pour '" . htmlspecialchars($userMessage) . "' dans notre système.<br><br>💡 <i>Conseil : Essayez d'écrire uniquement un mot-clé comme 'Filtre', 'Peugeot', 'Toyota' ou 'Frein'.</i>";
}

echo json_encode(['reply' => $reply]);
exit;