<?php
session_start();
require 'database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: 404.php');
    exit;
}

$id = (int) $_GET['id'];

try {
    $stmt = $conn->prepare(
        "SELECT locations.*, location_types.name AS type_name
         FROM locations
         JOIN location_types ON location_types.id = locations.location_type_id
         WHERE locations.id = :id"
    );
    $stmt->execute(['id' => $id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);

    // Aantal reserveringen voor deze plek
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE location_id = :id");
    $stmt->execute(['id' => $id]);
    $reservationCount = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: 500.php');
    exit;
}

if (!$location) {
    header('Location: 404.php');
    exit;
}

require 'header.php';
?>

<main>
    <div class="container">

        <div class="product-detail">
            <div class="row">
                <div class="col">
                    <img src="<?php echo !empty($location['photo_url'])
                        ? htmlspecialchars($location['photo_url'])
                        : 'https://placehold.co/600x400' ?>"
                        alt="Plek <?php echo htmlspecialchars($location['number']) ?>">
                </div>
                <div class="col">
                    <h3>Location number: <?php echo htmlspecialchars($location['number']) ?></h3>
                    <p>Type: <?php echo htmlspecialchars($location['type_name']) ?></p>
                    <p>Max guests: <?php echo htmlspecialchars($location['max_guests']) ?></p>
                    <p>Size: <?php echo htmlspecialchars($location['size_m2']) ?> m²</p>
                    <p>Electricity: <?php echo $location['has_electricity'] ? 'yes' : 'no' ?></p>
                    <p>Near water: <?php echo $location['near_water'] ? 'yes' : 'no' ?></p>
                    <p>Reserveringen: <span id="reservation-count"><?php echo $reservationCount ?></span></p>
                    <p>€ <?php echo number_format($location['price_per_night'], 2, ',', '.') ?> per night</p>
                </div>
            </div>

            <!-- Reserveren -->
            <div class="reserve-box">
                <h4>Reserveren</h4>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <div id="reserve-message"></div>

                    <form id="reserve-form" class="reserve-form">
                        <input type="hidden" name="location_id" value="<?php echo (int) $location['id'] ?>">

                        <label>
                            Aankomstdatum
                            <input type="date" name="arrival_date" min="<?php echo date('Y-m-d') ?>" required>
                        </label>

                        <label>
                            Vertrekdatum
                            <input type="date" name="departure_date" min="<?php echo date('Y-m-d') ?>" required>
                        </label>

                        <label>
                            Aantal personen
                            <input type="number" name="guests" min="1" max="<?php echo (int) $location['max_guests'] ?>" value="1" required>
                        </label>

                        <label>
                            Kenteken (niet verplicht, nodig om te parkeren)
                            <input type="text" name="license_plate" maxlength="20">
                        </label>

                        <label class="reserve-wide">
                            Opmerking (niet verplicht)
                            <input type="text" name="remark" maxlength="255" placeholder="Bijvoorbeeld: we komen pas laat aan">
                        </label>

                        <button type="submit" class="reserve-wide">Reserveren</button>
                    </form>

                <?php else: ?>

                    <div class="reserve-login">
                        Je moet ingelogd zijn om te reserveren.
                        <a href="login.php">Inloggen</a> of <a href="register.php">maak een account aan</a>.
                    </div>

                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<script>
    const form = document.getElementById('reserve-form');

    // Het formulier bestaat alleen als je bent ingelogd
    if (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();   // pagina niet opnieuw laden

            const message = document.getElementById('reserve-message');
            const button = form.querySelector('button');
            button.disabled = true;

            try {
                // Alle velden van het formulier als object, verstuurd als JSON
                const input = Object.fromEntries(new FormData(form));

                const response = await fetch('Add_reservation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(input)
                });
                const data = await response.json();

                message.textContent = data.message;
                message.className = 'alert ' + (data.success ? 'alert-success' : 'alert-error');

                if (data.success) {
                    // Aantal reserveringen op de pagina bijwerken
                    document.getElementById('reservation-count').textContent = data.count;
                    form.reset();
                }
            } catch (error) {
                message.textContent = 'Er ging iets mis. Probeer het opnieuw.';
                message.className = 'alert alert-error';
            }

            button.disabled = false;
        });
    }
</script>

<?php require 'footer.php'; ?>