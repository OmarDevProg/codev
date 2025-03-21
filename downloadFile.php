<?php
if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // Define the full file path
    $filePath = './doc/' . $file;  // Adjust this path as per your folder structure

    if (file_exists($filePath)) {
        // Set headers to force file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));

        // Read the file
        readfile($filePath);
        exit();
    } else {
        echo 'File not found.';
    }
}
?>
