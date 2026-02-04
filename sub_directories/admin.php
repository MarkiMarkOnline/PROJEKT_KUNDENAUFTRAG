<?php
require_once __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<main class="grid-administration" id="admin">
    <div class="module">
        <h3>Benutzerverwaltung</h3>
        <button class="admin-btn">Mitarbeiter hinzufügen</button>
    </div>
    
    <div class="module">
        <h3>Systemverwaltung</h3>
        <button class="admin-btn">Lagerverwaltung</button>
    </div>
    
    <div class="module">
        <h3>Einstellungen</h3>
        <button class="admin-btn">Rechte und Zugänge ändern</button>
    </div>
    
    <div class="module">
        <h3>Buchhalterische Einstellungen</h3>
        <button class="admin-btn">Buchhalterische Einstellungen</button>
    </div>
    
    <div class="module">
        <h3>Systemeinstellungen</h3>
        <button class="admin-btn">Systemeinstellungen</button>
    </div>
    
    <div class="module">
        <h3>Protokolle einsehen</h3>
        <button class="admin-btn">Protokolle einsehen</button>
    </div>
    
    <div class="module">
        <h3>Backup und Wiederherstellung</h3>
        <button class="admin-btn">Backup und Wiederherstellung</button>
    </div>
    
    <div class="module">
        <h3>Entwicklerzugang</h3>
        <button class="admin-btn">Entwicklerzugang</button>
    </div>
    
    <div class="module login-module admin-box">
        <button class="admin-btn admin-btn-danger">Logout</button>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>