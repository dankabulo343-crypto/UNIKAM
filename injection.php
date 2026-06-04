<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db.php';

try {
    // 1. DESACTIVER LES VERIFICATIONS DES CLES ETRANGERES
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // 2. ON SUPPRIME L'ANCIENNE TABLE BLOQUEE
    $pdo->exec("DROP TABLE IF EXISTS orders");
    
    // 3. ON RECRÉE LA TABLE PROPREMENT AVEC 'client_name'
    $pdo->exec("CREATE TABLE orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(100) NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'En attente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // 4. ON INSÈRE LES DONNÉES DE TEST POUR TES STATISTIQUES
    $sql = "INSERT INTO orders (client_name, total_price, status) VALUES 
            ('Jean Dupont', 145.50, 'En attente'),
            ('Marie Kone', 89.99, 'Payée'),
            ('Anaclet Mwamba', 230.00, 'Livrée')";
            
    $pdo->exec($sql);
    
    // 5. ON REACTIVE IMPERATIVEMENT LES VERIFICATIONS POUR LA SECURITE
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<h1 style='color: #19c37d; font-family: sans-serif; text-align: center; margin-top: 50px;'>
            ✅ Victoire ! La table a été réinitialisée et configurée avec succès.
          </h1>";
    echo "<p style='text-align: center;'><a href='dashboard.php' style='color: #19c37d; font-weight: bold; font-size: 18px;'>Accéder au Tableau de Bord de l'I.A. et de la Gestion</a></p>";

} catch (Exception $e) {
    // En cas de problème, on réactive quand même les clés étrangères par sécurité
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<h1 style='color: #e63946; font-family: sans-serif; text-align: center; margin-top: 50px;'>
            ❌ Erreur lors de l'injection
          </h1>";
    echo "<p style='text-align: center; color: red; background: #222; padding: 15px; font-family: monospace;'>" . $e->getMessage() . "</p>";
}