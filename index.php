<?php
// CORS заголовки для Swagger UI
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// Обработка preflight запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'database.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';
$params = explode('/', $path);

$resource = $params[0] ?? '';
$id = $params[1] ?? null;

switch ($resource) {
    case 'animals':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
                    $stmt->execute([$id]);
                    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo json_encode($animal ?: ['error' => 'Animal not found']);
                } else {
                    $stmt = $pdo->query("SELECT * FROM animals ORDER BY id");
                    $animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($animals);
                }
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $sql = "INSERT INTO animals (name, species, breed, age, status, photo_url) 
                        VALUES (?, ?, ?, ?, 'waiting', ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $data['name'],
                    $data['species'],
                    $data['breed'] ?? '',
                    $data['age'],
                    $data['photo_url'] ?? ''
                ]);
                echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
                break;
                
            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents('php://input'), true);
                    $sql = "UPDATE animals SET name=?, species=?, breed=?, age=?, status=?, photo_url=? WHERE id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        $data['name'],
                        $data['species'],
                        $data['breed'] ?? '',
                        $data['age'],
                        $data['status'],
                        $data['photo_url'] ?? '',
                        $id
                    ]);
                    echo json_encode(['success' => true]);
                }
                break;
                
            case 'DELETE':
                if ($id) {
                    $stmt = $pdo->prepare("DELETE FROM animals WHERE id=?");
                    $stmt->execute([$id]);
                    echo json_encode(['success' => true]);
                }
                break;
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        break;
}
?>