<?php
$ADMIN_HASH = '$2y$10$tGycFsMDxO/DCcFSiLp9Bul27H0H6k0F8O2.1ulYPcDyRQrPN0IPO';

// Admin-Prüfung
if (isset($_POST['check_admin'])) {
    $pw = $_POST['password'] ?? '';

    if (password_verify($pw, $ADMIN_HASH)) {
        echo 'OK';
    } else {
        echo 'FAIL';
    }
    exit;
}

// Datenbankverbindung einbinden
require_once __DIR__ . '/../config.php';

// config.php wird durch header.php geladen
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';


// Steuersatz speichern - für t_mwst Tabelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['steuersatz'])) {
    $steuersatz = (float)$_POST['steuersatz'];

    // Steuersatz in t_mwst Tabelle einfügen
    $stmt = $pdo->prepare("INSERT INTO t_mwst (mwst) VALUES (:mwst)");
    $stmt->execute([':mwst' => $steuersatz]);

    if (isset($_POST['speichern_schliessen'])) {
        echo "<script>
            alert('Steuersatz gespeichert');
            document.getElementById('buchhaltungModal').style.display = 'none';
        </script>";
    } else {
        echo "<script>alert('Steuersatz gespeichert');</script>";
    }
}

// Mitarbeiter speichern - für t_mitarbeiter Tabelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_mitarbeiter'])) {
    $vorname = $_POST['vorname'] ?? '';
    $nachname = $_POST['nachname'] ?? '';
    // rolle: 1 = Admin, 0 = Mitarbeiter
    $rolle = isset($_POST['rolle']) && $_POST['rolle'] === 'admin' ? 1 : 0;

    // Mitarbeiter in t_mitarbeiter Tabelle einfügen
    $stmt = $pdo->prepare("INSERT INTO t_mitarbeiter (vorname, nachname, rolle) VALUES (:vorname, :nachname, :rolle)");
    $stmt->execute([
        ':vorname' => $vorname,
        ':nachname' => $nachname,
        ':rolle' => $rolle
    ]);

    if (isset($_POST['speichern_schliessen'])) {
        echo "<script>
            alert('Mitarbeiter gespeichert');
            document.getElementById('benutzerModal').style.display = 'none';
        </script>";
    } else {
        echo "<script>alert('Mitarbeiter gespeichert');</script>";
    }
}
?>



<main class="grid-administration" id="admin">
    <div class="module">
        <h3>Benutzerverwaltung</h3>
        <button class="admin-btn" data-modal="benutzerModal">Mitarbeiter hinzufügen</button>
    </div>

    <div class="module">
        <h3>Lagerverwaltung</h3>
        <button class="admin-btn" data-modal="systemModal">Lagerverwaltung</button>
    </div>

    <div class="module">
        <h3>Einstellungen</h3>
        <button class="admin-btn" data-modal="einstellungenModal" data-no-login="true">Rechte und Zugänge ändern</button>
    </div>

    <div class="module">
        <h3>Buchhalterische Einstellungen</h3>
        <button class="admin-btn" data-modal="buchhaltungModal">Buchhalterische Einstellungen</button>
    </div>

    <div class="module">
        <h3>Protokolle einsehen</h3>
        <button class="admin-btn" data-modal="protokolleModal">Protokolle einsehen</button>
    </div>

    <div class="module">
        <h3>Backup und Wiederherstellung</h3>
        <button class="admin-btn" data-modal="backupModal">Backup und Wiederherstellung</button>
    </div>
</main>

