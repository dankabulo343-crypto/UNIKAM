<?php
require_once 'config/db.php';
require_once 'config/track.php';
include 'includes/header.php';
?>
<div style="position: relative; background: url('https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?q=80&w=1920&auto=format&fit=crop') no-repeat center center/cover; height: 75vh; display: flex; align-items: center; justify-content: center;">
    
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 25, 47, 0.75);"></div>
    
    <div style="position: relative; z-index: 2; max-width: 850px; text-align: center; padding: 20px; margin: 0 auto;">
        <span style="background: #e63946; color: white; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase;">Portail Officiel National</span>
        
        <h1 style="font-size: 48px; color: #fff; margin: 20px 0; font-weight: 800; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
             PIECE DE RECHANGE DES VEHICULES DE TOUTE MARQUE <span style="color: #e63946;">GROUPE 2</span>
        </h1>
        
        <p style="font-size: 19px; color: #ececf1; opacity: 0.95; margin: 0 auto 40px auto; line-height: 1.7; text-shadow: 0 1px 5px rgba(0,0,0,0.5);">
            Travail pratique d'e-commerce diriger par le CT Gabin-A-MANDE MULOPWE, en Faculté des sciences et technologies
        </p>
        
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="catalogue.php" style="background: #e63946; color: white; text-decoration: none; padding: 15px 35px; font-weight: bold; border-radius: 6px; font-size: 16px; box-shadow: 0 4px 15px rgba(230, 57, 70, 0.4); transition: 0.3s;">
                🛒 Accéder au Catalogue
            </a>
            <a href="ia.php" style="background: transparent; border: 2px solid #fff; color: white; text-decoration: none; padding: 13px 33px; font-weight: bold; border-radius: 6px; font-size: 16px; transition: 0.3s;">
                💬 Parler à l'Assistant
            </a>
        </div>
    </div>
</div>

<div style="max-width: 1100px; margin: -50px auto 50px auto; position: relative; z-index: 3; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 0 20px;">
    <div style="background: #1e1e1f; padding: 25px; border-radius: 8px; border: 1px solid #333; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <div style="font-size: 32px; font-weight: bold; color: #e63946;">100%</div>
        <div style="color: #aaa; font-size: 14px; margin-top: 5px;">Devise Locale en Francs Congolais (FC)</div>
    </div>
    <div style="background: #1e1e1f; padding: 25px; border-radius: 8px; border: 1px solid #333; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <div style="font-size: 32px; font-weight: bold; color: #19c37d;">Kamina</div>
        <div style="color: #aaa; font-size: 14px; margin-top: 5px;">Réseau Connecté Logistique Distant</div>
    </div>
    <div style="background: #1e1e1f; padding: 25px; border-radius: 8px; border: 1px solid #333; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <div style="font-size: 32px; font-weight: bold; color: #3498db;">Sécurisé</div>
        <div style="color: #aaa; font-size: 14px; margin-top: 5px;">Cryptage des sessions utilisateurs</div>
    </div>
</div>
</body>
</html>