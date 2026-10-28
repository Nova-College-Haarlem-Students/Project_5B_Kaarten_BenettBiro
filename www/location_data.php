<?php
include 'database.php';


$types = $_GET['type'] ?? '';
$sizes = $_GET['size'] ?? '';
$electricity = $_GET['electricity'] ?? '';
$water = $_GET['water'] ?? '';
$zoekterm = $_GET['zoekterm'] ?? '';

$sql = "SELECT * FROM locations WHERE is_active = 1";
$params = [];

if (!empty($types)) {
    $sql .= " AND location_type_id = :type";
    $params['type'] = $types;
}

if (!empty($sizes)) {
    $sql .= " AND size_m2 = :size";
    $params['size'] = $sizes;
}

if ($electricity !== '') {
    $sql .= " AND has_electricity = :electricity";
    $params['electricity'] = $electricity;
}

if ($water !== '') {
    $sql .= " AND near_water = :water";
    $params['water'] = $water;
}

if (!empty($zoekterm)) {
    $sql .= " AND (name LIKE :zoekterm1 OR number LIKE :zoekterm2)";
    $params['zoekterm1'] = "%$zoekterm%";
    $params['zoekterm2'] = "%$zoekterm%";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$location = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>