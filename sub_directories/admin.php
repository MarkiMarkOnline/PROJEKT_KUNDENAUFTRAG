<?php
require_once __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<main class="grid">

    <div class="module admin-box">
        <p>Benutzerverwaltung</p>
        <button id="addEmployee" class="admin-btn">Mitarbeiter hinzufügen</button>
    </div>

    <div class="module admin-box">
        <p>Systemverwaltung</p>
        <button id="lagerverwaltung" class="admin-btn">Lagerverwaltung</button>
    </div>

    <div class="module admin-box">
        <p>Einstellungen</p>
        <button id="changeRights" class="admin-btn">Rechte und Zugänge ändern</button>
    </div>

    <div class="module admin-box">
        <p>Buchhalterische Einstellungen</p>
        <button id="buha" class="admin-btn">Buchhalterische Einstellungen</button>
    </div>

    <div class="module admin-box">
        <p>Systemeinstellungen</p>
        <button id="systemSettings" class="admin-btn">Systemeinstellungen</button>
    </div>

    <div class="module admin-box">
        <p>Protokolle einsehen</p>
        <button id="viewLogs" class="admin-btn">Protokolle einsehen</button>
    </div>

    <div class="module admin-box">
        <p>Backup und Wiederherstellung</p>
        <button id="backupRestore" class="admin-btn">Backup und Wiederherstellung</button>
    </div>

    <div class="module admin-box">
        <p>Entwicklerzugang</p>
        <button id="entwicklerZugang" class="admin-btn">Entwicklerzugang</button>
    </div>

    <div class="module admin-box">
        <p>Logout</p>
        <button id="logoutBtn" class="admin-btn admin-btn-danger">Logout</button>
    </div>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>