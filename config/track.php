<?php
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$ip = $_SERVER['REMOTE_ADDR'];
$page = basename($_SERVER['PHP_SELF']);

try {
    $stmt = $pdo->prepare("INSERT INTO visites (ip_address, page_visited, visited_at) VALUES (?, ?, NOW())");
    $stmt->execute([$ip, $page]);
} catch (Exception $e) {
    // Échoue silencieusement pour ne pas bloquer l'utilisateur
}
?>