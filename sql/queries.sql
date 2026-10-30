$total_locations = $conn->query("SELECT COUNT(*) AS total FROM locations")->fetch(PDO::FETCH_ASSOC)['total'];
$total_members   = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_id = 1")->fetch(PDO::FETCH_ASSOC)['total'];
$total_employees = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_id = 2")->fetch(PDO::FETCH_ASSOC)['total'];

$total_profit    = $conn->query("SELECT SUM(total_price) AS total FROM reservations")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$fetch_avg_price = $conn->query("SELECT ROUND(AVG(total_price), 2) AS total FROM reservations")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$fetch_max_price = $conn->query("SELECT ROUND(MAX(total_price), 2) AS total FROM reservations")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$fetch_min_price = $conn->query("SELECT ROUND(MIN(total_price), 2) AS total FROM reservations")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$fetch_users = $conn->query(
    "SELECT users.id, users.name, users.email, users.phone, roles.name AS role_name
     FROM users
     JOIN roles ON roles.id = users.role_id
     ORDER BY users.id"
)->fetchAll(PDO::FETCH_ASSOC);


$stmt = $conn->prepare(
    "UPDATE locations
     SET location_type_id = :location_type_id,
         number = :number,
         name = :name,
         size_m2 = :size_m2,
         has_electricity = :has_electricity,
         near_water = :near_water,
         max_guests = :max_guests,
         price_per_night = :price_per_night,
         is_active = :is_active
     WHERE id = :id"
);