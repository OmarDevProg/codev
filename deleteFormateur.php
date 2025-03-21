<?php
require 'connect.php'; // Inclusion de la classe Dbf

// Instanciation de la classe Dbf pour l'interaction avec la base de données
$db = new Dbf();

// Récupérer les données JSON de la requête POST
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si le code_projet est fourni
if (isset($data['code_formation'])) {
    $codeProjet = trim($data['code_formation']);

    // Vérifier que le code_projet n'est pas vide
    if (!empty($codeProjet)) {
        // Préparer la requête SQL pour supprimer le projet
        $sql = "DELETE FROM formation WHERE code_formation = ?";

        // Exécuter la requête
        $params = [$codeProjet];
        $result = $db->delete($sql, $params);

        // Vérifier si des lignes ont été affectées
        if ($result > 0) {
            // Si la suppression est réussie, envoyer une réponse de succès
            echo json_encode([
                'status' => 'success',
                'message' => 'Le formateur a été supprimé avec succès.'
            ]);
        } else {
            // Si aucune ligne n'a été affectée, envoyer une réponse d'erreur
            echo json_encode([
                'status' => 'error',
                'message' => 'Aucun formateur trouvé avec cet identifiant.'
            ]);
        }
    } else {
        // Code de projet invalide, envoyer une réponse d'erreur
        echo json_encode([
            'status' => 'error',
            'message' => 'Code de formateur invalide.'
        ]);
    }
} else {
    // Code de projet manquant, envoyer une réponse d'erreur
    echo json_encode([
        'status' => 'error',
        'message' => 'Code de formateur manquant.'
    ]);
}
?>
