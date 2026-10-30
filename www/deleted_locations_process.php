<?php
session_start();
require 'database.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!in_array($_SESSION['role_id'] ?? null, [2, 3])) {
    header('Location: 403.php');
    exit;
}


if (!isset($_POST['delete'])) {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$user_id = (int) $_SESSION['user_id'];

try {
    $conn->beginTransaction();

   
    $stmt = $conn->prepare("SELECT id, number FROM locations WHERE id = :id AND deleted_at IS NULL");
    $stmt->execute(['id' => $id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$location) {
        $conn->rollBack();
        header('Location: 404.php');
        exit;
    }


    $stmt = $conn->prepare(
        "UPDATE locations
     SET deleted_at = NOW(),
         is_active = 0
     WHERE id = :id"
    );

    $stmt->execute(['id' => $id]);

    // Logboek
    $stmt = $conn->prepare(
        "INSERT INTO changes (user_id, location_id, changed_at, change_type, old_value, new_value)
         VALUES (:user_id, :location_id, NOW(), 'verwijderd', 'zichtbaar', 'verwijderd')"
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

$_SESSION['success'] = 'Plek ' . $location['number'] . ' is verwijderd.';
header('Location: locations.php');
exit;