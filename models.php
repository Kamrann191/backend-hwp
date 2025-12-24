<?php
declare(strict_types=1);

require_once __DIR__ . '/session.php';

class DrivingExperience {
    private ?int $id;
    private string $tripDate;
    private string $departureTime;
    private string $arrivalTime;
    private float $mileageKm;
    private int $weatherId;
    private int $timeOfDayId;
    private int $surfaceConditionId;
    private int $roadConditionId;
    private int $driverHealthId;
    private ?float $latitude;
    private ?float $longitude;
    private array $maneouvrings;
    private ?string $weatherName = null;
    private ?string $timeOfDayName = null;
    private ?string $surfaceName = null;
    private ?string $roadName = null;
    private ?string $driverHealthName = null;
    private ?int $durationSeconds = null;
    
    public function __construct(
        string $tripDate,
        string $departureTime,
        string $arrivalTime,
        float $mileageKm,
        int $weatherId,
        int $timeOfDayId,
        int $surfaceConditionId,
        int $roadConditionId,
        int $driverHealthId,
        ?float $latitude = null,
        ?float $longitude = null,
        array $maneouvrings = [],
        ?int $id = null
    ) {
        $this->id = $id;
        $this->tripDate = $tripDate;
        $this->departureTime = $departureTime;
        $this->arrivalTime = $arrivalTime;
        $this->mileageKm = $mileageKm;
        $this->weatherId = $weatherId;
        $this->timeOfDayId = $timeOfDayId;
        $this->surfaceConditionId = $surfaceConditionId;
        $this->roadConditionId = $roadConditionId;
        $this->driverHealthId = $driverHealthId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->maneouvrings = $maneouvrings;
    }
    
    public function getId(): ?int { return $this->id; }
    public function getTripDate(): string { return $this->tripDate; }
    public function getDepartureTime(): string { return $this->departureTime; }
    public function getArrivalTime(): string { return $this->arrivalTime; }
    public function getMileageKm(): float { return $this->mileageKm; }
    public function getWeatherId(): int { return $this->weatherId; }
    public function getTimeOfDayId(): int { return $this->timeOfDayId; }
    public function getSurfaceConditionId(): int { return $this->surfaceConditionId; }
    public function getRoadConditionId(): int { return $this->roadConditionId; }
    public function getDriverHealthId(): int { return $this->driverHealthId; }
    public function getLatitude(): ?float { return $this->latitude; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function getManeouvrings(): array { return $this->maneouvrings; }
    
    public function setId(int $id): void { $this->id = $id; }
    public function setWeatherName(?string $name): void { $this->weatherName = $name; }
    public function setTimeOfDayName(?string $name): void { $this->timeOfDayName = $name; }
    public function setSurfaceName(?string $name): void { $this->surfaceName = $name; }
    public function setRoadName(?string $name): void { $this->roadName = $name; }
    public function setDriverHealthName(?string $name): void { $this->driverHealthName = $name; }
    public function setDurationSeconds(?int $seconds): void { $this->durationSeconds = $seconds; }
    public function setManeouvrings(array $maneouvrings): void { $this->maneouvrings = $maneouvrings; }
    
    public function calculateDuration(): int {
        $dep = strtotime($this->departureTime);
        $arr = strtotime($this->arrivalTime);
        
        if ($arr >= $dep) {
            return $arr - $dep;
        } else {
            // Crossing midnight
            return (86400 - $dep) + $arr;
        }
    }
    
    public function validate(): array {
        $errors = [];
        
        if (empty($this->tripDate)) $errors[] = "Trip date is required";
        if (empty($this->departureTime)) $errors[] = "Departure time is required";
        if (empty($this->arrivalTime)) $errors[] = "Arrival time is required";
        if ($this->mileageKm <= 0) $errors[] = "Mileage must be positive";
        if ($this->weatherId <= 0) $errors[] = "Weather is required";
        if ($this->timeOfDayId <= 0) $errors[] = "Time of day is required";
        
        return $errors;
    }
    
    public function toArray(): array {
        return [
            'trip_date' => $this->tripDate,
            'departure_time' => $this->departureTime,
            'arrival_time' => $this->arrivalTime,
            'mileage_km' => $this->mileageKm,
            'weather_id' => $this->weatherId,
            'time_of_day_id' => $this->timeOfDayId,
            'surface_condition_id' => $this->surfaceConditionId,
            'road_condition_id' => $this->roadConditionId,
            'driver_health_id' => $this->driverHealthId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
    
    public function toJson(): array {
        return [
            'id' => $this->id,
            'trip_date' => $this->tripDate,
            'departure_time' => $this->departureTime,
            'arrival_time' => $this->arrivalTime,
            'mileage_km' => $this->mileageKm,
            'weather' => $this->weatherName,
            'time_of_day' => $this->timeOfDayName,
            'surface' => $this->surfaceName,
            'road' => $this->roadName,
            'driver_health' => $this->driverHealthName,
            'duration_seconds' => $this->durationSeconds ?? $this->calculateDuration(),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'maneouvrings' => $this->maneouvrings,
            'anonymous_id' => $this->id ? anonymizeId($this->id) : null
        ];
    }
    
    /**
     * Create from database row
     */
    public static function fromDatabaseRow(array $row): self {
        $trip = new self(
            $row['trip_date'],
            $row['departure_time'],
            $row['arrival_time'],
            (float)$row['mileage_km'],
            (int)$row['weather_id'] ?? 0,
            (int)$row['time_of_day_id'] ?? 0,
            (int)$row['surface_condition_id'] ?? 0,
            (int)$row['road_condition_id'] ?? 0,
            (int)$row['driver_health_id'] ?? 0,
            isset($row['latitude']) ? (float)$row['latitude'] : null,
            isset($row['longitude']) ? (float)$row['longitude'] : null,
            $row['maneouvrings'] ?? [],
            isset($row['id']) ? (int)$row['id'] : null
        );
        
        // Set display names if available
        if (isset($row['weather'])) $trip->setWeatherName($row['weather']);
        if (isset($row['time_of_day'])) $trip->setTimeOfDayName($row['time_of_day']);
        if (isset($row['surface'])) $trip->setSurfaceName($row['surface']);
        if (isset($row['road'])) $trip->setRoadName($row['road']);
        if (isset($row['driver_health'])) $trip->setDriverHealthName($row['driver_health']);
        if (isset($row['duration_seconds'])) $trip->setDurationSeconds((int)$row['duration_seconds']);
        
        return $trip;
    }
    
    /**
     * Create from JSON payload
     */
    public static function fromJsonPayload(array $data, ?int $id = null): self {
        return new self(
            $data['date'] ?? '',
            $data['departureTime'] ?? '',
            $data['arrivalTime'] ?? '',
            (float)($data['mileageKm'] ?? 0),
            (int)($data['weather'] ?? 0),
            (int)($data['timeOfDay'] ?? 0),
            isset($data['surface']) && $data['surface'] !== '' ? (int)$data['surface'] : 0,
            isset($data['road']) && $data['road'] !== '' ? (int)$data['road'] : 0,
            isset($data['driverHealth']) && $data['driverHealth'] !== '' ? (int)$data['driverHealth'] : 0,
            isset($data['latitude']) ? (float)$data['latitude'] : null,
            isset($data['longitude']) ? (float)$data['longitude'] : null,
            $data['maneouvrings'] ?? [],
            $id
        );
    }
}
