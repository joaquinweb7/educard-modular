<?php

$oldDb = new PDO('mysql:host=127.0.0.1;dbname=itecnoba_certificados', 'root', 'root');
$newDb = new PDO('mysql:host=127.0.0.1;dbname=educard', 'root', 'root');

$tables = [
    'certificados_plantillas',
    'certificados_cursos',
    'certificados',
    'smtp'
];

foreach ($tables as $table) {
    echo "Copying $table...\n";
    // Delete existing data in new db to avoid duplicates if run multiple times
    $newDb->exec("DELETE FROM $table");
    
    $stmt = $oldDb->query("SELECT * FROM $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($rows) > 0) {
        $columns = array_keys($rows[0]);
        $colString = implode(', ', $columns);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        
        $insertStmt = $newDb->prepare("INSERT INTO $table ($colString) VALUES ($placeholders)");
        
        foreach ($rows as $row) {
            $insertStmt->execute(array_values($row));
        }
    }
    echo "Copied " . count($rows) . " rows for $table.\n";
}

echo "Done.\n";
