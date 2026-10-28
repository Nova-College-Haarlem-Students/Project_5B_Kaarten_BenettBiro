<?php

session_start();
require 'database.php';

$id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$id'";

$sql_locations = "SELECT COUNT(*) as total_locations FROM locations";
$stmt_locations = $conn->query($sql_locations);
$total_locations = $stmt_locations->fetch(PDO::FETCH_ASSOC)['total_locations'];


$sql_members = "SELECT COUNT(*) as total_members FROM users WHERE role_id = 1";
$sql_employees = "SELECT COUNT(*) as total_employees FROM users WHERE role_id = 2";

$stmt_members = $conn->query($sql_members);
$total_members = $stmt_members->fetch(PDO::FETCH_ASSOC)['total_members'];

$stmt_employees = $conn->query($sql_employees);
$total_employees = $stmt_employees->fetch(PDO::FETCH_ASSOC)['total_employees'];


$stmt = $conn->query($sql);
$user = $stmt->fetch(PDO::FETCH_ASSOC);