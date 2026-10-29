<?php
session_start();
require 'database.php';

// Alleen ingelogde medewerkers (2) en admins (3)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!in_array($_SESSION['role_id'] ?? null, [2, 3])) {
    header('Location: 403.php');
    exit;
}

// De ingelogde gebruiker
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$total_locations = $conn->query("SELECT COUNT(*) AS total FROM locations")->fetch(PDO::FETCH_ASSOC)['total'];
$total_members   = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_id = 1")->fetch(PDO::FETCH_ASSOC)['total'];
$total_employees = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_id = 2")->fetch(PDO::FETCH_ASSOC)['total'];

$total_profit    = $conn->query("SELECT SUM(total_price) AS total FROM reservations")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$fetch_avg_price = $conn->query("SELECT ROUND(AVG(total_price), 2) AS total FROM reservations")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$fetch_max_price = $conn->query("SELECT ROUND(MAX(total_price), 2) AS total FROM reservations")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$fetch_min_price = $conn->query("SELECT ROUND(MIN(total_price), 2) AS total FROM reservations")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Alle gebruikers met hun rol (fetchAll, niet fetch)
$fetch_users = $conn->query(
    "SELECT users.id, users.name, users.email, users.phone, roles.name AS role_name
     FROM users
     JOIN roles ON roles.id = users.role_id
     ORDER BY users.id"
)->fetchAll(PDO::FETCH_ASSOC);

// Alle gebruikers met hun rol (fetchAll, niet fetch)
$fetch_locations = $conn->query(
    "SELECT * FROM locations "
)->fetchAll(PDO::FETCH_ASSOC);