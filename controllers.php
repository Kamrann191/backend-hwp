<?php
declare(strict_types=1);

require_once __DIR__ . '/repository.php';
require_once __DIR__ . '/session.php';

abstract class BaseController {
    protected PDO $pdo;
    protected array $request;
    protected string $method;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->request = $this->parseRequest();
    }
    
    protected function parseRequest(): array {
        if ($this->method === 'POST' || $this->method === 'PUT' || $this->method === 'DELETE') {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            return is_array($data) ? $data : [];
        }
        return $_GET;
    }
    
    protected function jsonResponse($data, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
    
    protected function errorResponse(string $message, int $code = 400): never {
        $this->jsonResponse(['error' => $message], $code);
    }
    
    protected function successResponse($data): never {
        $this->jsonResponse(['ok' => true, 'data' => $data]);
    }
    
    abstract public function handleRequest(string $action): void;
}

class TripController extends BaseController {
    private DrivingExperienceRepository $repository;
    
    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->repository = new DrivingExperienceRepository($pdo);
    }
    
    public function handleRequest(string $action): void {
        switch ($action) {
            case 'add_trip':
                $this->create();
                break;
            case 'update_trip':
                $this->update();
                break;
            case 'delete_trip':
                $this->delete();
                break;
            case 'get_trip':
                $this->show();
                break;
            case 'list_trips':
                $this->index();
                break;
            default:
                $this->errorResponse('Unknown action', 404);
        }
    }
    
    private function create(): void {
        if ($this->method !== 'POST') {
            $this->errorResponse('Method not allowed', 405);
        }
        
        try {
            $trip = DrivingExperience::fromJsonPayload($this->request);
            
            $errors = $trip->validate();
            if (!empty($errors)) {
                $this->errorResponse(implode(', ', $errors), 400);
            }
            
            $tripId = $this->repository->save($trip);
            $anonymousId = anonymizeId($tripId);
            cleanOldCodes();
            
            $this->jsonResponse([
                'ok' => true,
                'id' => $anonymousId,
                'realId' => $tripId
            ]);
        } catch (Throwable $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }
    
    private function update(): void {
        if ($this->method !== 'POST') {
            $this->errorResponse('Method not allowed', 405);
        }
        
        if (!isset($this->request['id'])) {
            $this->errorResponse('Missing id', 400);
        }
        
        // Deanonymize ID
        $realId = deanonymizeId($this->request['id']);
        if ($realId === null) {
            $this->errorResponse('Invalid or expired session ID', 403);
        }
        
        try {
            $trip = DrivingExperience::fromJsonPayload($this->request, $realId);
            
            $errors = $trip->validate();
            if (!empty($errors)) {
                $this->errorResponse(implode(', ', $errors), 400);
            }
            
            $this->repository->update($trip);
            
            $this->jsonResponse([
                'ok' => true,
                'id' => $this->request['id']
            ]);
        } catch (Throwable $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }
    
    private function delete(): void {
        if (!isset($this->request['id'])) {
            $this->errorResponse('Missing id', 400);
        }
        
        $realId = deanonymizeId($this->request['id']);
        if ($realId === null) {
            $this->errorResponse('Invalid or expired session ID', 403);
        }
        
        try {
            $this->repository->delete($realId);
            $this->successResponse(null);
        } catch (Throwable $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }
    
    private function show(): void {
        if (!isset($this->request['id'])) {
            $this->errorResponse('Missing id', 400);
        }
        
        $realId = deanonymizeId($this->request['id']);
        if ($realId === null) {
            $this->errorResponse('Invalid or expired session ID', 403);
        }
        
        try {
            $trip = $this->repository->findById($realId);
            if (!$trip) {
                $this->errorResponse('Not found', 404);
            }
            $tripData = $trip->toArray();
            $tripData['id'] = $this->request['id'];
            $tripData['maneouvrings'] = $trip->getManeouvrings();
            
            $this->jsonResponse($tripData);
        } catch (Throwable $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }
    
    /**
     * List all trips (GET)
     */
    private function index(): void {
        try {
            $trips = $this->repository->findAll();
            
            // Convert to JSON array
            $result = [];
            foreach ($trips as $trip) {
                $result[] = $trip->toJson();
            }
            
            cleanOldCodes();
            $this->jsonResponse($result);
        } catch (Throwable $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }
}

/*  STATISTICS CONTROLLER  */
class StatisticsController extends BaseController {
    public function handleRequest(string $action): void {
        if ($action === 'stats') {
            $this->getStatistics();
        } else {
            $this->errorResponse('Unknown action', 404);
        }
    }
    
    /**
     * Get dashboard statistics
     */
    private function getStatistics(): void {
        try {
            $data = [
                'kpis' => $this->getKPIs(),
                'series' => [
                    $this->getDistribution('weather_id', 'weather', 'Weather'),
                    $this->getDistribution('time_of_day_id', 'time_of_day', 'Time of Day'),
                    $this->getDistribution('surface_condition_id', 'surface_cond', 'Surface'),
                    $this->getDistribution('road_condition_id', 'road_cond', 'Road'),
                    $this->getDistribution('driver_health_id', 'driver_health', 'Driver Health'),
                    $this->getManeouvring()
                ]
            ];
            
            $this->jsonResponse($data);
        } catch (Throwable $e) {
            $this->errorResponse($e->getMessage(), 500);
        }
    }
    
    private function getKPIs(): array {
        return $this->pdo->query("
            SELECT
                COUNT(*) AS trips,
                COALESCE(SUM(mileage_km),0) AS total_km,
                COALESCE(SUM(
                    CASE WHEN arrival_time >= departure_time
                         THEN TIME_TO_SEC(TIMEDIFF(arrival_time, departure_time))
                         ELSE TIME_TO_SEC(TIMEDIFF(ADDTIME(arrival_time,'24:00:00'), departure_time))
                    END
                ),0) AS total_seconds
            FROM trips
        ")->fetch();
    }
    
    private function getDistribution(string $field, string $table, string $label): array {
        $sql = "SELECT l.name AS label, COUNT(*) AS cnt
                FROM trips t JOIN $table l ON l.id = t.$field
                GROUP BY l.id, l.name ORDER BY l.id";
        return [
            'label' => $label,
            'data' => $this->pdo->query($sql)->fetchAll()
        ];
    }
    
    private function getManeouvring(): array {
        $data = $this->pdo->query("
            SELECT m.name AS label, COUNT(*) AS cnt
            FROM maneouvring m
            JOIN trip_maneouvring tm ON tm.maneouvring_id = m.id
            GROUP BY m.id, m.name ORDER BY m.id
        ")->fetchAll();
        
        return ['label' => 'Maneouvring', 'data' => $data];
    }
}

/*  VIEW CONTROLLER  */
class ViewController extends BaseController {
    public function handleRequest(string $action): void {
        // This controller handles the HTML view rendering
        // Since we have a single-page app, we'll render the full page
        $this->renderView();
    }
    
    private function renderView(): void {
        // Get lookup data for form dropdowns
        $lookups = $this->getLookupsForView();
        
        // Pass to view (HTML section will use this)
        global $lookupsForView;
        $lookupsForView = $lookups;
    }
    
    private function getLookupsForView(): array {
        return [
            'weather' => $this->pdo->query("SELECT id,name FROM weather ORDER BY id")->fetchAll(),
            'time_of_day' => $this->pdo->query("SELECT id,name FROM time_of_day ORDER BY id")->fetchAll(),
            'surface' => $this->pdo->query("SELECT id,name FROM surface_cond ORDER BY id")->fetchAll(),
            'road' => $this->pdo->query("SELECT id,name FROM road_cond ORDER BY id")->fetchAll(),
            'health' => $this->pdo->query("SELECT id,name FROM driver_health ORDER BY id")->fetchAll(),
            'maneouvring' => $this->pdo->query("SELECT id,name FROM maneouvring ORDER BY id")->fetchAll()
        ];
    }
}
