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
                    <p>€ <?php echo number_format($location['price_per_night'], 2, ',', '.') ?> per night</p>
                </div>
            </div>
        </div>

    </div>
</main>

<?php require 'footer.php'; ?>