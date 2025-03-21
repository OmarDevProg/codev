<?php
if (isset($_POST['image_id'])) {
    try {
        $conn = new PDO("mysql:host=localhost;dbname=codevfordb14", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $image_id = $_POST['image_id'];

        // Get the image path from the database
        $stmt = $conn->prepare("SELECT image_path FROM images WHERE id = :image_id");
        $stmt->execute(['image_id' => $image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            // Delete the image file from the server
            $imagePath = $image['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath); // Remove the image file
            }

            // Delete the image record from the database
            $stmt = $conn->prepare("DELETE FROM images WHERE id = :image_id");
            $stmt->execute(['image_id' => $image_id]);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Image not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
