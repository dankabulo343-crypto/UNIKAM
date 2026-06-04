<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'includes/header.php';

$reponse_ia = "";
$question_utilisateur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['question'])) {
    $question_utilisateur = htmlspecialchars($_POST['question']);
    
    // URL d'un modèle d'IA gratuit et open-source (Mistral)
    $url = "https://api-inference.huggingface.co/models/MistralAI/Mistral-7B-Instruct-v0.3";
    
    // Prompt pour orienter l'IA sur ton projet SNCC Parts
    $prompt = "<s>[INST] Tu es l'assistant technique du site SNCC Parts (Groupe 2) à Kamina. Réponds en français de manière très courte et professionnelle à cette question : " . $question_utilisateur . " [/INST]";
    
    $data = [
        "inputs" => $prompt,
        "parameters" => ["max_new_tokens" => 150, "temperature" => 0.7]
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result) {
        $response_data = json_decode($result, true);
        
        // Extraction du texte généré par l'IA
        if (isset($response_data[0]['generated_text'])) {
            $texte_complet = $response_data[0]['generated_text'];
            // On nettoie la réponse pour enlever le prompt de départ
            $reponse_ia = str_replace($prompt, "", $texte_complet);
        } elseif (isset($response_data['error'])) {
            $reponse_ia = "🤖 L'IA se réveille... (Le serveur gratuit démarre, réessaye dans 10 secondes).";
        } else {
            $reponse_ia = "🤖 Je n'ai pas pu analyser la réponse. Réessaye.";
        }
    } else {
        $reponse_ia = "🤖 Connexion impossible. Vérifie ton accès Internet.";
    }
}
?>

<div style="max-width: 750px; margin: 40px auto; padding: 0 20px; font-family: 'Segoe UI', sans-serif;">
    <h2 style="color: #e63946; border-bottom: 2px solid #e63946; padding-bottom: 10px; margin-bottom: 30px;">💬 Assistant IA Autonome & Gratuit - Groupe 2</h2>
    
    <div style="background: #1e1e1f; border: 1px solid #333; border-radius: 8px; padding: 25px; margin-bottom: 20px; min-height: 200px; display: flex; flex-direction: column; justify-content: space-between;">
        <div id="zone-discussion">
            <?php if (!empty($question_utilisateur)): ?>
                <p style="color: #3498db; margin-bottom: 15px;"><strong>👤 Vous :</strong> <?= $question_utilisateur ?></p>
                <div style="color: #fff; background: #131314; padding: 15px; border-radius: 6px; border-left: 4px solid #19c37d; white-space: pre-wrap;">
                    <strong>🤖 Assistant GROUPE 2 :</strong><br><?= htmlspecialchars(trim($reponse_ia)) ?>
                </div>
            <?php else: ?>
                <p style="color: #aaa; font-style: italic; text-align: center; margin-top: 50px;">Posez n'importe quelle question mécanique ou technique à l'IA.</p>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST" style="display: flex; gap: 10px;">
        <input type="text" name="question" placeholder="Posez votre question ici..." required style="flex: 1; padding: 15px; background: #1e1e1f; color: #fff; border: 1px solid #444; border-radius: 6px; font-size: 15px;">
        <button type="submit" style="background: #e63946; color: white; border: none; padding: 0 25px; font-weight: bold; border-radius: 6px; cursor: pointer; font-size: 15px;">Envoyer 🚀</button>
    </form>
</div>
</body>
</html>