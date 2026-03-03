<?php
header("Content-Type: application/json");
include 'db.php';

$sql = "SELECT id, task, created_at FROM tasks ORDER BY id DESC";
$result = $conn->query($sql);

$tasks = [];
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $tasks[] = $row;
    }
}

echo json_encode($tasks);
$conn->close();
?>