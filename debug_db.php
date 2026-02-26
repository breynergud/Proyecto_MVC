<?php
require 'Conexion.php';
$conn = Conexion::getConnect();

echo "PROGRAMAS:\n";
$stmt = $conn->query('SELECT * FROM programa LIMIT 5');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\nCOMPETENCIAS POR PROGRAMA:\n";
$stmt2 = $conn->query('SELECT * FROM competxprograma LIMIT 15');
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