<!-- Modal: Benutzerverwaltung -->
<div id="benutzerModal" class="modal-overlay">
    <div class="module modal-content">
        <div id="benutzerAdminStatus" class="admin-status">Sie sind als Admin eingeloggt</div>

        <div class="login-box">
            <h3>Admin Anmeldung</h3>
            <p>Geben Sie Admin Kennwort ein</p>
            <input type="password" class="admin-password-input" style="width:100%; padding:8px;">
            <button class="admin-btn login-btn">Anmelden</button>
            <button class="admin-btn admin-btn-secondary cancel-btn">Abbrechen</button>
            <p class="login-fehler">Falsches Kennwort</p>
        </div>

        <form class="content-form" method="post">
            <h3>Neuen Mitarbeiter hinzufügen</h3>

            <label>Geben Sie den Vornamen ein</label><br>
            <input type="text" name="vorname" required style="width:100%; padding:8px; margin-bottom:15px;"><br>

            <label>Geben Sie den Nachnamen ein</label><br>
            <input type="text" name="nachname" required style="width:100%; padding:8px; margin-bottom:15px;"><br>

            <label>Wählen Sie die Rolle</label><br>
            <div style="margin: 10px 0;">
                <input type="radio" id="rolle_mitarbeiter" name="rolle" value="mitarbeiter" checked>
                <label for="rolle_mitarbeiter" style="margin-left: 5px;">Mitarbeiter</label><br>
                <input type="radio" id="rolle_admin" name="rolle" value="admin">
                <label for="rolle_admin" style="margin-left: 5px;">Admin</label>
            </div><br>

            <button type="submit" name="save_mitarbeiter" class="admin-btn">Speichern</button>
            <button type="submit" name="save_mitarbeiter" onclick="this.form.speichern_schliessen.value='1'" class="admin-btn">Speichern und Schließen</button>
            <input type="hidden" name="speichern_schliessen" value="0">
            <button type="button" class="admin-btn admin-btn-secondary close-without-save">Schließen ohne Speichern</button>
        </form>
    </div>
</div>

<!-- Modal: Systemverwaltung -->
<div id="systemModal" class="modal-overlay">
    <div class="module modal-content">
        <div id="systemAdminStatus" class="admin-status">Sie sind als Admin eingeloggt</div>

        <div class="login-box">
            <h3>Admin Anmeldung</h3>
            <p>Geben Sie Admin Kennwort ein</p>
            <input type="password" class="admin-password-input" style="width:100%; padding:8px;">
            <button class="admin-btn login-btn">Anmelden</button>
            <button class="admin-btn admin-btn-secondary cancel-btn">Abbrechen</button>
            <p class="login-fehler">Falsches Kennwort</p>
        </div>

        <div class="content-form">
            <h3>Systemverwaltung</h3>
            <p>Systemverwaltungsfunktionen werden hier angezeigt.</p>
            
            <button type="button" class="admin-btn">Lagerverwaltung starten</button>
            <button type="button" class="admin-btn">Systemdiagnose</button>
            <button type="button" class="admin-btn admin-btn-secondary close-without-save">Schließen</button>
        </div>
    </div>
</div>

<!-- Modal: Einstellungen (ohne Login) -->
<div id="einstellungenModal" class="modal-overlay">
    <div class="module modal-content">
        <h3>Einstellungen</h3>

        <div style="margin: 20px 0;">
            <label style="font-weight: bold;">Theme-Auswahl</label><br>
            <div style="margin: 10px 0;">
                <input type="radio" id="theme_hofladen" name="theme" value="hofladen" checked>
                <label for="theme_hofladen" style="margin-left: 5px;">Hofladen Theme</label><br>
                <input type="radio" id="theme_dunkel" name="theme" value="dunkel">
                <label for="theme_dunkel" style="margin-left: 5px;">Dunkles Theme</label><br>
                <input type="radio" id="theme_hell" name="theme" value="hell">
                <label for="theme_hell" style="margin-left: 5px;">Helles Theme</label>
            </div>
        </div>

        <div style="margin: 20px 0;">
            <label style="font-weight: bold;">Sprache</label><br>
            <div style="margin: 10px 0;">
                <input type="radio" id="sprache_de" name="sprache" value="deutsch" checked>
                <label for="sprache_de" style="margin-left: 5px;">Deutsch</label><br>
                <input type="radio" id="sprache_en" name="sprache" value="englisch">
                <label for="sprache_en" style="margin-left: 5px;">Englisch</label>
            </div>
        </div>

        <div style="margin: 20px 0;">
            <label style="font-weight: bold;">Benachrichtigungen</label><br>
            <div style="margin: 10px 0;">
                <input type="radio" id="notif_an" name="notifications" value="an" checked>
                <label for="notif_an" style="margin-left: 5px;">An</label><br>
                <input type="radio" id="notif_aus" name="notifications" value="aus">
                <label for="notif_aus" style="margin-left: 5px;">Aus</label>
            </div>
        </div>

        <button class="admin-btn" onclick="alert('Einstellungen gespeichert');">Speichern</button>
        <button class="admin-btn admin-btn-secondary" onclick="document.getElementById('einstellungenModal').style.display='none'">Schließen</button>
    </div>
