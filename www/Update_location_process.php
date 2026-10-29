<?php
require 'database.php';
$id = $_POST['id'];
$type_id = $_POST['location_type_id'];
$number = $_POST['number'];
$name = $_POST['name'];
$size_m2 = $_POST['size_m2'];
$has_electricity = $_POST['has_electricity'];
$near_water = $_POST['near_water'];
$max_guests = $_POST['max_guests'];
$price_per_night = $_POST['price_per_night'];
$is_active = $_POST['is_active'];


$stmt = $conn->prepare(
    "UPDATE locations
     SET location_type_id = :location_type_id,
         number = :number,
         name = :name,
         size_m2 = :size_m2,
         has_electricity = :has_electricity,
         near_water = :near_water,
         max_guests = :max_guests,
         price_per_night = :price_per_night,
         is_active = :is_active
     WHERE id = :id"
);

$stmt->execute([
    'location_type_id' => $_POST['location_type_id'],
    'number' => $_POST['number'],
    'name' => $_POST['name'],
    'size_m2' => $_POST['size_m2'],
    'has_electricity' => $_POST['has_electricity'],
    'near_water' => $_POST['near_water'],
    'max_guests' => $_POST['max_guests'],
    'price_per_night' => $_POST['price_per_night'],
    'is_active' => $_POST['is_active'],
    'id' => $_POST['id']
]);

header('Location:stats_locations.php');
exit;
?>