<?php
declare(strict_types=1);

function initializeSchema(PDO $pdo): void {
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS weather        (id TINYINT PRIMARY KEY, name VARCHAR(40) UNIQUE NOT NULL);
    CREATE TABLE IF NOT EXISTS time_of_day    (id TINYINT PRIMARY KEY, name VARCHAR(40) UNIQUE NOT NULL);
    CREATE TABLE IF NOT EXISTS surface_cond   (id TINYINT PRIMARY KEY, name VARCHAR(40) UNIQUE NOT NULL);
    CREATE TABLE IF NOT EXISTS road_cond      (id TINYINT PRIMARY KEY, name VARCHAR(40) UNIQUE NOT NULL);
    CREATE TABLE IF NOT EXISTS driver_health  (id TINYINT PRIMARY KEY, name VARCHAR(40) UNIQUE NOT NULL);

    CREATE TABLE IF NOT EXISTS maneouvring (
      id TINYINT PRIMARY KEY,
      name VARCHAR(60) UNIQUE NOT NULL,
      complexity VARCHAR(20) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS trips (
      id INT AUTO_INCREMENT PRIMARY KEY,
      trip_date DATE NOT NULL,
      departure_time TIME NOT NULL,
      arrival_time TIME NOT NULL,
      mileage_km DECIMAL(7,1) NOT NULL,
      weather_id TINYINT NOT NULL,
      time_of_day_id TINYINT NOT NULL,
      surface_condition_id TINYINT NOT NULL,
      road_condition_id TINYINT NOT NULL,
      driver_health_id TINYINT NOT NULL,
      latitude DECIMAL(9,6) NULL,
      longitude DECIMAL(9,6) NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (weather_id) REFERENCES weather(id),
      FOREIGN KEY (time_of_day_id) REFERENCES time_of_day(id),
      FOREIGN KEY (surface_condition_id) REFERENCES surface_cond(id),
      FOREIGN KEY (road_condition_id) REFERENCES road_cond(id),
      FOREIGN KEY (driver_health_id) REFERENCES driver_health(id)
    );

    CREATE TABLE IF NOT EXISTS trip_maneouvring (
      trip_id INT NOT NULL,
      maneouvring_id TINYINT NOT NULL,
      PRIMARY KEY (trip_id, maneouvring_id),
      FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
      FOREIGN KEY (maneouvring_id) REFERENCES maneouvring(id)
    );
    ");
}

function seedIfEmpty(PDO $pdo, string $table, array $rows): void {
    $n = (int)$pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    if ($n === 0) {
        $cols = array_keys($rows[0]);
        $place = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
        $stmt = $pdo->prepare("INSERT INTO $table (" . implode(',', $cols) . ") VALUES $place");
        foreach ($rows as $r) $stmt->execute(array_values($r));
    }
}

function ensureDashOption(PDO $pdo, string $table): void {
    $pdo->exec("INSERT INTO $table (id,name) VALUES (0,'-') ON DUPLICATE KEY UPDATE name='-'");
}

function seedLookupTables(PDO $pdo): void {
    seedIfEmpty($pdo, 'weather', [
        ['id'=>1,'name'=>'Sunny'],['id'=>2,'name'=>'Cloudy'],['id'=>3,'name'=>'Foggy'],['id'=>4,'name'=>'Rainy'],
        ['id'=>5,'name'=>'Snowy'],['id'=>6,'name'=>'Stormy'],['id'=>7,'name'=>'Overcast'],['id'=>8,'name'=>'Gloomy'],
        ['id'=>9,'name'=>'Clear']
    ]);
    
    seedIfEmpty($pdo, 'time_of_day', [
        ['id'=>1,'name'=>'Morning'],['id'=>2,'name'=>'Afternoon'],['id'=>3,'name'=>'Evening'],['id'=>4,'name'=>'Night']
    ]);
    
    seedIfEmpty($pdo, 'surface_cond', [
        ['id'=>1,'name'=>'Dry'],['id'=>2,'name'=>'Wet'],['id'=>3,'name'=>'Icy'],['id'=>4,'name'=>'Snowy']
    ]);
    
    seedIfEmpty($pdo, 'road_cond', [
        ['id'=>1,'name'=>'Potholes'],['id'=>2,'name'=>'Curvy Roads'],['id'=>3,'name'=>'Steep Hills'],
        ['id'=>4,'name'=>'Unmarked Roads'],['id'=>5,'name'=>'New Road']
    ]);
    
    seedIfEmpty($pdo, 'driver_health', [
        ['id'=>1,'name'=>'Healthy'],['id'=>2,'name'=>'Ill'],['id'=>3,'name'=>'Injured'],['id'=>4,'name'=>'Medicated']
    ]);
    
    ensureDashOption($pdo, 'surface_cond');
    ensureDashOption($pdo, 'road_cond');
    ensureDashOption($pdo, 'driver_health');
    
    seedIfEmpty($pdo, 'maneouvring', [
        ['id'=>1,'name'=>'Lane Change','complexity'=>'Medium'],
        ['id'=>2,'name'=>'Overtaking','complexity'=>'High'],
        ['id'=>3,'name'=>'Emergency Brake','complexity'=>'High'],
        ['id'=>4,'name'=>'U-turn','complexity'=>'Medium'],
        ['id'=>5,'name'=>'Left Turn','complexity'=>'Low'],
        ['id'=>6,'name'=>'Right Turn','complexity'=>'Low'],
        ['id'=>7,'name'=>'Parking','complexity'=>'Medium'],
        ['id'=>8,'name'=>'Merging','complexity'=>'Medium']
    ]);
}

/**
 * Fetch lookup data for views
 */
function getLookupData(PDO $pdo): array {
    return [
        'weather' => $pdo->query("SELECT id,name FROM weather ORDER BY id")->fetchAll(),
        'time_of_day' => $pdo->query("SELECT id,name FROM time_of_day ORDER BY id")->fetchAll(),
        'surface' => $pdo->query("SELECT id,name FROM surface_cond ORDER BY id")->fetchAll(),
        'road' => $pdo->query("SELECT id,name FROM road_cond ORDER BY id")->fetchAll(),
        'health' => $pdo->query("SELECT id,name FROM driver_health ORDER BY id")->fetchAll(),
        'maneouvring' => $pdo->query("SELECT id,name FROM maneouvring ORDER BY id")->fetchAll()
    ];
}
