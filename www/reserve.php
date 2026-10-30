<?php
// AJAX-endpoint: maakt een reservering aan en geeft JSON terug.
session_start();
require 'database.php';

header('Content-Type: application/json; charset=utf-8');

function respond($status, $success, $message, $extra = [])
{
    http_response_code($status);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, false, 'Ongeldig verzoek.');
}

// Alleen ingelogde gebruikers (dit controleren we hier op de server, niet alleen in de pagina)
if (!isset($_SESSION['user_id'])) {
    respond(401, false, 'Je moet ingelogd zijn om te reserveren.');
}

$userId       = (int) $_SESSION['user_id'];
$locationId   = (int) ($_POST['location_id'] ?? 0);
$arrival      = $_POST['arrival_date'] ?? '';
$departure    = $_POST['departure_date'] ?? '';
$guests       = (int) ($_POST['guests'] ?? 0);
$remark       = trim($_POST['remark'] ?? '');
$licensePlate = trim($_POST['license_plate'] ?? '');

// --- Datums controleren ---
$arrivalDate   = DateTime::createFromFormat('!Y-m-d', $arrival);
$departureDate = DateTime::createFromFormat('!Y-m-d', $departure);

if (!$arrivalDate || $arrivalDate->format('Y-m-d') !== $arrival
    || !$departureDate || $departureDate->format('Y-m-d') !== $departure) {
    respond(422, false, 'Vul een geldige aankomst- en vertrekdatum in.');
}

if ($arrivalDate < new DateTime('today')) {
    respond(422, false, 'De aankomstdatum mag niet in het verleden liggen.');
}

if ($departureDate <= $arrivalDate) {
    respond(422, false, 'De vertrekdatum moet na de aankomstdatum liggen.');
}


if (strlen($remark) > 255) {
    respond(422, false, 'Je opmerking is te lang (maximaal 255 tekens).');
}

if (strlen($licensePlate) > 20) {
    respond(422, false, 'Het kenteken is te lang.');
}

$nights = $arrivalDate->diff($departureDate)->days;


try {
    $conn->beginTransaction();

    // De plek vergrendelen, zodat twee gasten niet tegelijk dezelfde dagen kunnen boeken
    $stmt = $conn->prepare(
        "SELECT id, max_guests, price_per_night
         FROM locations
         WHERE id = :id AND is_active = 1
         FOR UPDATE"
    );
    $stmt->execute(['id' => $locationId]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$location) {
        $conn->rollBack();
        respond(404, false, 'Deze plek bestaat niet of is niet beschikbaar.');
    }

    if ($guests < 1 || $guests > $location['max_guests']) {
        $conn->rollBack();
        respond(422, false, 'Het aantal personen moet tussen 1 en ' . $location['max_guests'] . ' liggen.');
    }

    // Overlapt de periode met een bestaande reservering? (vertrekdag = aankomstdag van de volgende mag)
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM reservations
         WHERE location_id = :id
           AND arrival_date < :departure
           AND departure_date > :arrival"
    );
    $stmt->execute([
        'id'        => $locationId,
        'departure' => $departure,
        'arrival'   => $arrival,
    ]);

    if ($stmt->fetchColumn() > 0) {
        $conn->rollBack();
        respond(409, false, 'Deze plek is in die periode al gereserveerd.');
    }

    // De totaalprijs bewaren we in de reservering, zodat latere prijsverhogingen niets veranderen
    $totalPrice = round($nights * $location['price_per_night'], 2);

    $stmt = $conn->prepare(
        "INSERT INTO reservations
            (user_id, location_id, arrival_date, departure_date, guests, remark, license_plate, total_price)
         VALUES
            (:user_id, :location_id, :arrival, :departure, :guests, :remark, :license_plate, :total_price)"
    );
    $stmt->execute([
        'user_id'       => $userId,
        'location_id'   => $locationId,
        'arrival'       => $arrival,
        'departure'     => $departure,
        'guests'        => $guests,
        'remark'        => $remark === '' ? null : $remark,
        'license_plate' => $licensePlate === '' ? null : $licensePlate,
        'total_price'   => $totalPrice,
    ]);

    // Nieuw aantal reserveringen voor deze plek
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE location_id = :id");
    $stmt->execute(['id' => $locationId]);
    $count = (int) $stmt->fetchColumn();

    $conn->commit();

    respond(200, true,
        'Gelukt! Je hebt ' . $nights . ' nacht(en) gereserveerd voor € ' . number_format($totalPrice, 2, ',', '.') . '.',
        ['count' => $count]
    );
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log($e->getMessage());
    respond(500, false, 'Er ging iets mis op de server. Probeer het later opnieuw.');
}