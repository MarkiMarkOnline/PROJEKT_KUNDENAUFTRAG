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
        <h2>Bericht über:</h2>

        <label for="artikelname">Suche nach Artikel: </label>
        <input type="text" id="artikelname" name="artikelname" placeholder="Artikelname eingeben...">
        <button type="button" id="btn-artikel-suche">OK</button><br><br>

        <label for="art">Zeige alle Artikel mit einem:</label>
        <select id="art" name="art">
            <option value="bestand"> - </option>
            <option value="bestand">Bestand</option>
            <option value="preis">Preis</option>
            <option value="umsatz">Umsatz</option>
        </select>

        <select id="vergleich" name="vergleich">
            <option value="gleich">gleich</option>
            <option value="groesser">größer</option>
            <option value="kleiner">kleiner</option>
        </select>

        <input type="number" id="anzahl" name="anzahl" placeholder="Wert">

        <label for="kategorie">aus der Kategorie: </label>
        <select id="kategorie" name="kategorie">
            <option value="alle">Alle</option>
            <option value="Obst">Obst</option>
            <option value="Gemüse">Gemüse</option>
            <option value="Milchprodukt">Milchprodukt</option>
            <option value="Fleisch">Fleisch</option>
            <option value="Nüsse">Nüsse</option>
            <option value="Marmelade">Marmelade</option>
            <option value="Öl">Öl</option>
        </select>

        <select id="gruppe" name="gruppe">
            <option value="alle">Alle</option>
            <option value="Eigenerzeugnis">Eigenerzeugnis</option>
            <option value="Eigenproduktion">Eigenproduktion</option>
            <option value="Zulieferung">Zulieferung</option>
        </select>
        <button type="button" id="btn-erweiterte-suche">OK</button>
    </div>

    <div class="module table-module">
        <h2>Artikelliste</h2>
        <p>Geben Sie einen Suchbegriff ein oder wählen Sie Filter aus...</p>
    </div>
</main>

<script>
// JavaScript für Artikelsuche
document.addEventListener('DOMContentLoaded', function() {
    
    // Event Listener für den OK-Button bei der Artikelsuche
    const suchButton = document.querySelector('#artikelname + button');
    const artikelnameInput = document.getElementById('artikelname');
    const tableModule = document.querySelector('.table-module');
    
    // Event Listener für den erweiterten Such-Button
    const erweiterterSuchButton = document.getElementById('btn-erweiterte-suche');
    
    if (suchButton) {
        suchButton.addEventListener('click', artikelSuchen);
    }
    
    if (erweiterterSuchButton) {
        erweiterterSuchButton.addEventListener('click', erweiterteSuche);
    }
    
    // Enter-Taste im Input-Feld unterstützen
    if (artikelnameInput) {
        artikelnameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                artikelSuchen();
            }
        });
    }
    
    function artikelSuchen() {
        const suchbegriff = artikelnameInput.value.trim();
        
        if (!suchbegriff) {
            zeigeNachricht('Bitte geben Sie einen Suchbegriff ein.', 'warning');
            return;
        }
        
        // Lade-Animation anzeigen
        tableModule.innerHTML = '<h2>Artikelliste</h2><p class="loading">Suche läuft...</p>';
        
        // AJAX Request
        fetch('ajax_artikel_suche.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'artikelname=' + encodeURIComponent(suchbegriff)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Artikel Suche Response:', data); // Debug
            if (data.success) {
                zeigeErgebnisse(data.data, data.count, suchbegriff);
            } else {
                zeigeNachricht(data.message, 'info');
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
            <p><strong>${anzahl}</strong> Artikel gefunden für: "<em>${beschreibung}</em>"</p>
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
                    <td>${bestand}</td>
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
    
    // Erweiterte Suche mit Filtern
    function erweiterteSuche() {
        const art = document.getElementById('art').value;
        const vergleich = document.getElementById('vergleich').value;
        const anzahl = document.getElementById('anzahl').value;
        const kategorie = document.getElementById('kategorie').value;
        const gruppe = document.getElementById('gruppe').value;
        
        // Lade-Animation anzeigen
        tableModule.innerHTML = '<h2>Artikelliste</h2><p class="loading">Suche läuft...</p>';
        
        // FormData für POST-Request
        const formData = new URLSearchParams();
        formData.append('art', art);
        formData.append('vergleich', vergleich);
        formData.append('anzahl', anzahl);
        formData.append('kategorie', kategorie);
        formData.append('gruppe', gruppe);
        
        // AJAX Request
        fetch('ajax_erweiterte_suche.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        })
        .then(response => {
            // Prüfe ob die Response OK ist
            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }
            return response.text(); // Erst als Text
        })
        .then(text => {
            console.log('Raw Response:', text); // Debug: Rohe Antwort
            try {
                const data = JSON.parse(text);
                console.log('Parsed Response:', data); // Debug: Geparste Antwort
                if (data.success) {
                    let filterBeschreibung = erstelleFilterBeschreibung(art, vergleich, anzahl, kategorie, gruppe);
                    zeigeErgebnisse(data.data, data.count, filterBeschreibung);
                } else {
                    zeigeNachricht(data.message, 'info');
                }
            } catch(e) {
                console.error('JSON Parse Error:', e);
                console.error('Response Text:', text);
                zeigeNachricht('Serverfehler: Ungültige Antwort. Siehe Konsole für Details.', 'error');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            zeigeNachricht('Ein Fehler ist aufgetreten: ' + error.message, 'error');
        });
    }
    
    function erstelleFilterBeschreibung(art, vergleich, anzahl, kategorie, gruppe) {
        let teile = [];
        
        if (art && art !== '' && art !== '-' && anzahl) {
            const artText = art === 'bestand' ? 'Bestand' : art === 'preis' ? 'Preis' : 'Umsatz';
            const vergleichText = vergleich === 'groesser' ? 'größer als' : vergleich === 'kleiner' ? 'kleiner als' : 'gleich';
            teile.push(`${artText} ${vergleichText} ${anzahl}`);
        }
        
        if (kategorie && kategorie !== 'alle') {
            teile.push(`Kategorie: ${kategorie}`);
        }
        
        if (gruppe && gruppe !== 'alle') {
            teile.push(`Herkunft: ${gruppe}`);
        }
        
        return teile.length > 0 ? teile.join(' | ') : 'Alle Artikel';
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>