<?php
session_start();
require 'database.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!in_array($_SESSION['role_id'] ?? null, [2, 3])) {
    header('Location: 403.php');
    exit;
}

$errors = [];
$location_type_id = '';
$number = '';
$name = '';
$size_m2 = '';
$has_electricity = 0;
$near_water = 0;
$photo_url = '';
$max_guests = '';
$price_per_night = '';
$is_active = 1;

try {
   
    $stmt = $conn->prepare("SELECT id, name FROM location_types ORDER BY name");
    $stmt->execute();
    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: 500.php');
    exit;
}

if (isset($_POST['submit'])) {
    $location_type_id = (int) ($_POST['location_type_id'] ?? 0);
    $number = (int) ($_POST['number'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $size_m2 = (float) ($_POST['size_m2'] ?? 0);
    $has_electricity = isset($_POST['has_electricity']) ? 1 : 0;
    $near_water = isset($_POST['near_water']) ? 1 : 0;
    $photo_url = trim($_POST['photo_url'] ?? '');
    $max_guests = (int) ($_POST['max_guests'] ?? 0);
    $price_per_night = (float) ($_POST['price_per_night'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($name === '') {
        $errors[] = 'Vul een naam in.';
    }

    if ($number < 1) {
        $errors[] = 'Het plaatsnummer moet minimaal 1 zijn.';
    }

    if ($size_m2 <= 0) {
        $errors[] = 'De oppervlakte moet groter zijn dan 0.';
    }

    if ($max_guests < 1) {
        $errors[] = 'Het maximaal aantal gasten moet minimaal 1 zijn.';
    }

    if ($price_per_night <= 0 || $price_per_night > 9999.99) {
        $errors[] = 'De prijs per nacht moet tussen 0,01 en 9999,99 liggen.';
    }

    if (strlen($photo_url) > 255) {
        $errors[] = 'De foto-url is te lang (maximaal 255 tekens).';
    }

    try {
        
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM location_types WHERE id = :id");
            $stmt->execute(['id' => $location_type_id]);
            if (!$stmt->fetch()) {
                $errors[] = 'Kies een geldige soort plek.';
            }
        }

      
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM locations WHERE number = :number AND deleted_at IS NULL");
            $stmt->execute(['number' => $number]);
            if ($stmt->fetch()) {
                $errors[] = 'Plaatsnummer ' . $number . ' bestaat al.';
            }
        }

        if (empty($errors)) {
            $conn->beginTransaction();

            $stmt = $conn->prepare(
                "INSERT INTO locations
                    (location_type_id, number, name, size_m2, has_electricity, near_water,
                     photo_url, max_guests, price_per_night, is_active)
                 VALUES
                    (:location_type_id, :number, :name, :size_m2, :has_electricity, :near_water,
                     :photo_url, :max_guests, :price_per_night, :is_active)"
            );
            $stmt->execute([
                'location_type_id' => $location_type_id,
                'number' => $number,
                'name' => $name,
                'size_m2' => $size_m2,
                'has_electricity' => $has_electricity,
                'near_water' => $near_water,
                'photo_url' => $photo_url,
                'max_guests' => $max_guests,
                'price_per_night' => $price_per_night,
                'is_active' => $is_active,
            ]);

            $new_id = (int) $conn->lastInsertId();

            // Logboek
            $stmt = $conn->prepare(
                "INSERT INTO changes (user_id, location_id, changed_at, change_type, old_value, new_value)
                 VALUES (:user_id, :location_id, NOW(), 'aangemaakt', '-', 'nieuw')"
            );
            $stmt->execute([
                'user_id' => (int) $_SESSION['user_id'],
                'location_id' => $new_id,
            ]);

            $conn->commit();

            $_SESSION['success'] = 'Plek ' . $number . ' is aangemaakt.';
            header('Location: Location_detail.php?id=' . $new_id);
            exit;
        }
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log($e->getMessage());
        header('Location: 500.php');
        exit;
    }
}

require 'header.php';
?>
<link rel="stylesheet" href="style.css">
<main>
    <div class="profile-card">
        <h2>Plek aanmaken</h2>
        <p class="profile-intro">Voeg een nieuwe plek toe aan de camping.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="profile-form">
            <label>
                Soort plek
                <select name="location_type_id" required>
                    <option value="">Kies een soort</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?php echo (int) $type['id'] ?>" <?php echo ((int) $location_type_id === (int) $type['id']) ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Plaatsnummer
                <input type="number" name="number" min="1" value="<?php echo htmlspecialchars((string) $number) ?>"
                    required>
            </label>

            <label>
                Naam
                <input type="text" name="name" value="<?php echo htmlspecialchars($name) ?>" required>
            </label>

            <label>
                Oppervlakte (m²)
                <input type="number" name="size_m2" min="1" step="0.01"
                    value="<?php echo htmlspecialchars((string) $size_m2) ?>" required>
            </label>

            <label>
                Maximaal aantal gasten
                <input type="number" name="max_guests" min="1"
                    value="<?php echo htmlspecialchars((string) $max_guests) ?>" required>
            </label>

            <label>
                Prijs per nacht (€)
                <input type="number" name="price_per_night" min="0.01" max="9999.99" step="0.01"
                    value="<?php echo htmlspecialchars((string) $price_per_night) ?>" required>
            </label>

            <label>
                Foto-url (verplicht, bijvoorbeeld img/plekken/tent-01.jpg)
                <input type="text" name="photo_url" maxlength="255" value="<?php echo htmlspecialchars($photo_url) ?>" >
            </label>

            <label>
                <input type="checkbox" name="has_electricity" <?php echo $has_electricity ? 'checked' : '' ?>>
                Met stroom
            </label>

            <label>
                <input type="checkbox" name="near_water" <?php echo $near_water ? 'checked' : '' ?>>
                Bij het water
            </label>

            <label>
                <input type="checkbox" name="is_active" <?php echo $is_active ? 'checked' : '' ?>>
                Direct boekbaar (actief)
            </label>

            <button type="submit" name="submit">Plek aanmaken</button>
        </form>

        <p class="form-note"><a href="stats_locations.php">Terug naar alle plekken</a></p>
    </div>
</main>

<?php require 'footer.php'; ?>