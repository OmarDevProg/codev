<?php
session_start();
require 'connect.php'; // Include your database connection class

$db = new Dbf(); // Initialize the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $nom_formation = trim($_POST['nom']);
    $prenom_formation = trim($_POST['prenom']);
    $profession_formation = trim($_POST['profession']);
    $email_formation = trim($_POST['email']);
    $date_formation = trim($_POST['date_formation']);
    $date_naissance_formation = trim($_POST['date_naissance']);
    $nationalite_formation = trim($_POST['nationalite']);

    // Handle the file upload (CV)
    $doc_formationFilename = null;
    if (isset($_FILES['doc_formation'])) {
        if ($_FILES['doc_formation']['error'] === UPLOAD_ERR_OK) {
            $doc_formationTmpName = $_FILES['doc_formation']['tmp_name'];
            $doc_formationExtension = pathinfo($_FILES['doc_formation']['name'], PATHINFO_EXTENSION);
            $doc_formationFilename = uniqid() . '.' . $doc_formationExtension;
            $doc_formationDestination = 'uploads/cvs/' . $doc_formationFilename;

            // Move the uploaded CV to the target directory
            if (!move_uploaded_file($doc_formationTmpName, $doc_formationDestination)) {
                $_SESSION['error'] = "Error uploading the CV file.";
                exit();
            }
        } else {
            $_SESSION['error'] = "Error uploading the CV. Error code: " . $_FILES['doc_formation']['error'];
            exit();
        }
    } else {
        $_SESSION['error'] = "Please upload a CV file.";
        exit();
    }

    // Prepare SQL query for insertion
    $insertQuery = "INSERT INTO formation
                    (nom_formation, prenom_formation, profession_formation, email_formation, date_formation, 
                     date_naissance_formation, nationalite_formation, doc_formation) 
                    VALUES 
                    (:nom_formation, :prenom_formation, :profession_formation, :email_formation, :date_formation, 
                     :date_naissance_formation, :nationalite_formation, :doc_formation)";

    $params = [
        'nom_formation' => $nom_formation,
        'prenom_formation' => $prenom_formation,
        'profession_formation' => $profession_formation,
        'email_formation' => $email_formation,
        'date_formation' => $date_formation,
        'date_naissance_formation' => $date_naissance_formation,
        'nationalite_formation' => $nationalite_formation,
        'doc_formation' => $doc_formationFilename // Store the filename/path of the uploaded CV

    ];

    // Execute the query
    $result = $db->insert($insertQuery, $params);

    // Check if the insertion was successful
    if ($result) {
        $_SESSION['success'] = "Formateur added successfully.";
    } else {
        $_SESSION['error'] = "Error adding formateur.";
    }
    header("Location: g_formateur.php");

    // Redirect to another page or display a success message
    exit();
}
?>
