<?php
require_once 'model/FichaModel.php';

try {
    $model = new FichaModel();
    $data = $model->readAll();
    echo "Fichas obtenidas: " . count($data) . "\n";
    if (count($data) > 0) {
        print_r($data[0]);
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
