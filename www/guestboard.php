<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int) $_SESSION['user_id'];
$errors = [];

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

try {

    $stmt = $conn->prepare("SELECT id, name, email, phone FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: logout.php');
        exit;
    }

    
    if (isset($_POST['submit'])) {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '') {
            $errors[] = 'Vul je naam in.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Vul een geldig e-mailadres in.';
        }

        if ($phone !== '' && !preg_match('/^[0-9+\- ]{8,15}$/', $phone)) {
            $errors[] = 'Vul een geldig telefoonnummer in (alleen cijfers, + en -).';
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $stmt->execute(['email' => $email, 'id' => $id]);
            if ($stmt->fetch()) {
                $errors[] = 'Dit e-mailadres is al in gebruik.';
            }
        }

        if (empty($errors)) {
            $stmt = $conn->prepare(
                "UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id"
            );
            $stmt->execute([
                'name'  => $name,
                'email' => $email,
                'phone' => $phone === '' ? null : $phone,
                'id'    => $id,
            ]);

            $_SESSION['name']    = $name;
            $_SESSION['email']   = $email;
            $_SESSION['success'] = 'Je gegevens zijn opgeslagen.';
            header('Location: guestboard.php');
            exit;
        }

        $user['name']  = $name;
        $user['email'] = $email;
        $user['phone'] = $phone;
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: 500.php');
    exit;
}

require 'header.php';
?>
  <link rel="stylesheet" href="guest.css">

<main>
    <div class="profile-card">
        <h2>Mijn gegevens</h2>
        <p class="profile-intro">Hier zie je je accountgegevens en kun je ze aanpassen.</p>

        <?php if ($success !== ''): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="guestboard.php" class="profile-form">
            <label>
                Naam
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']) ?>" required>
            </label>

            <label>
                E-mailadres
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']) ?>" required>
            </label>

            <label>
                Telefoonnummer
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? '') ?>">
            </label>

            <button type="submit" name="submit">Opslaan</button>
        </form>
    </div>
</main>

<?php require 'footer.php'; ?>