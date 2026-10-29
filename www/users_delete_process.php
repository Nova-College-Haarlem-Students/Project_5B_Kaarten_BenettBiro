<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "Huh? Wat doe je?";
    exit;
}

if (isset($_POST['id'])) {

    require 'database.php';

    $id = $_POST["id"];

    $conn->beginTransaction();

    try {
    
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollBack();
        die("Er ging iets fout: " . htmlspecialchars($e->getMessage()));
    }

    header("Location:index.php");
    exit;
}