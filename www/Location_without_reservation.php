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

try {
    // LEFT JOIN: alle plekken, ook die zonder reservering.
    // Waar reservations.id leeg (NULL) is, is er geen reservering gevonden.
    $stmt = $conn->prepare(
        "SELECT locations.id, locations.number, locations.name, locations.size_m2,
                locations.max_guests, locations.price_per_night,
                location_types.name AS type_name
         FROM locations
         LEFT JOIN reservations ON reservations.location_id = locations.id
         LEFT JOIN location_types ON location_types.id = locations.location_type_id
         WHERE reservations.id IS NULL
           AND locations.deleted_at IS NULL
         ORDER BY locations.number"
    );
    $stmt->execute();
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: 500.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plekken zonder reserveringen</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>

    <nav>
        <div class="nav-inner">
            <div class="nav-logo">Camping</div>
            <div class="nav-right">
                <a href="index.php" class="btn-blue">Home</a>
                <a href="logout.php?logout=1" class="btn-red">Uitloggen</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-inner">
            <h1>Plekken zonder reserveringen</h1>
            <p>Plekken die nog nooit zijn gereserveerd.</p>
        </div>
    </section>

    <main class="main-content">
        <div class="detail-card">
            <div class="detail-content">
                <h2 class="detail-title">Overzicht</h2>
                <p class="detail-type"><?php echo count($locations) ?> plek(ken) zonder reservering.</p>

                <div class="table-card">
                    <?php if (count($locations) === 0): ?>
                        <p class="table-empty">Alle plekken hebben minstens één reservering.</p>
                    <?php else: ?>
                        <div class="table-scroll">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Plek</th>
                                        <th>Naam</th>
                                        <th>Soort</th>
                                        <th>m²</th>
                                        <th>Max gasten</th>
                                        <th>Prijs per nacht</th>
                                        <th>Acties</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locations as $location): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($location['number']) ?></td>
                                            <td><?php echo htmlspecialchars($location['name'] ?? '-') ?></td>
                                            <td><?php echo htmlspecialchars($location['type_name'] ?? '-') ?></td>
                                            <td><?php echo htmlspecialchars($location['size_m2']) ?></td>
                                            <td><?php echo (int) $location['max_guests'] ?></td>
                                            <td>€ <?php echo number_format($location['price_per_night'], 2, ',', '.') ?></td>
                                            <td>
                                                <a href="update_location.php?id=<?php echo (int) $location['id'] ?>">Bewerken</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-inner">
            <div>
                <h4>Over Ons</h4>
                <p>Wij zijn een camping die zich richt op een fijne vakantie.</p>
            </div>
            <div>
                <h4>Snelle Links</h4>
                <ul class="footer-links">
                    <li><a href="locations.php">Alle plekken</a></li>
                </ul>
            </div>
            <div></div>
        </div>
    </footer>

</body>

</html>