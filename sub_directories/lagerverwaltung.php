<?php
require_once __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>


<main class="grid">
    <!-- Bestandspflege Modul -->
    <div class="module lager-module" id="bestand">
        <h2>Bestandspflege</h2>
        <p>Einpflegen von Bestellungen</p>
        <p>Bestände aktualisieren</p>
    </div>

    <!-- Berichte Modul -->
    <div class="module lager-module" id="berichte">
        <h2>Berichte</h2>
        <p>Berichte von Artikel mit geringem Bestand</p>
        <p>Berichte von Artikel mit hohem Bestand</p>
        <p>Berichte von Artikeln mit niedrigem MHD</p>
        <p>Umsatzberichte</p>
    </div>

    <!-- Verwaltung Modul -->
    <div class="module lager-module" id="verwaltung">
        <h2>Verwaltung</h2>
        <p>Hinzufügen neuer Artikelvarianten</p>
        <p>Entfernen von Artikelvarianten</p>
        <p>Hinzufügen von Lieferanten</p>
        <p>Entfernen von Lieferanten</p>
    </div>

    <!-- Bericht erstellen Modul (volle Breite) -->
    <div class="module lager-module report-module">
        <h2>Bericht über:</h2>
        <label for="art">Zeige alle Artikel mit einem:</label>
        <select id="art" name="genre">
            <option value="bestand">Bestand</option>
            <option value="mhd">MHD</option>
            <option value="umsatz">Umsatz</option>
        </select>

        <select id="vergleich" name="vergleich">
            <option value="groesser">größer</option>
            <option value="kleiner">kleiner</option>
        </select>

        <input type="number" id="anzahl" name="anzahl">

        <button type="button" class="admin-btn">OK</button>
    </div>

    <!-- Tabellen-Ansicht Modul (volle Breite) -->
    <div class="module table-module">
        <p>Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle </p>
        <p>Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle</p>
        <p>Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle Tabelle</p>
        <button type="button" class="admin-btn">Bericht drucken oder versenden</button>       
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>