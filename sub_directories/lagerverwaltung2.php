<?php
require_once __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>

<style>
/* Tabellen-Styling für Artikelsuche */
.table-wrapper {
    overflow-x: auto;
    margin-top: 1rem;
}

/* Klickbare Module als Links */
a.module {
    text-decoration: none;
    color: inherit;
    display: block;
    transition: transform 0.2s, box-shadow 0.2s;
}

a.module:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    cursor: pointer;
}

.artikel-tabelle {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.artikel-tabelle thead {
    background: #4CAF50;
    color: white;
}

.artikel-tabelle th {
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
    font-size: 0.95rem;
}

.artikel-tabelle td {
    padding: 10px 15px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 0.9rem;
}

.artikel-tabelle tbody tr:hover {
    background-color: #f5f5f5;
    cursor: pointer;
}

.artikel-tabelle tbody tr:last-child td {
    border-bottom: none;
}

/* Nachrichten-Styling */
.table-module p.loading {
    color: #2196F3;
    font-style: italic;
    padding: 20px;
    text-align: center;
}

.table-module p.error {
    color: #f44336;
    background: #ffebee;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #f44336;
}

.table-module p.warning {
    color: #ff9800;
    background: #fff3e0;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #ff9800;
}

.table-module p.info {
    color: #2196F3;
    background: #e3f2fd;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #2196F3;
}

/* Responsive Design */
@media (max-width: 768px) {
    .artikel-tabelle {
        font-size: 0.85rem;
    }
    
    .artikel-tabelle th,
    .artikel-tabelle td {
        padding: 8px 10px;
    }
}
</style>

<main class="grid-lagerverwaltung" id="lager">
    <a href="lagerverwaltung2.php" class="module lager-module" id="bestand">
        <h2>Bestandspflege</h2>
        <p>Einpflegen von Bestellungen</p>
        <p>Bestände aktualisieren</p>
    </a>

    <a href="lagerverwaltung.php" class="module lager-module" id="berichte">
        <h2>Berichte</h2>
        <p>Berichte von Artikel mit geringem Bestand</p>
        <p>Berichte von Artikel mit hohem Bestand</p>
        <p>Berichte von Artikeln mit niedrigem MHD</p>
        <p>Umsatzberichte</p>
    </a>

    <div class="module lager-module" id="verwaltung">
        <h2>Verwaltung</h2>
        <p>Hinzufügen neuer Artikelvarianten</p>
        <p>Entfernen von Artikelvarianten</p>
        <p>Hinzufügen von Lieferanten</p>
        <p>Entfernen von Lieferanten</p>
    </div>

    <div class="module report-module">
        <h2>Bestand ändern:</h2>
        <label for="artikelid">ID:</label>
        <input type="number" id="artikelid" name="artikelid" placeholder="Artikel ID">
        <label for="addbestand">Bestandsänderung (Addierung):</label>
        <input type="number" step="0.01" id="addbestand" name="addbestand" placeholder="+ / - Wert">
        <button type="button" id="btn-bestandsadd">OK</button><br><br>
    </div>

    <div class="module table-module">
        <h2>Artikelliste</h2>
        <p>Gebe nur eine ID ein um den Artikel aufzurufen, gib eine ID und eine Bestandsänderung ein, um den Bestand zu aktualisieren.</p>
    </div>
</main>

<script>
// JavaScript für Bestandspflege
document.addEventListener('DOMContentLoaded', function() {
    
    const artikelidInput = document.getElementById('artikelid');
    const tableModule = document.querySelector('.table-module');
    const bestandAddButton = document.getElementById('btn-bestandsadd');
    const addbestandInput = document.getElementById('addbestand');
    
    if (bestandAddButton) {
        bestandAddButton.addEventListener('click', intelligenterButton);
    }
    
    // Enter-Taste in beiden Feldern unterstützen
    if (artikelidInput) {
        artikelidInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                intelligenterButton();
            }
        });
    }
    
    if (addbestandInput) {
        addbestandInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                intelligenterButton();
            }
        });
    }
    
    // Intelligenter Button: entscheidet zwischen Suche und Update
    function intelligenterButton() {
        const artikelid = artikelidInput.value.trim();
        const bestandsaenderung = addbestandInput.value.trim();
        
        if (!artikelid) {
            zeigeNachricht('Bitte geben Sie eine Artikel-ID ein.', 'warning');
            return;
        }
        
        // Wenn Bestandsänderung leer ist: nur Suche
        if (!bestandsaenderung || bestandsaenderung === '') {
            artikelSuchenById();
        } else {
            // Wenn Bestandsänderung ausgefüllt: Update durchführen
            bestandAktualisieren();
        }
    }
    
    // Funktion: Artikel nach ID suchen
    function artikelSuchenById() {
        const artikelid = artikelidInput.value.trim();
        
        // Lade-Animation anzeigen
        tableModule.innerHTML = '<h2>Artikelliste</h2><p class="loading">Suche läuft...</p>';
        
        // AJAX Request
        fetch('ajax_artikel_suche_id.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'artikelid=' + encodeURIComponent(artikelid)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Artikel ID Suche Response:', data);
            if (data.success) {
                zeigeErgebnisse(data.data, data.count, 'ID: ' + artikelid);
            } else {
                zeigeNachricht(data.message, 'info');
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
            zeigeNachricht('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 'error');
        });
    }
    
    // Funktion: Bestand aktualisieren
    function bestandAktualisieren() {
        const artikelid = artikelidInput.value.trim();
        const bestandsaenderung = addbestandInput.value.trim();
        
        // Lade-Animation anzeigen
        tableModule.innerHTML = '<h2>Artikelliste</h2><p class="loading">Bestand wird aktualisiert...</p>';
        
        // AJAX Request
        fetch('ajax_bestand_update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'artikelid=' + encodeURIComponent(artikelid) + '&bestandsaenderung=' + encodeURIComponent(bestandsaenderung)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Bestand Update Response:', data);
            if (data.success) {
                // Erfolgsmeldung mit Details
                const aenderung = data.aenderung;
                const meldung = `✓ ${aenderung.artikel}: ${aenderung.alter_bestand} → ${aenderung.neuer_bestand} (${aenderung.differenz > 0 ? '+' : ''}${aenderung.differenz})`;
                
                zeigeErgebnisse(data.data, data.count, meldung);
                
                // Bestandsänderungs-Feld leeren (ID bleibt für weitere Buchungen)
                addbestandInput.value = '';
            } else {
                zeigeNachricht(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
            zeigeNachricht('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 'error');
        });
    }
    
    function zeigeErgebnisse(artikel, anzahl, beschreibung) {
        let html = `
            <h2>Artikelliste</h2>
            <p><strong>${anzahl}</strong> Artikel: <em>${beschreibung}</em></p>
            <div class="table-wrapper">
                <table class="artikel-tabelle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Artikelbezeichnung</th>
                            <th>Preis (€)</th>
                            <th>Einheit</th>
                            <th>Lagerbestand</th>
                            <th>MwSt (%)</th>
                            <th>Warengruppe</th>
                            <th>Herkunft</th>
                            <th>Lieferant</th>
                            <th>Saisonware</th>
                            <th>Saison</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        artikel.forEach(a => {
            const preis = parseFloat(a.preis).toFixed(2);
            const bestand = parseFloat(a.lagerbestand).toFixed(2);
            const mwst = a.mwst_satz ? parseFloat(a.mwst_satz).toFixed(2) : '-';
            const saisonware = a.saisonware == 1 ? 'Ja' : 'Nein';
            const saison = a.saisonware == 1 
                ? `${formatDatum(a.saisonstart)} - ${formatDatum(a.saisonende)}`
                : '-';
            
            html += `
                <tr>
                    <td>${a.id_artikel}</td>
                    <td>${a.artikelbezeichnung}</td>
                    <td>${preis}</td>
                    <td>${a.einheit || '-'}</td>
                    <td><strong>${bestand}</strong></td>
                    <td>${mwst}</td>
                    <td>${a.warengruppen || '-'}</td>
                    <td>${a.herkuenfte || '-'}</td>
                    <td>${a.lieferanten || '-'}</td>
                    <td>${saisonware}</td>
                    <td>${saison}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        tableModule.innerHTML = html;
    }
    
    function zeigeNachricht(nachricht, typ) {
        const cssClass = typ === 'error' ? 'error' : typ === 'warning' ? 'warning' : 'info';
        tableModule.innerHTML = `
            <h2>Artikelliste</h2>
            <p class="${cssClass}">${nachricht}</p>
        `;
    }
    
    function formatDatum(datum) {
        if (!datum) return '-';
        const d = new Date(datum);
        return d.toLocaleDateString('de-DE');
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>