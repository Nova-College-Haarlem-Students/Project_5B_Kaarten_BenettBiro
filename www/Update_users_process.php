<?php
session_start();
require 'database.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Alleen POST-verzoeken toestaan
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

// Haal het ID op van de gebruiker die aangepast moet worden
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: admin.php');
    exit;
}

$errors = [];

try {
    // Controleer of de ingelogde gebruiker admin is
    $stmt = $conn->prepare(
        "SELECT role_id FROM users WHERE id = :id"
    );

    $stmt->execute([
        'id' => $_SESSION['user_id']
    ]);

    $loggedInUser = $stmt->fetch(PDO::FETCH_ASSOC);

    
    if (!$loggedInUser || $loggedInUser['role_id'] !== 3) {
        header('Location: guestboard.php');
        exit;
    }


    // Haal de gegevens uit het formulier
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Controleer de naam
    if ($name === '') {
        $errors[] = 'Vul een naam in.';
    }

    // Controleer het e-mailadres
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vul een geldig e-mailadres in.';
    }

    // Controleer het telefoonnummer
    if (
        $phone !== '' &&
        !preg_match('/^[0-9+\- ]{8,15}$/', $phone)
    ) {
        $errors[] = 'Vul een geldig telefoonnummer in.';
    }

    // Controleer of het e-mailadres al door iemand anders wordt gebruikt
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "SELECT id FROM users
             WHERE email = :email AND id != :id"
        );

        $stmt->execute([
            'email' => $email,
            'id' => $id
        ]);

        if ($stmt->fetch()) {
            $errors[] = 'Dit e-mailadres is al in gebruik.';
        }
    }

    // Sla de wijzigingen op
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE users
             SET name = :name,
                 email = :email,
                 phone = :phone
             WHERE id = :id"
        );

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone === '' ? null : $phone,
            'id' => $id
        ]);

        $_SESSION['success'] = 'De gebruiker is succesvol aangepast.';

        header('Location: index.php');
        exit;
    }

    // Bewaar de foutmelding
    $_SESSION['errors'] = $errors;

    header('Location: edit_user.php?id=' . $id);
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    $_SESSION['errors'] = [
        'Er is iets misgegaan bij het opslaan van de gebruiker.'
    ];

    header('Location: edit_user.php?id=' . $id);
    exit;
}
