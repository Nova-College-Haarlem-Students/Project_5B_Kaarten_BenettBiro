<?php
session_start();
require 'database.php';

header('Content-Type: application/json');   


$input = file_get_contents('php://input');  
$data = json_decode($input, true);          

function stop($success, $message, $extra = [])
{
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}


if (!isset($_SESSION['user_id'])) {
    stop(false, 'Je moet ingelogd zijn om te reserveren.');
}

if (!is_array($data) || !isset($data['location_id'])) {
    stop(false, 'Ongeldig verzoek.');
}

$user_id        = (int) $_SESSION['user_id'];   
$location_id    = (int) $data['location_id'];
$arrival_date   = $data['arrival_date'] ?? '';
$departure_date = $data['departure_date'] ?? '';
$guests         = (int) ($data['guests'] ?? 0);
$remark         = trim($data['remark'] ?? '');
$license_plate  = trim($data['license_plate'] ?? '');


$arrival   = DateTime::createFromFormat('!Y-m-d', $arrival_date);
$departure = DateTime::createFromFormat('!Y-m-d', $departure_date);

if (!$arrival || $arrival->format('Y-m-d') !== $arrival_date
    || !$departure || $departure->format('Y-m-d') !== $departure_date) {
    stop(false, 'Vul een geldige aankomst- en vertrekdatum in.');
}

if ($arrival < new DateTime('today')) {
    stop(false, 'De aankomstdatum mag niet in het verleden liggen.');
}

if ($departure <= $arrival) {
    stop(false, 'De vertrekdatum moet na de aankomstdatum liggen.');
}

if (strlen($remark) > 255 || strlen($license_plate) > 20) {
    stop(false, 'Je opmerking of kenteken is te lang.');
}

$nights = $arrival->diff($departure)->days;

try {
    $conn->beginTransaction();

    
    $stmt = $conn->prepare(
        "SELECT id, max_guests, price_per_night
         FROM locations
         WHERE id = :id AND is_active = 1
         FOR UPDATE"
    );
    $stmt->execute(['id' => $location_id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$location) {
        $conn->rollBack();
        stop(false, 'Deze plek bestaat niet of is niet beschikbaar.');
    }

    if ($guests < 1 || $guests > $location['max_guests']) {
        $conn->rollBack();
        stop(false, 'Het aantal personen moet tussen 1 en ' . $location['max_guests'] . ' liggen.');
    }

   
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM reservations
         WHERE location_id = :location_id
           AND arrival_date < :departure
           AND departure_date > :arrival"
    );
    $stmt->execute([
        'location_id' => $location_id,
        'departure'   => $departure_date,
        'arrival'     => $arrival_date,
    ]);

    if ($stmt->fetchColumn() > 0) {
        $conn->rollBack();
        stop(false, 'Deze plek is in die periode al gereserveerd.');
    }

  
    $total_price = round($nights * $location['price_per_night'], 2);

    $sql = "INSERT INTO reservations
                (user_id, location_id, arrival_date, departure_date, guests, remark, license_plate, total_price)
            VALUES
                (:user_id, :location_id, :arrival_date, :departure_date, :guests, :remark, :license_plate, :total_price)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'user_id'        => $user_id,
        'location_id'    => $location_id,
        'arrival_date'   => $arrival_date,
        'departure_date' => $departure_date,
        'guests'         => $guests,
        'remark'         => $remark === '' ? null : $remark,
        'license_plate'  => $license_plate === '' ? null : $license_plate,
        'total_price'    => $total_price,
    ]);


    $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE location_id = :location_id");
    $stmt->execute(['location_id' => $location_id]);
    $count = (int) $stmt->fetchColumn();

    $conn->commit();

    stop(true,
        'Gelukt! Je hebt ' . $nights . ' nacht(en) gereserveerd voor € ' . number_format($total_price, 2, ',', '.') . '.',
        ['count' => $count]
    );
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log($e->getMessage());
    http_response_code(500);
    stop(false, 'Er ging iets mis op de server. Probeer het later opnieuw.');
}