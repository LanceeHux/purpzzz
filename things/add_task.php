<?php
header("Content-Type: application/json");
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['task']) && !empty(trim($data['task']))) {

    $task = trim($data['task']);

    // Optional: accept created_at from JS, otherwise use NOW()
    $created_at = $data['created_at'] ?? null;

    if ($created_at) {
        // Convert ISO (from JS) to MySQL DATETIME
        try {
            $dt = new DateTime($created_at);
            $created_at = $dt->format("Y-m-d H:i:s");
        } catch (Exception $e) {
            $created_at = null; // fallback to NOW()
        }
    }

    if ($created_at) {
        $stmt = $conn->prepare("INSERT INTO tasks (task, created_at) VALUES (?, ?)");
        $stmt->bind_param("ss", $task, $created_at);
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (task) VALUES (?)");
        $stmt->bind_param("s", $task);
    }

    if($stmt->execute()) {
        // If DB default timestamp is used, fetch it back
        $id = $stmt->insert_id;

        $res = $conn->query("SELECT id, task, created_at FROM tasks WHERE id = $id LIMIT 1");
        $row = $res ? $res->fetch_assoc() : null;

        echo json_encode($row ?: [
            "id" => $id,
            "task" => $task,
            "created_at" => $created_at
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $stmt->error]);
    }

    $stmt->close();

} else {
    http_response_code(400);
    echo json_encode(["error" => "Task is empty"]);
}

$conn->close();
?>