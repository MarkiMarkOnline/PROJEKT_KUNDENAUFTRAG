<?php
// ajax_artikel_suche.php
// Speichere diese Datei im selben Verzeichnis wie deine lagerverwaltung.php

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Prüfen ob ein Suchbegriff übergeben wurde
if (!isset($_POST['artikelname']) || empty(trim($_POST['artikelname']))) {
    echo json_encode(['success' => false, 'message' => 'Bitte geben Sie einen Suchbegriff ein.']);
    exit;
}

$suchbegriff = trim($_POST['artikelname']);

try {
    // SQL-Query mit LIKE für teilweise Übereinstimmungen
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
        WHERE a.artikelbezeichnung LIKE :suchbegriff
        ORDER BY a.artikelbezeichnung ASC
    ");
    
    $stmt->execute(['suchbegriff' => '%' . $suchbegriff . '%']);
    $ergebnisse = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Einheiten, MwSt, Warengruppen, Herkunft und Lieferanten nachträglich holen
    foreach ($ergebnisse as &$artikel) {
        // Einheit holen
        if ($artikel['fk_einheit']) {
            $stmt_einheit = $pdo->prepare("SELECT einheit FROM t_einheit WHERE id_einheit = ?");
            $stmt_einheit->execute([$artikel['fk_einheit']]);
            $einheit_row = $stmt_einheit->fetch(PDO::FETCH_ASSOC);
            $artikel['einheit'] = $einheit_row ? $einheit_row['einheit'] : null;
        } else {
            $artikel['einheit'] = null;
        }
        
        // MwSt holen
        if ($artikel['fk_mwst']) {
            $stmt_mwst = $pdo->prepare("SELECT mwst FROM t_mwst WHERE id_mwst = ?");
            $stmt_mwst->execute([$artikel['fk_mwst']]);
            $mwst_row = $stmt_mwst->fetch(PDO::FETCH_ASSOC);
            $artikel['mwst_satz'] = $mwst_row ? $mwst_row['mwst'] : null;
        } else {
            $artikel['mwst_satz'] = null;
        }
        
        // Warengruppen holen (kann mehrere geben)
        $stmt_warengruppe = $pdo->prepare("
            SELECT w.warengruppe 
            FROM vt_artikel_warengruppe vw
            JOIN t_warengruppe w ON vw.fk_warengruppe = w.id_warengruppe
            WHERE vw.fk_artikel = ?
        ");
        $stmt_warengruppe->execute([$artikel['id_artikel']]);
        $warengruppen = $stmt_warengruppe->fetchAll(PDO::FETCH_COLUMN);
        $artikel['warengruppen'] = $warengruppen ? implode(', ', $warengruppen) : '-';
        
        // Herkunft holen (kann mehrere geben)
        $stmt_herkunft = $pdo->prepare("
            SELECT h.herkunft 
            FROM vt_artikel_herkunft vh
            JOIN t_herkunft h ON vh.fk_herkunft = h.id_herkunft
            WHERE vh.fk_artikel = ?
        ");
        $stmt_herkunft->execute([$artikel['id_artikel']]);
        $herkuenfte = $stmt_herkunft->fetchAll(PDO::FETCH_COLUMN);
        $artikel['herkuenfte'] = $herkuenfte ? implode(', ', $herkuenfte) : '-';
        
        // Lieferanten holen (kann mehrere geben)
        $stmt_lieferant = $pdo->prepare("
            SELECT l.firma 
            FROM vt_artikel_lieferant vl
            JOIN t_lieferant l ON vl.fk_lieferant = l.id_lieferant
            WHERE vl.fk_artikel = ?
        ");
        $stmt_lieferant->execute([$artikel['id_artikel']]);
        $lieferanten = $stmt_lieferant->fetchAll(PDO::FETCH_COLUMN);
        $artikel['lieferanten'] = $lieferanten ? implode(', ', $lieferanten) : '-';
    }
    unset($artikel);
    
    if (count($ergebnisse) > 0) {
        echo json_encode([
            'success' => true,
            'data' => $ergebnisse,
            'count' => count($ergebnisse)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Keine Artikel gefunden für: ' . htmlspecialchars($suchbegriff)
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Datenbankfehler: ' . $e->getMessage()
    ]);
}
?>