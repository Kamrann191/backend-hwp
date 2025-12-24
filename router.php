<?php
declare(strict_types=1);

require_once __DIR__ . '/controllers.php';

class Router {
    private PDO $pdo;
    private array $controllers = [];
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->registerControllers();
    }
    
    private function registerControllers(): void {
        $this->controllers['trip'] = new TripController($this->pdo);
        $this->controllers['stats'] = new StatisticsController($this->pdo);
        $this->controllers['view'] = new ViewController($this->pdo);
    }
    
    public function dispatch(): void {
        if (!isset($_GET['api'])) {
            $this->controllers['view']->handleRequest('render');
            return;
        }
        
        $action = $_GET['api'];
        
        $routes = [
            'add_trip' => 'trip',
            'update_trip' => 'trip',
            'delete_trip' => 'trip',
            'get_trip' => 'trip',
            'list_trips' => 'trip',
            'stats' => 'stats'
        ];
        
        if (!isset($routes[$action])) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unknown API endpoint']);
            exit;
        }
        
        $controllerKey = $routes[$action];
        $this->controllers[$controllerKey]->handleRequest($action);
    }
}
