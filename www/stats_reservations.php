<?php
require 'data_dashboard.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: inloggen.php');
    exit;
}

if (isset($_SESSION['rol_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: dashboard.php?id=' . $_SESSION['user_id']);
    exit;
}

require 'database.php';

$id = $_SESSION['user_id'];
$sql = "SELECT * FROM reservations";
$stmt = $conn->prepare($sql);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>

    <nav>
        <div class="nav-inner">
            <div class="nav-logo">Camping</div>
            <div class="nav-right">

                <span class="timer-label">
                    Tijd op pagina: <span id="timer" class="timer">00:00</span>
                </span>
                <a href="index.php" class="btn-blue">Home</a>
                <a href="logout.php?logout=1" class="btn-red">Uitloggen</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-inner">
            <h1>Mijn Dashboard</h1>
            <p></p>
        </div>
    </section>

    <main class="main-content">
        <div class="detail-card">
            <div class="detail-content">
                <h2 class="detail-title">Reservations info</h2>
                <p class="detail-type">Reservations are showed under here</p>

                <div class="table-card">
                    <h2>Reservations</h2>

                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>user id</th>
                                    <th>location id</th>
                                    <th> arrival date</th>
                                    <th> departure date</th>
                                    <th> total guests</th>
                                    <th> remark</th>
                                    <th> license plate</th>
                                    <th>total price</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reservation['id']) ?></td>
                                        <td><?php echo htmlspecialchars($reservation['user_id']) ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($reservation['location_id']) ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($reservation['arrival_date']) ?></td>
                                        <td><?php echo htmlspecialchars($reservation['departure_date'] ?? '-') ?></td>
                                        <td><?php echo htmlspecialchars($reservation['guests']) ?></td>
                                        <td><?php echo htmlspecialchars($reservation['remark'] ?? '-') ?></td>
                                        <td><?php echo htmlspecialchars($reservation['license_plate'] ?? '-') ?></td>
                                        <td><?php echo htmlspecialchars($reservation['total_price']) ?></td>
                                        <td>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>


    </main>

    <footer>
        <div class="footer-inner">
            <div>
                <h4>Over Ons</h4>
                <p></p>
            </div>
            <div>
                <h4>Snelle Links</h4>
                <ul class="footer-links">
                   
                </ul>
            </div>
            <div></div>
        </div>
    </footer>

    <script>
        let seconden = 0;
        function updateTimer() {
            seconden++;
            const minuten = Math.floor(seconden / 60);
            const restSeconden = seconden % 60;
            document.getElementById('timer').textContent =
                String(minuten).padStart(2, '0') + ':' + String(restSeconden).padStart(2, '0');
        }
        setInterval(updateTimer, 1000);
    </script>

</body>

</html>