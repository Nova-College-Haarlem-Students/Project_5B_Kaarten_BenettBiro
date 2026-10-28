<?php
session_start();
require 'database.php';
$error = '';

if (isset($_POST['submit'])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $emailForm = $_POST['email'];
        $passwordForm = $_POST['password'];


        $sql = "SELECT users.id, users.name, users.email, users.password, users.role_id
                FROM users
                WHERE users.email = :email";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $emailForm]);
        $dbuser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dbuser && password_verify($passwordForm, $dbuser['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $dbuser['id'];
            $_SESSION['email'] = $dbuser['email'];
            $_SESSION['name'] = $dbuser['name'];
            $_SESSION['role_id'] = $dbuser['role_id'];

            // Aantal reserveringen van deze gebruiker ophalen voor de header
            $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $dbuser['id']]);
            $_SESSION['reservation_count'] = (int) $stmt->fetchColumn();

            if ($dbuser['role_id'] == 2 || $dbuser['role_id'] == 3) {
                header('Location: dashboard.php');
            } else {
                header('Location: guest_dashboard.php');
            }
            exit;
        } else {
            $error = 'Onjuiste e-mail of wachtwoord.';
        }
    } else {
        $error = 'Vul beide velden in.';
    }
}
?>