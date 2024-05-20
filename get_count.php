<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Соединение не удалось: " . $conn->connect_error);
}

$sql = "SELECT COUNT(*) AS count FROM users";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode($row['count'], JSON_NUMERIC_CHECK);

$conn->close();
?>