</div>

<!-- Modal: Buchhalterische Einstellungen -->
<div id="buchhaltungModal" class="modal-overlay">
    <div class="module modal-content">
        <div id="buchhaltungAdminStatus" class="admin-status">Sie sind als Admin eingeloggt</div>

        <div class="login-box">
            <h3>Admin Anmeldung</h3>
            <p>Geben Sie Admin Kennwort ein</p>
            <input type="password" class="admin-password-input" style="width:100%; padding:8px;">
            <button class="admin-btn login-btn">Anmelden</button>
            <button class="admin-btn admin-btn-secondary cancel-btn">Abbrechen</button>
            <p class="login-fehler">Falsches Kennwort</p>
        </div>

        <form class="content-form" method="post">
            <h3>Neuen Steuersatz festlegen</h3>

            <label>Neuer Steuersatz (%)</label><br>
            <input type="number" name="steuersatz" min="0" max="100" step="0.01" style="width:100%; padding:8px;"><br><br>

            <button type="submit" name="speichern" class="admin-btn">Speichern</button>
            <button type="submit" name="speichern_schliessen" class="admin-btn">Speichern und Schließen</button>
            <button type="button" class="admin-btn admin-btn-secondary close-without-save">Schließen ohne Speichern</button>
        </form>
    </div>
</div>

<!-- Modal: Protokolle einsehen -->
<div id="protokolleModal" class="modal-overlay">
    <div class="module modal-content">
        <div id="protokolleAdminStatus" class="admin-status">Sie sind als Admin eingeloggt</div>

        <div class="login-box">
            <h3>Admin Anmeldung</h3>
            <p>Geben Sie Admin Kennwort ein</p>
            <input type="password" class="admin-password-input" style="width:100%; padding:8px;">
            <button class="admin-btn login-btn">Anmelden</button>
            <button class="admin-btn admin-btn-secondary cancel-btn">Abbrechen</button>
            <p class="login-fehler">Falsches Kennwort</p>
        </div>

        <div class="content-form">
            <h3>Protokolle einsehen</h3>

            <label>Wählen Sie ein Protokoll</label><br>
            <select id="protokollAuswahl" style="width:100%; padding:8px; margin: 10px 0;">
                <option value="">-- Bitte wählen --</option>
                <option value="protokoll1.jpg">Verkaufsprotokoll</option>
                <option value="protokoll2.jpg">Lagerprotokoll</option>
                <option value="protokoll3.jpg">Systemprotokoll</option>
            </select><br>

            <button type="button" class="admin-btn" onclick="zeigeProtokoll()">Erstellen</button>

            <div id="protokollAnzeige" style="margin-top: 20px; display: none;">
                <img id="protokollBild" src="" alt="Protokoll" style="max-width: 100%; border: 1px solid #ccc;">
            </div>

            <button type="button" class="admin-btn" onclick="speichereProtokoll()">Protokoll speichern</button>
            <button type="button" class="admin-btn admin-btn-secondary close-without-save">Schließen</button>
        </div>
    </div>
</div>

<!-- Modal: Backup und Wiederherstellung -->
<div id="backupModal" class="modal-overlay">
    <div class="module modal-content">
        <div id="backupAdminStatus" class="admin-status">Sie sind als Admin eingeloggt</div>

        <div class="login-box">
            <h3>Admin Anmeldung</h3>
            <p>Geben Sie Admin Kennwort ein</p>
            <input type="password" class="admin-password-input" style="width:100%; padding:8px;">
            <button class="admin-btn login-btn">Anmelden</button>
            <button class="admin-btn admin-btn-secondary cancel-btn">Abbrechen</button>
            <p class="login-fehler">Falsches Kennwort</p>
        </div>

        <div class="content-form">
            <h3>Backup und Wiederherstellung</h3>

            <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin: 15px 0; border-radius: 4px;">
                <strong>Achtung!</strong> Backups werden täglich erstellt.
            </div>

            <label>Verzeichnis auswählen</label><br>
            <input type="text" id="backupVerzeichnis" placeholder="z.B. /pfad/zum/backup" style="width:100%; padding:8px; margin: 10px 0;"><br>

            <button type="button" class="admin-btn" onclick="erstelleBackup()">Backup erstellen</button>
            <button type="button" class="admin-btn" onclick="wiederherstellenBackup()">Wiederherstellen</button>
            <button type="button" class="admin-btn admin-btn-secondary close-without-save">Schließen</button>
        </div>
    </div>
