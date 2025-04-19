<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root";
$password = "";
$database = "goqiidb";

// DB connection
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
//print_r($method);die;
// Extract ID from query string if needed
parse_str($_SERVER['QUERY_STRING'] ?? "", $params);
$id = $params['id'] ?? null;

// CREATE: Handle POST request
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        echo json_encode(["error" => "No data provided"]);
        exit;
    }

    /*$name = $data['name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $dob = $data['dob']; */
    $errors = [];
$data = json_decode(file_get_contents("php://input"), true);

// Validate name
if (empty($data['name'])) {
    $errors[] = "Name is required.";
} else {
    $name = trim($data['name']);
}

// Validate email
if (empty($data['email'])) {
    $errors[] = "Email is required.";
} elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
} else {
    $email = trim($data['email']);
}

// Validate password
if (empty($data['password'])) {
    $errors[] = "Password is required.";
} elseif (strlen($data['password']) < 6) {
    $errors[] = "Password must be at least 6 characters.";
} else {
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
}

// Validate date of birth
if (empty($data['dob'])) {
    $errors[] = "Date of birth is required.";
} elseif (!DateTime::createFromFormat('Y-m-d', $data['dob'])) {
    $errors[] = "Invalid date format. Use YYYY-MM-DD.";
} else {
    $dob = $data['dob'];
}

// Handle errors
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(["errors" => $errors]);
    exit;
}



    $stmt = $conn->prepare("INSERT INTO users (name, email, password, dob) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $dob);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User created"]);
    } else {
        echo json_encode(["error" => "Failed to insert user"]);
    }
    $stmt->close();
}

// READ: Handle GET request
elseif ($method === 'GET') {
    $result = $conn->query("SELECT id, name, email, dob FROM users ORDER BY id DESC");
    $users = [];

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
}

// UPDATE: Handle PUT request
elseif ($method === 'PUT') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Invalid JSON in PUT request']);
        exit;
    }

    if (!isset($_GET['id'])) {
        echo json_encode(['error' => 'User ID required']);
        exit;
    }

    $id = $_GET['id'];
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $dob = $data['dob'] ?? '';
    $password = $data['password'] ?? '';

    if ($password !== '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, dob=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $dob, $hashedPassword, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, dob=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $dob, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(['message' => 'User updated successfully']);
    } else {
        echo json_encode(['error' => 'Update failed: ' . $stmt->error]);
    }
    exit;
}


// DELETE: Handle DELETE request
elseif ($method === 'DELETE' && $id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User deleted"]);
    } else {
        echo json_encode(["error" => "Delete failed"]);
    }
    $stmt->close();
}

$conn->close();
?>
