<?php
// rename_album.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get the album id and the new album name from the request
        $albumId = $_POST['album_id'];
        $newName = $_POST['new_name'];

        // Establish database connection
        $conn = new PDO("mysql:host=localhost;dbname=codevfordb14", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Update album name in the database
        $stmt = $conn->prepare("UPDATE albums SET name = :name WHERE id = :id");
        $stmt->bindParam(':name', $newName);
        $stmt->bindParam(':id', $albumId);
        $stmt->execute();

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
