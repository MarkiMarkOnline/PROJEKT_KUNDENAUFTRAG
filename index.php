<?php
require_once __DIR__ . '/config.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>


<main class="grid">

    <a href="<?php echo BASE_URL; ?>/sub_directories/pos.php" class="module-link">
        <div class="module">
            <h2>Point of Sale</h2>
            <p>Weiterleitung zu Point of Sale</p>
        </div>
    </a>

    <a href="<?php echo BASE_URL; ?>/sub_directories/lagerverwaltung.php" class="module-link">
        <div class="module">
            <h2>Lagerverwaltung</h2>
            <p>Weiterleitung zur Lagerverwaltung</p>
        </div>
    </a>

    <a href="<?php echo BASE_URL; ?>/sub_directories/admin.php" class="module-link">
        <div class="module">
            <h2>Administration</h2>
            <p>Weiterleitung zum Admin-Panel</p>
        </div>
    </a>

    <a href="<?php echo BASE_URL; ?>/sub_directories/einstellungen.php" class="module-link">
        <div class="module">
            <h2>Einstellungen</h2>
            <p>Weiterleitung zu den Einstellungen</p>
        </div>
    </a>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>