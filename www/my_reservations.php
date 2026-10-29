<?php
session_start();
require 'database.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

try {
    $stmt = $conn->prepare(
        "SELECT reservations.id,
                reservations.arrival_date,
                reservations.departure_date,
                reservations.guests,
                reservations.remark,
                reservations.license_plate,
                reservations.total_price,
                locations.id AS location_id,
                locations.number,
                location_types.name AS type_name
         FROM reservations
         JOIN locations ON locations.id = reservations.location_id
         JOIN location_types ON location_types.id = locations.location_type_id
         WHERE reservations.user_id = :user_id
         ORDER BY reservations.arrival_date DESC"
    );
    $stmt->execute(['user_id' => $user_id]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: 500.php');
    exit;
}


$_SESSION['reservation_count'] = count($reservations);

require 'header.php';
?>

<main>
    <div class="table-card">
        <h2>Mijn reserveringen</h2>

        <?php if (count($reservations) === 0): ?>
            <p class="table-empty">
                Je hebt nog geen reserveringen.
                <a href="locations.php">Bekijk de plekken</a>
            </p>
        <?php else: ?>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Plek</th>
                            <th>Aankomst</th>
                            <th>Vertrek</th>
                            <th>Nachten</th>
                            <th>Personen</th>
                            <th>Kenteken</th>
                            <th>Opmerking</th>
                            <th>Totaalprijs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <?php
                            $nights = (new DateTime($reservation['arrival_date']))
                                ->diff(new DateTime($reservation['departure_date']))->days;
                            ?>
                            <tr>
                                <td>
                                    <a href="Location_detail.php?id=<?php echo urlencode($reservation['location_id']) ?>">
                                        Plek <?php echo htmlspecialchars($reservation['number']) ?>
                                    </a>
                                    <span class="table-sub"><?php echo htmlspecialchars($reservation['type_name']) ?></span>
                                </td>
                                <td><?php echo date('d-m-Y', strtotime($reservation['arrival_date'])) ?></td>
                                <td><?php echo date('d-m-Y', strtotime($reservation['departure_date'])) ?></td>
                                <td><?php echo $nights ?></td>
                                <td><?php echo htmlspecialchars($reservation['guests']) ?></td>
                                <td><?php echo htmlspecialchars($reservation['license_plate'] ?? '-') ?></td>
                                <td><?php echo htmlspecialchars($reservation['remark'] ?? '-') ?></td>
                                <td>€ <?php echo number_format($reservation['total_price'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require 'footer.php'; ?>