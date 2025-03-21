<?php
session_start();
require 'connect.php'; // Inclusion de la classe de connexion à la base de données

$db = new Dbf(); // Initialisation de la connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $projet = trim($_POST['projet']);
    $fin = trim($_POST['fin']);
    $pays_projet = trim($_POST['pays_projet']);
    $email_projet = trim($_POST['email_projet']);
    $adress_projet = trim($_POST['adress_projet']);
    $email_coorprojet = trim($_POST['email_coorprojet']);
    $theme_projet = trim($_POST['theme_projet']);
    $date_projet = trim($_POST['date_projet']);
    $site_projet = trim($_POST['site_projet']);

    // Traitement de l'upload de la photo
    $photoFilename = null;
    if (isset($_FILES['photo'])) {
        if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoTmpName = $_FILES['photo']['tmp_name'];
            $photoExtension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photoFilename = uniqid() . '.' . $photoExtension;
            $photoDestination = 'image/img_projet/' . $photoFilename;

            // Déplacement de la photo uploadée vers le dossier cible
            if (!move_uploaded_file($photoTmpName, $photoDestination)) {
                $_SESSION['error'] = "Erreur lors de l'upload de la photo.";
                header("Location: g_projet.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Erreur lors du téléchargement de la photo. Code d'erreur: " . $_FILES['photo']['error'];
            header("Location: g_projet.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Veuillez télécharger une photo.";
        header("Location: g_projet.php");
        exit();
    }

    // Préparation de la requête d'insertion
    $insertQuery = "INSERT INTO projet
                    (projet, fin, pays_projet, email_projet, adress_projet, email_coorprojet, theme_projet, date_projet, site_projet) 
                    VALUES 
                    (:projet, :fin, :pays_projet, :email_projet, :adress_projet, :email_coorprojet, :theme_projet, :date_projet, :site_projet)";

    $params = [
        'projet'         => $projet,
        'fin'            => $fin,
        'pays_projet'    => $pays_projet,
        'email_projet'   => $email_projet,
        'adress_projet'  => $adress_projet,
        'email_coorprojet' => $email_coorprojet,
        'theme_projet'   => $theme_projet,
        'date_projet'    => $date_projet,
        'site_projet'    => $site_projet
    ];

    // Exécution de la requête d'insertion
    $result = $db->insert($insertQuery, $params);

    // Récupération de l'ID du projet inséré
    $projetId = $db->conF->lastInsertId();

    // Si l'insertion est réussie, renomme la photo pour qu'elle corresponde à l'ID du projet
    if ($result && $projetId) {
        $finalPhotoPath = 'image/img_projet/' . $projetId . '.jpg';
        rename($photoDestination, $finalPhotoPath);
        $_SESSION['success'] = "Projet ajouté avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout du projet.";
    }

    // Redirection vers la liste des projets ou une autre page pertinente
    header("Location: g_projet.php");
    exit();
}
?>
