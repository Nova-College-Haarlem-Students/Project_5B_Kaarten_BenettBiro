<?php
session_start();
require 'database.php';

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

try {
    // Controleer of de ingelogde gebruiker admin is
    $stmt = $conn->prepare(
        "SELECT roles.name
         FROM users
         JOIN roles ON users.role_id = roles.id
         WHERE users.id = :id"
    );

    $stmt->execute([
        'id' => $_SESSION['user_id']
    ]);

    $role = $stmt->fetchColumn();

    // Niet-admins mogen alleen hun eigen gegevens bekijken
    if ($role !== 'admin') {
        header('Location: guestboard.php');
        exit;
    }

    // Haal het ID op van de gebruiker die bewerkt wordt
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        header('Location: admin.php');
        exit;
    }

    // Haal de gegevens van de geselecteerde gebruiker op
    $stmt = $conn->prepare(
        "SELECT id, name, email, phone
         FROM users
         WHERE id = :id"
    );

    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['errors'] = ['Deze gebruiker bestaat niet.'];
        header('Location: admin.php');
        exit;
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
        <h2>Gebruiker aanpassen</h2>

        <p class="profile-intro">
            Pas de gegevens van deze gebruiker aan.
        </p>

        <?php if ($success !== ''): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form
            method="post"
            action="Update_users_process.php?id=<?php echo (int) $user['id']; ?>"
            class="profile-form"
        >
            <label>
                Naam
                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($user['name']); ?>"
                    required
                >
            </label>

            <label>
                E-mailadres
                <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    required
                >
            </label>

            <label>
                Telefoonnummer
                <input
                    type="text"
                    name="phone"
                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                >
            </label>

            <button type="submit">Opslaan</button>
        </form>
    </div>
</main>

<?php require 'footer.php'; ?>
