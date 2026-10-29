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

// Alleen na het indrukken van de knop (een gewone link herstelt nooit iets)
if (!isset($_POST['restore'])) {
    header('Location: Deleted_locations.php');
    exit;
}

$id      = (int) ($_POST['id'] ?? 0);
$user_id = (int) $_SESSION['user_id'];

try {
    $conn->beginTransaction();

    // De plek moet bestaan en verwijderd zijn
    $stmt = $conn->prepare("SELECT id, number FROM locations WHERE id = :id AND deleted_at IS NOT NULL");
    $stmt->execute(['id' => $id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$location) {
        $conn->rollBack();
        header('Location: 404.php');
        exit;
    }

    // Is het nummer ondertussen aan een andere plek gegeven?
    $stmt = $conn->prepare("SELECT id FROM locations WHERE number = :number AND deleted_at IS NULL");
    $stmt->execute(['number' => $location['number']]);

    if ($stmt->fetch()) {
        $conn->rollBack();
        $_SESSION['error'] = 'Plek ' . $location['number'] . ' kan niet hersteld worden: dit nummer is nu van een andere plek.';
        header('Location: Deleted_locations.php');
        exit;
    }

    // Herstellen: deleted_at weer leeg
    $stmt = $conn->prepare("UPDATE locations SET deleted_at = NULL WHERE id = :id");
    $stmt->execute(['id' => $id]);

    // Logboek
    $stmt = $conn->prepare(
        "INSERT INTO changes (user_id, location_id, changed_at, change_type, old_value, new_value)
         VALUES (:user_id, :location_id, NOW(), 'hersteld', 'verwijderd', 'zichtbaar')"
    );
    $stmt->execute(['user_id' => $user_id, 'location_id' => $id]);

    $conn->commit();
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log($e->getMessage());
    header('Location: 500.php');
    exit;
}

$_SESSION['success'] = 'Plek ' . $location['number'] . ' is hersteld.';
header('Location: Deleted_locations.php');
exit;