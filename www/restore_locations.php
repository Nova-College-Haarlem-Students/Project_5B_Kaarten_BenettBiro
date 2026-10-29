<?php
require 'database.php';

// ID ophalen
$id = $_POST['id'];

// Locatie herstellen
$stmt = $conn->prepare(
    "UPDATE locations
     SET deleted_at = NULL,
         is_active = 1
     WHERE id = :id"
);

$stmt->execute(['id' => $id]);

header('Location: index.php');
exit();
?>