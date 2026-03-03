<?php
// db.php
$host = "sql108.infinityfree.com";       // MySQL host
$dbname = "if0_41251564_todo_db";       // your database name
$user = "if0_41251564";            // MySQL username
$pass = "WERtE9KGVZxCA";                // MySQL password

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>