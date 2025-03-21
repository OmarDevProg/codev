<?php
session_start();
require 'connect.php'; // Inclusion de la classe de connexion à la base de données

$db = new Dbf(); // Initialisation de la connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire pour le projet
    $code_projet      = $_POST['code_projet'];
    $projet           = trim($_POST['projet']) ?: null;
    $fin              = trim($_POST['fin']) ?: null;
    $pays_projet      = trim($_POST['pays_projet']) ?: null;
    $email_projet     = trim($_POST['email_projet']) ?: null;
    $adress_projet    = trim($_POST['adress_projet']) ?: null;
    $email_coorprojet = trim($_POST['email_coorprojet']) ?: null;
    $theme_projet     = trim($_POST['theme_projet']) ?: null;
    $date_projet      = trim($_POST['date_projet']) ?: null;
    $site_projet      = trim($_POST['site_projet']) ?: null;

    // Préparation de la requête de mise à jour
    $updateQuery = "UPDATE projet SET 
        projet = :projet, 
        fin = :fin, 
        pays_projet = :pays_projet, 
        email_projet = :email_projet, 
        adress_projet = :adress_projet, 
        email_coorprojet = :email_coorprojet, 
        theme_projet = :theme_projet, 
        date_projet = :date_projet, 
        site_projet = :site_projet
    WHERE code_projet = :code_projet";

    // Préparation des paramètres
    $params = [
        'projet'           => $projet,
        'fin'              => $fin,
        'pays_projet'      => $pays_projet,
        'email_projet'     => $email_projet,
        'adress_projet'    => $adress_projet,
        'email_coorprojet' => $email_coorprojet,
        'theme_projet'     => $theme_projet,
        'date_projet'      => $date_projet,
        'site_projet'      => $site_projet,
        'code_projet'      => $code_projet
    ];

    // Exécution de la requête de mise à jour
    $result = $db->update($updateQuery, $params);

    if ($result > 0) {
        $_SESSION['success'] = "Projet mis à jour avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour.";
    }

    // Redirection vers la page de gestion des projets
    header("Location: g_projet.php");
    exit();
}
?>
