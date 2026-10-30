<?php
require 'database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: stats_locations.php');
    exit;
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM locations WHERE id = :id");
$stmt->execute(['id' => $id]);
$location = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$location) {
    header('Location: stats_locations.php');
    exit;
}
?>

<h1>Locatie aanpassen</h1>
<link rel="stylesheet" href="update.css">

<form method="POST" action="Update_location_process.php">
    <input type="hidden" name="id"
           value="<?= (int) $location['id'] ?>">

    <label for="location_type_id">Locatietype</label>
    <input type="number" id="location_type_id"
           name="location_type_id"
           value="<?= htmlspecialchars($location['location_type_id'] ?? '') ?>"
           required>

    <label for="number">Nummer</label>
    <input type="number" id="number" name="number"
           value="<?= htmlspecialchars($location['number'] ?? '') ?>"
           required>

    <label for="name">Naam</label>
    <input type="text" id="name" name="name"
           value="<?= htmlspecialchars($location['name'] ?? '') ?>"
           required>

    <label for="size_m2">Grootte (m²)</label>
    <input type="number" id="size_m2" name="size_m2"
           value="<?= htmlspecialchars($location['size_m2'] ?? '') ?>"
           required>

    <label for="has_electricity">Elektriciteit</label>
    <select id="has_electricity" name="has_electricity" required>
        <option value="1"
            <?= $location['has_electricity'] == 1 ? 'selected' : '' ?>>
            Ja
        </option>
        <option value="0"
            <?= $location['has_electricity'] == 0 ? 'selected' : '' ?>>
            Nee
        </option>
    </select>

    <label for="near_water">Dicht bij water</label>
    <select id="near_water" name="near_water" required>
        <option value="1"
            <?= $location['near_water'] == 1 ? 'selected' : '' ?>>
            Ja
        </option>
        <option value="0"
            <?= $location['near_water'] == 0 ? 'selected' : '' ?>>
            Nee
        </option>
    </select>

    <label for="max_guests">Maximaal aantal gasten</label>
    <input type="number" id="max_guests" name="max_guests"
           value="<?= htmlspecialchars($location['max_guests'] ?? '') ?>"
           required>

    <label for="price_per_night">Prijs per nacht (€)</label>
    <input type="number" step="0.01" id="price_per_night"
           name="price_per_night"
           value="<?= htmlspecialchars($location['price_per_night'] ?? '') ?>"
           required>

    <label for="is_active">Actief</label>
    <select id="is_active" name="is_active" required>
        <option value="1"
            <?= $location['is_active'] == 1 ? 'selected' : '' ?>>
            Ja
        </option>
        <option value="0"
            <?= $location['is_active'] == 0 ? 'selected' : '' ?>>
            Nee
        </option>
    </select>

    <button type="submit">Wijzigingen opslaan</button>
</form>
```