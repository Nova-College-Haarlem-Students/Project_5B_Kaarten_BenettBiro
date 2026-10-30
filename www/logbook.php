<?php
session_start();
require 'database.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!in_array($_SESSION['role_id'] ?? null, [3])) {
    header('Location: 403.php');
    exit;
}

try {
  
    $stmt = $conn->prepare(
        "SELECT changes.id, changes.changed_at, changes.change_type,
                changes.old_value, changes.new_value,
                users.name AS user_name,
                locations.number AS location_number
         FROM changes
         LEFT JOIN users ON users.id = changes.user_id
         LEFT JOIN locations ON locations.id = changes.location_id
         ORDER BY changes.changed_at DESC, changes.id DESC"
    );
    $stmt->execute();
    $changes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Logboek</title>
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
            <h1>Logboek</h1>
            <p>Alle wijzigingen aan plekken, nieuwste bovenaan.</p>
        </div>
    </section>

    <main class="main-content">
        <div class="detail-card">
            <div class="detail-content">
                <h2 class="detail-title">Wijzigingen</h2>
                <p class="detail-type"><?php echo count($changes) ?> regel(s) in het logboek.</p>

                <div class="table-card">
                    <?php if (count($changes) === 0): ?>
                        <p class="table-empty">Er zijn nog geen wijzigingen gelogd.</p>
                    <?php else: ?>
                        <div class="table-scroll">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Door</th>
                                        <th>Plek</th>
                                        <th>Wijziging</th>
                                        <th>Oud</th>
                                        <th>Nieuw</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($changes as $change): ?>
                                        <tr>
                                            <td><?php echo date('d-m-Y H:i', strtotime($change['changed_at'])) ?></td>
                                            <td><?php echo htmlspecialchars($change['user_name'] ?? 'onbekend') ?></td>
                                            <td><?php echo $change['location_number'] !== null ? 'Plek ' . htmlspecialchars($change['location_number']) : '-' ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($change['change_type']) ?></td>
                                            <td><?php echo htmlspecialchars($change['old_value'] ?? '-') ?></td>
                                            <td><?php echo htmlspecialchars($change['new_value'] ?? '-') ?></td>
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