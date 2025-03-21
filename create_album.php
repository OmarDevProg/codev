<?php
$conn = new PDO("mysql:host=localhost;dbname=codevfordb14", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['album_name'])) {
    $album_name = $_POST['album_name'];
    $stmt = $conn->prepare("INSERT INTO albums (name) VALUES (:album_name)");
    $stmt->bindParam(':album_name', $album_name);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
}
