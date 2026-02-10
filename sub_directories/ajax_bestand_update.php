<?php
// ajax_bestand_update.php
// Speichere diese Datei im selben Verzeichnis wie lagerverwaltung2.php

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Validierung
if (!isset($_POST['artikelid']) || empty(trim($_POST['artikelid']))) {
    echo json_encode(['success' => false, 'message' => 'Bitte geben Sie eine Artikel-ID ein.']);
    exit;
}

if (!isset($_POST['bestandsaenderung']) || $_POST['bestandsaenderung'] === '') {
    echo json_encode(['success' => false, 'message' => 'Bitte geben Sie eine Bestandsänderung ein.']);
    exit;
}

$artikelid = intval($_POST['artikelid']);
$bestandsaenderung = floatval($_POST['bestandsaenderung']);

try {
    // Prüfen ob Artikel existiert
    $stmt_check = $pdo->prepare("SELECT id_artikel, artikelbezeichnung, lagerbestand FROM t_artikel WHERE id_artikel = ?");
    $stmt_check->execute([$artikelid]);
    $artikel = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if (!$artikel) {
        echo json_encode([
            'success' => false,
            'message' => 'Artikel mit ID ' . $artikelid . ' nicht gefunden.'
        ]);
        exit;
    }
    
    $alter_bestand = floatval($artikel['lagerbestand']);
    $neuer_bestand = $alter_bestand + $bestandsaenderung;
    
    // Bestand aktualisieren
    $stmt_update = $pdo->prepare("UPDATE t_artikel SET lagerbestand = ? WHERE id_artikel = ?");
    $stmt_update->execute([$neuer_bestand, $artikelid]);
    
    // Aktualisierten Artikel mit allen Details holen
    $stmt = $pdo->prepare("
        SELECT 
            a.id_artikel,
            a.artikelbezeichnung,
            a.preis,
            a.lagerbestand,
            a.saisonware,
            a.saisonstart,
            a.saisonende,
            a.fk_einheit,
            a.fk_mwst
        FROM t_artikel a
        WHERE a.id_artikel = ?
    ");
    
    $stmt->execute([$artikelid]);
    $artikel_neu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Einheit holen
    if ($artikel_neu['fk_einheit']) {
        $stmt_einheit = $pdo->prepare("SELECT einheit FROM t_einheit WHERE id_einheit = ?");
        $stmt_einheit->execute([$artikel_neu['fk_einheit']]);
        $einheit_row = $stmt_einheit->fetch(PDO::FETCH_ASSOC);
        $artikel_neu['einheit'] = $einheit_row ? $einheit_row['einheit'] : null;
    } else {
        $artikel_neu['einheit'] = null;
    }
    
    // MwSt holen
    if ($artikel_neu['fk_mwst']) {
        $stmt_mwst = $pdo->prepare("SELECT mwst FROM t_mwst WHERE id_mwst = ?");
        $stmt_mwst->execute([$artikel_neu['fk_mwst']]);
        $mwst_row = $stmt_mwst->fetch(PDO::FETCH_ASSOC);
        $artikel_neu['mwst_satz'] = $mwst_row ? $mwst_row['mwst'] : null;
    } else {
        $artikel_neu['mwst_satz'] = null;
    }
    
    // Warengruppen holen
    $stmt_warengruppe = $pdo->prepare("
        SELECT w.warengruppe 
        FROM vt_artikel_warengruppe vw
        JOIN t_warengruppe w ON vw.fk_warengruppe = w.id_warengruppe
        WHERE vw.fk_artikel = ?
    ");
    $stmt_warengruppe->execute([$artikel_neu['id_artikel']]);
    $warengruppen = $stmt_warengruppe->fetchAll(PDO::FETCH_COLUMN);
    $artikel_neu['warengruppen'] = $warengruppen ? implode(', ', $warengruppen) : '-';
    
    // Herkunft holen
    $stmt_herkunft = $pdo->prepare("
        SELECT h.herkunft 
        FROM vt_artikel_herkunft vh
        JOIN t_herkunft h ON vh.fk_herkunft = h.id_herkunft
        WHERE vh.fk_artikel = ?
    ");
    $stmt_herkunft->execute([$artikel_neu['id_artikel']]);
    $herkuenfte = $stmt_herkunft->fetchAll(PDO::FETCH_COLUMN);
    $artikel_neu['herkuenfte'] = $herkuenfte ? implode(', ', $herkuenfte) : '-';
    
    // Lieferanten holen
    $stmt_lieferant = $pdo->prepare("
        SELECT l.firma 
        FROM vt_artikel_lieferant vl
        JOIN t_lieferant l ON vl.fk_lieferant = l.id_lieferant
        WHERE vl.fk_artikel = ?
    ");
    $stmt_lieferant->execute([$artikel_neu['id_artikel']]);
    $lieferanten = $stmt_lieferant->fetchAll(PDO::FETCH_COLUMN);
    $artikel_neu['lieferanten'] = $lieferanten ? implode(', ', $lieferanten) : '-';
    
    echo json_encode([
        'success' => true,
        'message' => 'Bestand erfolgreich aktualisiert',
        'data' => [$artikel_neu],
        'count' => 1,
        'aenderung' => [
            'artikel' => $artikel['artikelbezeichnung'],
            'alter_bestand' => $alter_bestand,
            'neuer_bestand' => $neuer_bestand,
            'differenz' => $bestandsaenderung
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Datenbankfehler: ' . $e->getMessage()
    ]);
}
?>
