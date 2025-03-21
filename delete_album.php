<?php
// delete_album.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get the album id from the request
        $albumId = $_POST['album_id'];

        // Establish database connection
        $conn = new PDO("mysql:host=localhost;dbname=codevfordb14", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Delete associated images from the images table
        $stmt = $conn->prepare("DELETE FROM images WHERE album_id = :album_id");
        $stmt->bindParam(':album_id', $albumId);
        $stmt->execute();

        // Delete the album from the albums table
        $stmt = $conn->prepare("DELETE FROM albums WHERE id = :id");
        $stmt->bindParam(':id', $albumId);
        $stmt->execute();

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
