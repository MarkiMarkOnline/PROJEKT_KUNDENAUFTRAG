<?php
require_once __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>


<main class="grid-lagerverwaltung" id="lager">
    <div class="module lager-module" id="bestand">
        <h2>Bestandspflege</h2>
        <p>Einpflegen von Bestellungen</p>
        <p>Bestände aktualisieren</p>
    </div>
    
    <div class="module lager-module" id="berichte">
        <h2>Berichte</h2>
        <p>Berichte von Artikel mit geringem Bestand</p>
        <p>Berichte von Artikel mit hohem Bestand</p>
        <p>Berichte von Artikeln mit niedrigem MHD</p>
        <p>Umsatzberichte</p>
    </div>
    
    <div class="module lager-module" id="verwaltung">
        <h2>Verwaltung</h2>
        <p>Hinzufügen neuer Artikelvarianten</p>
        <p>Entfernen von Artikelvarianten</p>
        <p>Hinzufügen von Lieferanten</p>
        <p>Entfernen von Lieferanten</p>
    </div>
    
    <div class="module report-module">
        <h2>Bericht über:</h2>
        <label>Zeige alle Artikel mit einem:</label>
        <select>
            <option>Bestand</option>
            <option>MHD</option>
        </select>
        <select>
            <option>größer</option>
            <option>kleiner</option>
        </select>
        <input type="number" placeholder="0">
        <button>OK</button>
    </div>
    
    <div class="module table-module">
        <h2>Artikelliste</h2>
        <p>Hier würde die Tabelle mit den Ergebnissen erscheinen...</p>
    </div>
</main>


<?php include __DIR__ . '/../includes/footer.php'; ?>