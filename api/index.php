<?php
require 'database.php';
require '../SoloProject/api/controller/AnimalController.php';
require '../SoloProject/api/controller/VolunteerController.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$q = $_GET['q'] ?? '';
$params = explode('/', $q);

$type = $params[0] ?? '';
$id = $params[1] ?? null;

switch ($type) {
    case 'animals':
        switch ($method) {
            case 'GET':
                if ($id) {
                    getAnimalByIdApi($pdo, $id);
                } else {
                    getAllAnimalsApi($pdo);
                }
                break;
            case 'POST':
                addAnimalApi($pdo);
                break;
            case 'PUT':
                if ($id) {
                    updateAnimalApi($pdo, $id);
                }
                break;
            case 'DELETE':
                if ($id) {
                    deleteAnimalApi($pdo, $id);
                }
                break;
        }
        break;

    case 'volunteers':
        switch ($method) {
            case 'GET':
                if ($id) {
                    getVolunteerByIdApi($pdo, $id);
                } else {
                    getAllVolunteersApi($pdo);
                }
                break;
            case 'POST':
                addVolunteerApi($pdo);
                break;
            case 'PUT':
                if ($id) {
                    updateVolunteerApi($pdo, $id);
                }
                break;
            case 'DELETE':
                if ($id) {
                    deleteVolunteerApi($pdo, $id);
                }
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Операция не найдена']);
        break;
}