</div>


<style>
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 999;
}

.modal-content {
    max-width: 600px;
    margin: 100px auto;
    position: relative;
    max-height: 80vh;
    overflow-y: auto;
}

.admin-status {
    position: absolute;
    top: 10px;
    right: 10px;
    font-weight: bold;
    display: none;
    color: var(--primary-green);
}

.login-box {
    display: block;
}

.content-form {
    display: none;
}

.login-fehler {
    color: red;
    display: none;
    margin-top: 10px;
}

.admin-btn-secondary {
    background: #6c757d;
}

.admin-btn-secondary:hover {
    background: #5a6268;
}
</style> 

<script>
// Allgemeine Modal-Verwaltung
const buttons = document.querySelectorAll('[data-modal]');

buttons.forEach(btn => {
    btn.addEventListener('click', () => {
        const modalId = btn.getAttribute('data-modal');
        const noLogin = btn.getAttribute('data-no-login');
        const modal = document.getElementById(modalId);
        
        if (modal) {
            modal.style.display = 'block';
            
            // Für Einstellungen: Login überspringen
            if (noLogin) {
                const loginBox = modal.querySelector('.login-box');
                const contentForm = modal.querySelector('.content-form');
                if (loginBox) loginBox.style.display = 'none';
                if (contentForm) contentForm.style.display = 'block';
            }
        }
    });
});

// Login-Funktionalität für alle Modals
document.querySelectorAll('.login-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const modal = this.closest('.modal-overlay');
        const passwordInput = modal.querySelector('.admin-password-input');
        const loginBox = modal.querySelector('.login-box');
        const contentForm = modal.querySelector('.content-form');
        const adminStatus = modal.querySelector('.admin-status');
        const loginFehler = modal.querySelector('.login-fehler');
        
        const pw = passwordInput.value;

        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'check_admin=1&password=' + encodeURIComponent(pw)
        })
        .then(res => res.text())
        .then(result => {
            if (result === 'OK') {
                loginBox.style.display = 'none';
                contentForm.style.display = 'block';
                if (adminStatus) adminStatus.style.display = 'block';
                alert('Sie sind als Admin eingeloggt');
            } else {
                loginFehler.style.display = 'block';
            }
        });
    });
});

// Abbrechen-Buttons
document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const modal = this.closest('.modal-overlay');
        modal.style.display = 'none';
    });
});

// Schließen ohne Speichern
document.querySelectorAll('.close-without-save').forEach(btn => {
    btn.addEventListener('click', function() {
        const modal = this.closest('.modal-overlay');
        modal.style.display = 'none';
    });
});

// Protokoll-Funktionen
function zeigeProtokoll() {
    const auswahl = document.getElementById('protokollAuswahl').value;
    const anzeige = document.getElementById('protokollAnzeige');
    const bild = document.getElementById('protokollBild');
    
    if (auswahl) {
        bild.src = '/pfad/zu/protokollen/' + auswahl;
        anzeige.style.display = 'block';
    } else {
        alert('Bitte wählen Sie ein Protokoll aus');
    }
}

function speichereProtokoll() {
    const auswahl = document.getElementById('protokollAuswahl').value;
    if (auswahl) {
        alert('Protokoll wird gespeichert: ' + auswahl);
    } else {
        alert('Kein Protokoll zum Speichern ausgewählt');
    }
}

// Backup-Funktionen
function erstelleBackup() {
    const verzeichnis = document.getElementById('backupVerzeichnis').value;
    if (verzeichnis) {
        alert('Backup wird erstellt im Verzeichnis: ' + verzeichnis);
    } else {
        alert('Bitte geben Sie ein Verzeichnis an');
    }
}

function wiederherstellenBackup() {
    const verzeichnis = document.getElementById('backupVerzeichnis').value;
    if (verzeichnis) {
        if (confirm('Möchten Sie wirklich das Backup wiederherstellen? Alle aktuellen Daten werden überschrieben!')) {
            alert('Backup wird wiederhergestellt aus: ' + verzeichnis);
        }
    } else {
        alert('Bitte geben Sie ein Verzeichnis an');
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>