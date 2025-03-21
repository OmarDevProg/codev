<?php
session_start();
require 'connect.php'; // Include the database class

$db = new Dbf(); // Initialize the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from form
    $nom = trim($_POST['nom']);
    $org = trim($_POST['org']);
    $lieuFormation = trim($_POST['lieu_formation']);
    $pays = trim($_POST['pays']);
    $titreFormation = trim($_POST['titre_formation']);
    $dateNaissance = trim($_POST['date_naissance']);
    $email = trim($_POST['email']);
    $tel = trim($_POST['tel']);
    $adresse = trim($_POST['adresse']);
    $projet = trim($_POST['projet']);
    $fax = trim($_POST['fax']);
    $fonction = trim($_POST['fonction']);
    $typeFormation = trim($_POST['type_formation']);
    $dateDebutSession = trim($_POST['date_debut_session']);
    $dateFinSession = trim($_POST['date_fin_session']);
    $preinscription = trim($_POST['preinscription']);
    $facture = trim($_POST['facture']);
    $paiement = trim($_POST['paiement']);

    // Handle file upload for photo
    $photoFilename = null;
    if (isset($_FILES['photo'])) {
        if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoTmpName = $_FILES['photo']['tmp_name'];
            $photoExtension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION); // Get the file extension
            $photoFilename = uniqid() . '.' . $photoExtension; // Generate a unique filename for the photo

            $photoDestination = 'image/img_data-base/' . $photoFilename;

            // Move the uploaded photo to the target directory
            if (!move_uploaded_file($photoTmpName, $photoDestination)) {
                $_SESSION['error'] = "Erreur lors de l'upload de la photo.";
                header("Location: g_participant.php");
                exit();
            }
        } else {
            // Print the error code for debugging
            $_SESSION['error'] = "Erreur lors du téléchargement de la photo. Code d'erreur: " . $_FILES['photo']['error'];
            header("Location: g_participant.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Veuillez télécharger une photo.";
        header("Location: g_participants.php");
        exit();
    }

    // Prepare the insert query (without the photo field in the database)
    $insertQuery = "INSERT INTO ci_participants 
                    (nom, org, lieu_formation, pays, titre_formation, date_naissance, email, tel, adresse, projet, fax, fonction, type_formation, date_debut_session, date_fin_session, preinscription, facture, paiement) 
                    VALUES 
                    (:nom, :org, :lieuFormation, :pays, :titreFormation, :dateNaissance, :email, :tel, :adresse, :projet, :fax, :fonction, :typeFormation, :dateDebutSession, :dateFinSession, :preinscription, :facture, :paiement)";

    // Prepare parameters
    $params = [
        'nom' => $nom,
        'org' => $org,
        'lieuFormation' => $lieuFormation,
        'pays' => $pays,
        'titreFormation' => $titreFormation,
        'dateNaissance' => $dateNaissance,
        'email' => $email,
        'tel' => $tel,
        'adresse' => $adresse,
        'projet' => $projet,
        'fax' => $fax,
        'fonction' => $fonction,
        'typeFormation' => $typeFormation,
        'dateDebutSession' => $dateDebutSession,
        'dateFinSession' => $dateFinSession,
        'preinscription' => $preinscription,
        'facture' => $facture,
        'paiement' => $paiement
    ];

    // Execute the insert query using the `insert` method
    $result = $db->insert($insertQuery, $params);

    // Get the inserted participant's ID (using the correct method)
    $participantId = $db->conF->lastInsertId(); // Correctly access PDO's lastInsertId()

    // If insert is successful, save the photo with the correct participant ID
    if ($result && $participantId) {
        $finalPhotoPath = 'image/img_data-base/' . $participantId . '.jpg'; // Use participant ID as filename
        rename($photoDestination, $finalPhotoPath); // Rename the photo to match the participant's ID
        $_SESSION['success'] = "Participant ajouté avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout du participant.";
    }

    // Redirect back to the participant list or another relevant page with a success or error message
    header("Location: g_participants.php");
    exit();
}
?>
