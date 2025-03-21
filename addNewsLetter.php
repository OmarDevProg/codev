<?php
session_start();
require_once 'connect.php';

// Google reCAPTCHA secret key
$secret_key = '6LecePkqAAAAANp9HMwEz8Xi2KtQ5B0qRShdiSJZ';

// Form processing logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = trim($_POST['email']);
    $ip = isset($_POST['ip']) && !empty($_POST['ip']) ? $_POST['ip'] : $_SERVER['REMOTE_ADDR']; // If IP is empty, use server's IP
    $date_submission = $_POST['date_submission'] ?? null;
    $captcha_response = $_POST['g-recaptcha-response'] ?? '';

    // Check if required fields are not empty
    if (empty($email) || empty($date_submission)) {
        $_SESSION['error'] = "Email and Submission Date are required.";
        header("Location: newsletter.php");
        exit;
    }

    // Verify reCAPTCHA response
    $captcha_verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($captcha_verify_url . "?secret=" . $secret_key . "&response=" . $captcha_response . "&remoteip=" . $ip);
    $response_keys = json_decode($response, true);

    if (!isset($response_keys['success']) || intval($response_keys['success']) !== 1) {
        // CAPTCHA failed
        $_SESSION['error'] = "Please verify that you are not a robot.";
        header("Location: newsletter.php");
        exit;
    }

    // Proceed to insert data into the database if CAPTCHA is verified
    try {
        // Instantiate the Dbf class
        $db = new Dbf();

        // Prepare the query to insert data into the newsletter table
        $query = "INSERT INTO `eform-inscription-newsletter` (email, ip, date_submission) VALUES (:email, :ip, :date_submission)";

        // Define parameters for the query
        $params = [
            ':email' => $email,
            ':ip' => $ip,
            ':date_submission' => $date_submission,
        ];

        // Execute the insert query
        $insert_id = $db->insert($query, $params);

        // If insert is successful, store success message
        if ($insert_id) {
            $_SESSION['success'] = "Newsletter ajoutée avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout de la newsletter.";
        }
    } catch (Exception $e) {
        // Catch any errors and store them in session
        $_SESSION['error'] = "Une erreur est survenue lors de l'ajout: " . $e->getMessage();
    }

    // Redirect back to the newsletter list or another relevant page
    header("Location: newsletters.php");
    exit();
}
?>
