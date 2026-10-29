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

// Meldingen na het herstellen (uit de sessie, zodat verversen niets opnieuw doet)
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

try {
    // Alleen de verwijderde plekken (deleted_at is gevuld), de laatst verwijderde bovenaan
    $stmt = $conn->prepare(
        "SELECT locations.id, locations.number, locations.name, locations.price_per_night, locations.deleted_at,
                location_types.name AS type_name
         FROM locations
         LEFT JOIN location_types ON location_types.id = locations.location_type_id
         WHERE locations.deleted_at IS NOT NULL
         ORDER BY locations.deleted_at DESC"
    );
    $stmt->execute();
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: 500.php');
    exit;
}

require 'header.php';
?>

<main>
    <div class="table-card">
        <h2>Verwijderde plekken</h2>

        <?php if ($success !== ''): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (count($locations) === 0): ?>
            <p class="table-empty">Er zijn geen verwijderde plekken.</p>
        <?php else: ?>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Plek</th>
                            <th>Soort</th>
                            <th>Prijs per nacht</th>
                            <th>Verwijderd op</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($locations as $location): ?>
                            <tr>
                                <td>
                                    Plek <?php echo htmlspecialchars($location['number']) ?>
                                    <span class="table-sub"><?php echo htmlspecialchars($location['name'] ?? '') ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($location['type_name'] ?? '-') ?></td>
                                <td>€ <?php echo number_format($location['price_per_night'], 2, ',', '.') ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($location['deleted_at'])) ?></td>
                                <td>
                                    <form method="post" action="restore_locations.php">
                                        <input type="hidden" name="id" value="<?php echo (int) $location['id'] ?>">
                                        <button type="submit" name="restore">Herstellen</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require 'footer.php'; ?>