<?php
require_once __DIR__ . '/../config.php';

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("SELECT id_artikel, artikelbezeichnung, preis FROM t_artikel WHERE artikelbezeichnung LIKE ?");
$stmt->execute(["%$q%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results);
