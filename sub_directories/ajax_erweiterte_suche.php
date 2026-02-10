<?php
// ajax_erweiterte_suche.php
// Speichere diese Datei im selben Verzeichnis wie lagerverwaltung.php

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

try {
    // Basis-Query
    $sql = "SELECT DISTINCT a.id_artikel, a.artikelbezeichnung, a.preis, a.lagerbestand, 
            a.saisonware, a.saisonstart, a.saisonende, a.fk_einheit, a.fk_mwst 
            FROM t_artikel a";
    
    // Arrays für JOINs und WHERE-Bedingungen
    $joins = [];
    $where = [];
    $params = [];
    
    // JOIN für Kategorie (Warengruppe)
    if (isset($_POST['kategorie']) && $_POST['kategorie'] !== 'alle') {
        $joins[] = "INNER JOIN vt_artikel_warengruppe vw ON a.id_artikel = vw.fk_artikel";
        $joins[] = "INNER JOIN t_warengruppe w ON vw.fk_warengruppe = w.id_warengruppe";
        $where[] = "w.warengruppe = :kategorie";
        $params[':kategorie'] = $_POST['kategorie'];
    }
    
    // JOIN für Gruppe (Herkunft)
    if (isset($_POST['gruppe']) && $_POST['gruppe'] !== 'alle') {
        $joins[] = "INNER JOIN vt_artikel_herkunft vh ON a.id_artikel = vh.fk_artikel";
        $joins[] = "INNER JOIN t_herkunft h ON vh.fk_herkunft = h.id_herkunft";
        $where[] = "h.herkunft = :gruppe";
        $params[':gruppe'] = $_POST['gruppe'];
    }
    
    // WHERE für Vergleich (Bestand, Preis, etc.)
    if (isset($_POST['art']) && $_POST['art'] !== '' && $_POST['art'] !== '-' && 
        isset($_POST['vergleich']) && isset($_POST['anzahl']) && $_POST['anzahl'] !== '') {
        
        $art = $_POST['art']; // bestand, preis, umsatz
        $vergleich = $_POST['vergleich']; // gleich, groesser, kleiner
        $anzahl = floatval($_POST['anzahl']);
        
        // Mapping der Vergleichsoperatoren
        $operator = '=';
        switch($vergleich) {
            case 'groesser':
                $operator = '>';
                break;
            case 'kleiner':
                $operator = '<';
                break;
            case 'gleich':
            default:
                $operator = '=';
                break;
        }
        
        // Mapping der Spalten
        $spalte = '';
        switch($art) {
            case 'bestand':
                $spalte = 'a.lagerbestand';
                break;
            case 'preis':
                $spalte = 'a.preis';
                break;
            case 'umsatz':
                // Umsatz müsste aus Bestellungen berechnet werden - erstmal Platzhalter
                // TODO: JOIN mit t_bestelldetails für Umsatzberechnung
                $spalte = 'a.preis'; // Provisorisch
                break;
        }
        
        if ($spalte !== '') {
            $where[] = "$spalte $operator :anzahl";
            $params[':anzahl'] = $anzahl;
        }
    }
    
    // JOINs zur Query hinzufügen (doppelte entfernen)
    if (!empty($joins)) {
        $joins = array_unique($joins);
        $sql .= " " . implode(" ", $joins);
    }
    
    // WHERE-Klauseln zur Query hinzufügen
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    // Sortierung
    $sql .= " ORDER BY a.artikelbezeichnung ASC";
    
    // Query ausführen
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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
        
        // Warengruppen holen
        $stmt_warengruppe = $pdo->prepare("
            SELECT w.warengruppe 
            FROM vt_artikel_warengruppe vw
            JOIN t_warengruppe w ON vw.fk_warengruppe = w.id_warengruppe
            WHERE vw.fk_artikel = ?
        ");
        $stmt_warengruppe->execute([$artikel['id_artikel']]);
        $warengruppen = $stmt_warengruppe->fetchAll(PDO::FETCH_COLUMN);
        $artikel['warengruppen'] = $warengruppen ? implode(', ', $warengruppen) : '-';
        
        // Herkunft holen
        $stmt_herkunft = $pdo->prepare("
            SELECT h.herkunft 
            FROM vt_artikel_herkunft vh
            JOIN t_herkunft h ON vh.fk_herkunft = h.id_herkunft
            WHERE vh.fk_artikel = ?
        ");
        $stmt_herkunft->execute([$artikel['id_artikel']]);
        $herkuenfte = $stmt_herkunft->fetchAll(PDO::FETCH_COLUMN);
        $artikel['herkuenfte'] = $herkuenfte ? implode(', ', $herkuenfte) : '-';
        
        // Lieferanten holen
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
            'count' => count($ergebnisse),
            'query' => $sql  // Für Debugging
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Keine Artikel mit den gewählten Filtern gefunden.'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Datenbankfehler: ' . $e->getMessage(),
        'sql' => isset($sql) ? $sql : 'Query nicht erstellt',
        'params' => isset($params) ? $params : []
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Allgemeiner Fehler: ' . $e->getMessage()
    ]);
}
?>