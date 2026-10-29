<?php
require 'database.php';

// Wie al is ingelogd hoeft zich niet te registreren
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$name = '';
$email = '';
$phone = '';

if (isset($_POST['submit'])) {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($name === '') {
        $errors[] = 'Vul je naam in.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vul een geldig e-mailadres in.';
    }

    if ($phone !== '' && !preg_match('/^[0-9+\- ]{8,15}$/', $phone)) {
        $errors[] = 'Vul een geldig telefoonnummer in (alleen cijfers, + en -).';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Je wachtwoord moet minimaal 8 tekens lang zijn.';
    }

    if ($password !== $password2) {
        $errors[] = 'De wachtwoorden komen niet overeen.';
    }

    try {
        
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Dit e-mailadres is al in gebruik.';
            }
        }

        if (empty($errors)) {
            
            $stmt = $conn->prepare(
                "INSERT INTO users (role_id, name, email, password, phone)
                 VALUES (1, :name, :email, :password, :phone)"
            );
            $stmt->execute([
                'name'     => $name,
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'phone'    => $phone === '' ? null : $phone,
            ]);

         
            header('Location: index.php');
            exit;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        header('Location: 500.php');
        exit;
    }
}

require 'header.php';
?>
<link rel="stylesheet" href="style.css">;
<main>
    <div class="profile-card">
        <h2>Account aanmaken</h2>
        <p class="profile-intro">Maak een account aan om een plek te kunnen reserveren.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php" class="profile-form">
            <label>
                Naam
                <input type="text" name="name" value="<?php echo htmlspecialchars($name) ?>" required>
            </label>

            <label>
                E-mailadres
                <input type="email" name="email" value="<?php echo htmlspecialchars($email) ?>" required>
            </label>

            <label>
                Telefoonnummer (niet verplicht)
                <input type="text" name="phone" value="<?php echo htmlspecialchars($phone) ?>">
            </label>

            <label>
                Wachtwoord (minimaal 8 tekens)
                <input type="password" name="password" required>
            </label>

            <label>
                Herhaal wachtwoord
                <input type="password" name="password2" required>
            </label>

            <button type="submit" name="submit">Account aanmaken</button>
        </form>

        <p class="form-note">Heb je al een account? <a href="login.php">Inloggen</a></p>
    </div>
</main>

<?php require 'footer.php'; ?>