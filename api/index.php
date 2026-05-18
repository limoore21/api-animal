<?php
require '../database.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$q = $_GET['q'] ?? '';
$params = explode('/', $q);
$resource = $params[0] ?? '';
$id = $params[1] ?? null;

switch ($resource) {
    case 'animals':
        switch ($method) {
            case 'GET':
                if ($id) {
                    $stmt = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
                    $stmt->execute([$id]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo json_encode($result ?: []);
                } else {
                    $stmt = $pdo->query("SELECT * FROM animals ORDER BY id");
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($result);
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

            default:
                echo json_encode(['error' => 'Method not allowed']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        break;
}