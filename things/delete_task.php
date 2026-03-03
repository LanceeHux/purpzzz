<?php
header("Content-Type: application/json");
include 'db.php';

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql = "DELETE FROM tasks WHERE id=$id";
    if($conn->query($sql)){
        echo json_encode(["deleted" => $id]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => $conn->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "No ID provided"]);
}

$conn->close();
?>