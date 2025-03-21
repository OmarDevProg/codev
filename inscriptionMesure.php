
<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit; // Stop the script if the user is not logged in
}
?>
<?php
require 'connect.php';

$db = new Dbf();

$conditions = [];
$params = [];

// Filter by year
if (!empty($_GET['year'])) {
    $conditions[] = "YEAR(date_inscription) = :year";
    $params[':year'] = $_GET['year'];
}

// Filter by month
if (!empty($_GET['month'])) {
    $conditions[] = "MONTH(date_inscription) = :month";
    $params[':month'] = $_GET['month'];
}

// Filter by exact date
if (!empty($_GET['date'])) {
    $conditions[] = "DATE(date_inscription) = :date";
    $params[':date'] = $_GET['date'];
}

// Construct the query with filters
$query = "SELECT * FROM `eform-inscription-formation-mesure` ";

// Add conditions if any filters are applied
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Fetch results
$inscriptions = $db->select($query, $params);
?>


<?php

include 'navbar.php';

?>


<main class="main-content position-relative border-radius-lg ">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success position-fixed top-0 end-50 m-3 fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger position-fixed top-0 end-50 m-3 fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <script>
        setTimeout(() => {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => alert.classList.add('fade'));
            setTimeout(() => alerts.forEach(alert => alert.remove()), 500);
        }, 3000);
    </script>

    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-white active" aria-current="page">Inscription sur mesure</li>
                </ol>
                <h6 class="font-weight-bolder text-white mb-0">Gestion des mesures</h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    <div class="input-group">
                        <input type="hidden" class="form-control" placeholder="Type here...">
                    </div>
                </div>
                <ul class="navbar-nav  justify-content-end">
                    <li class="nav-item d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-white font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none">Administrateur</span>
                        </a>
                    </li>
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line bg-white"></i>
                                <i class="sidenav-toggler-line bg-white"></i>
                                <i class="sidenav-toggler-line bg-white"></i>
                            </div>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>





    <div class="container-fluid py-4">
        <main class="table ms-auto" id="customers_table">
            <section class="table__header">
                <div class="d-flex justify-content-md-end">
                    <div class="text-center mt-4"> <!-- 'mt-3' adds top margin -->
                        <button class="btn btn-light btn-sm" onclick="showAddForm()">
                            <i class="fas fa-plus me-2"></i> Ajouter une inscription sur mesure
                        </button>
                    </div>
                    <div class="export__file mt-4">
                        <label for="export-file" class="export__file-btn" title="Export File"> </label>
                        <input type="checkbox" id="export-file">
                        <div class="export__file-options">
                            <label>Exporter en &nbsp; &#10140;</label>
                            <label for="export-file" id="toPDF">PDF <img src="image/pdf.png" alt=""></label>
                            <label for="export-file" id="toJSON">JSON <img src="image/json.png" alt=""></label>
                            <label for="export-file" id="toCSV">CSV <img src="image/csv.png" alt=""></label>
                            <label for="export-file" id="toEXCEL">EXCEL <img src="image/excel.png" alt=""></label>
                        </div>
                    </div>

                    <div style="margin-left: 50px">
                        <label for="yearFilter" class="text-xs font-weight-bold mb-0 text-uppercase text-secondary opacity-7">Filtrer par Année</label>
                        <select id="yearFilter" class="form-select form-select-sm" onchange="filterData()">
                            <option value="">Année</option>
                            <?php
                            // Dynamically list available years
                            $years = array_merge(range(1999, 2025), [2019, 2020, 2021, 2022, 2023, 2024]);
                            foreach ($years as $year) {
                                // Check if this year is selected
                                $selected = ($_GET['year'] == $year) ? 'selected' : '';
                                echo "<option value=\"$year\" $selected>$year</option>";
                            }
                            ?>
                        </select>
                    </div>

                    &nbsp;
                    <div>
                        <label for="monthFilter" class="text-xs font-weight-bold mb-0 text-uppercase text-secondary opacity-7">Filtrer par Mois</label>
                        <select id="monthFilter" class="form-select form-select-sm" onchange="filterData()">
                            <option value="">Mois</option>
                            <?php
                            // Dynamically list available months
                            $months = [
                                '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
                                '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
                                '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
                                '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
                            ];
                            foreach ($months as $month => $monthName) {
                                $selected = ($_GET['month'] == $month) ? 'selected' : '';
                                echo "<option value=\"$month\" $selected>$monthName</option>";
                            }
                            ?>
                        </select>
                    </div>
                    &nbsp;
                    <div>
                        <label for="dateFilter" class="text-xs font-weight-bold mb-1 text-uppercase text-secondary">Filtrer par Date</label>
                        <input type="date" id="dateFilter" class="form-control form-control-sm border-primary">
                    </div>
                    &nbsp;
                    <!-- Filter Button -->
                    <div>
                        <button class="btn btn-primary btn-sm mt-4" onclick="filterData()">
                            <i class="fas fa-filter text-dark"></i> Filtrer
                        </button>
                    </div>
                </div>

                <!-- Dropdown Menu for Columns -->
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div class="search-container ms-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher..." style="width: 250px;">
                    </div>
                    <div class="dropdown mt-4">
                        <button type="button" class="btn btn-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-eye" style="font-size: 1.6rem; color: darkslategray"></i> <!-- Icon only -->
                        </button>

                        <ul class="dropdown-menu p-3" style="background-color: white; min-width: 100px;">
                            <li><label><input type="checkbox" class="column-toggle" data-col="1" checked> Titre</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="2" checked> Nom</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="3" checked> Prénom</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="4" checked> Email</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="5" checked> Téléphone</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="6" checked> Poste</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="7" checked> Organisme</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="8" checked> Pays</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="9" checked> Source</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="10" checked> Formation Proposition</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="11" checked> Date Proposition</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="12" checked> Nombre de Personnes</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="13" checked> Lieu Proposition</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="14" checked> Source Financement</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="15" checked> Commentaire</label></li>
                        </ul>
                    </div>
                </div>

            </section>

    <link rel="stylesheet" type="text/css" href="style.css">


            <section class="table__body">
                <table id="customers_table">
                    <thead>
                    <tr>
                        <th>Titre <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Nom <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Prénom <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Email <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Téléphone <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Poste <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Organisme <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Pays <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Source <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Formation Proposition <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Date Proposition <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Nombre de Personnes <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Lieu Proposition <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Source Financement <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Commentaire <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Date d'Inscription <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Actions <span class="icon-arrow">&UpArrow;</span></th>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    <?php foreach ($inscriptions as $inscription): ?>
                        <tr>
                            <td><?= htmlspecialchars($inscription['titre']); ?></td>
                            <td><?= htmlspecialchars($inscription['nom']); ?></td>
                            <td><?= htmlspecialchars($inscription['prenom']); ?></td>
                            <td><?= htmlspecialchars($inscription['email']); ?></td>
                            <td><?= htmlspecialchars($inscription['tel']); ?></td>
                            <td><?= htmlspecialchars($inscription['post']); ?></td>
                            <td><?= htmlspecialchars($inscription['organisme']); ?></td>
                            <td><?= htmlspecialchars($inscription['pays']); ?></td>
                            <td><?= htmlspecialchars($inscription['source']); ?></td>
                            <td><?= htmlspecialchars($inscription['formation_proposition']); ?></td>
                            <td><?= htmlspecialchars($inscription['date_proposition']); ?></td>
                            <td><?= htmlspecialchars($inscription['nbr_personne']); ?></td>
                            <td><?= htmlspecialchars($inscription['lieu_proposition']); ?></td>
                            <td><?= htmlspecialchars($inscription['source_financement']); ?></td>
                            <td class="text-start text-wrap" style="max-width: 200px;">
                                <?= nl2br(htmlspecialchars($inscription['commentaire'])); ?>
                            </td>
                            <td><?= htmlspecialchars($inscription['date_inscription']); ?></td>
                            <td>
                                <!-- View Action -->
                                <a href="javascript:void(0);" data-toggle="tooltip" title="Read"
                                   onclick='showDetails(<?php echo json_encode($inscription, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                    <i class="fas fa-eye" style="transition: transform 0.3s ease, color 0.3s ease; color: #007bff;"
                                       onmouseover="this.style.transform='scale(1.2)'; this.style.color='#1e90ff';"
                                       onmouseout="this.style.transform='scale(1)'; this.style.color='#007bff';"></i>
                                </a>
                                &nbsp;
                                <!-- Edit Action -->
                                <a href="javascript:void(0);" onclick="showEditForm(this)"
                                   data-id="<?php echo htmlspecialchars($inscription['id']); ?>"
                                   data-titre="<?php echo htmlspecialchars($inscription['titre']); ?>"
                                   data-nom="<?php echo htmlspecialchars($inscription['nom']); ?>"
                                   data-prenom="<?php echo htmlspecialchars($inscription['prenom']); ?>"
                                   data-email="<?php echo htmlspecialchars($inscription['email']); ?>"
                                   data-tel="<?php echo htmlspecialchars($inscription['tel']); ?>"
                                   data-post="<?php echo htmlspecialchars($inscription['post']); ?>"
                                   data-organisme="<?php echo htmlspecialchars($inscription['organisme']); ?>"
                                   data-pays="<?php echo htmlspecialchars($inscription['pays']); ?>"
                                   data-source="<?php echo htmlspecialchars($inscription['source']); ?>"
                                   data-formation-proposition="<?php echo htmlspecialchars($inscription['formation_proposition']); ?>"
                                   data-date-proposition="<?php echo htmlspecialchars($inscription['date_proposition']); ?>"
                                   data-nbr-personne="<?php echo htmlspecialchars($inscription['nbr_personne']); ?>"
                                   data-lieu-proposition="<?php echo htmlspecialchars($inscription['lieu_proposition']); ?>"
                                   data-source-financement="<?php echo htmlspecialchars($inscription['source_financement']); ?>"
                                   data-commentaire="<?php echo htmlspecialchars($inscription['commentaire']); ?>"
                                   data-date-inscription="<?= date("Y-m-d", strtotime($inscription['date_inscription'])); ?>"
                                   data-toggle="tooltip" title="Edit">
                                    <i class="fas fa-pencil-alt" style="color: #ffc107;"></i>
                                </a>
                                &nbsp;
                                <!-- Delete Action -->
                                <a href="javascript:void(0);" data-toggle="tooltip" title="Delete"
                                   onclick='deleteInscription(<?php echo $inscription['id']; ?>)'>
                                    <i class="fas fa-trash" style="color: #dc3545;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <div class="pagination">
                <button id="prevPage" class="pagination-btn">Précédent</button>
                <span id="pageInfo"></span>
                <button id="nextPage" class="pagination-btn">Suivant</button>
            </div>

        </main>


    <div id="detailsCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
        <div class="card-header bg-info text-white p-4 rounded-top" style="border-bottom: 1px solid #e0e0e0;">
            <!-- Card Header: Title of the Registration Details -->
            <h5 class="mb-0"><i class="fas fa-user me-2"></i> Détails de l'inscription</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <!-- Left Column: Personal Information -->
                <div class="col-md-6">
                    <!-- Title -->
                    <div class="mb-3">
                        <p><strong>Titre:</strong> <span id="detailTitre" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Last Name -->
                    <div class="mb-3">
                        <p><strong>Nom:</strong> <span id="detailNom" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- First Name -->
                    <div class="mb-3">
                        <p><strong>Prénom:</strong> <span id="detailPrenom" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Email Address -->
                    <div class="mb-3">
                        <p><strong>Email:</strong> <span id="detailEmail" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Phone Number -->
                    <div class="mb-3">
                        <p><strong>Téléphone:</strong> <span id="detailTel" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Job Title -->
                    <div class="mb-3">
                        <p><strong>Poste:</strong> <span id="detailPost" class="fw-normal text-muted"></span></p>
                    </div>
                </div>

                <!-- Right Column: Organizational and Proposal Information -->
                <div class="col-md-6">
                    <!-- Organization -->
                    <div class="mb-3">
                        <p><strong>Organisme:</strong> <span id="detailOrganisme" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Country -->
                    <div class="mb-3">
                        <p><strong>Pays:</strong> <span id="detailPays" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Source of Registration -->
                    <div class="mb-3">
                        <p><strong>Source:</strong> <span id="detailSource" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Training Proposal -->
                    <div class="mb-3">
                        <p><strong>Formation Proposition:</strong> <span id="detailFormationProposition" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Date of Proposal -->
                    <div class="mb-3">
                        <p><strong>Date Proposition:</strong> <span id="detailDateProposition" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Number of Participants -->
                    <div class="mb-3">
                        <p><strong>Nombre de Personnes:</strong> <span id="detailNbrPersonne" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Location of Proposal -->
                    <div class="mb-3">
                        <p><strong>Lieu Proposition:</strong> <span id="detailLieuProposition" class="fw-normal text-muted"></span></p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Left Column: Financing Information and Comments -->
                <div class="col-md-6">
                    <!-- Source of Funding -->
                    <div class="mb-3">
                        <p><strong>Source Financement:</strong> <span id="detailSourceFinancement" class="fw-normal text-muted"></span></p>
                    </div>
                    <!-- Additional Comments -->
                    <div class="mb-3">
                        <p><strong>Commentaire:</strong> <span id="detailCommentaire" class="fw-normal text-muted"></span></p>
                    </div>
                </div>

                <!-- Right Column: Registration Date -->
                <div class="col-md-6">
                    <!-- Registration Date -->
                    <div class="mb-3">
                        <p><strong>Date d'Inscription:</strong> <span id="detailDateInscription" class="fw-normal text-muted"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Footer with Close Button -->
        <div class="card-footer bg-light p-3 text-center rounded-bottom">
            <button class="btn btn-secondary w-50" onclick="hideDetails()">
                <i class="fas fa-times me-2"></i> Fermer
            </button>
        </div>
    </div>

<style>
    #detailsCard {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

</style>


    <div id="editCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
                     style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
                    <div class="card-header bg-gradient-success text-white p-3">
                        <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Modifier</h6>
                    </div>

                    <div class="card-body p-3">
                        <form id="editForm" method="post" action="updateInscrireMesure.php">
                            <input type="hidden" name="id" id="editId">

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="editTitre">Titre<span class="text-danger"> *</span></label>
                                    <input type="text" id="editTitre" name="titre" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editNom">Nom<span class="text-danger"> *</span></label>
                                    <input type="text" id="editNom" name="nom" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editPrenom">Prénom<span class="text-danger"> *</span></label>
                                    <input type="text" id="editPrenom" name="prenom" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editEmail">Email<span class="text-danger"> *</span></label>
                                    <input type="email" id="editEmail" name="email" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editTel">Téléphone<span class="text-danger"> *</span></label>
                                    <input type="text" id="editTel" name="tel" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editPost">Post<span class="text-danger"> *</span></label>
                                    <input type="text" id="editPost" name="post" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editOrganisme">Organisme<span class="text-danger"> *</span></label>
                                    <input type="text" id="editOrganisme" name="organisme" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editPays">Pays<span class="text-danger"> *</span></label>
                                    <input type="text" id="editPays" name="pays" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editSource">Source<span class="text-danger"> *</span></label>
                                    <input type="text" id="editSource" name="source" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editFormationProposition">Formation Proposition<span class="text-danger"> *</span></label>
                                    <input type="text" id="editFormationProposition" name="formation_proposition" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editDateProposition">Date Proposition<span class="text-danger"> *</span></label>
                                    <input type="date" id="editDateProposition" name="date_proposition" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editNbrPersonne">Nombre de Personnes<span class="text-danger"> *</span></label>
                                    <input type="number" id="editNbrPersonne" name="nbr_personne" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editLieuProposition">Lieu Proposition<span class="text-danger"> *</span></label>
                                    <input type="text" id="editLieuProposition" name="lieu_proposition" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editSourceFinancement">Source Financement<span class="text-danger"> *</span></label>
                                    <input type="text" id="editSourceFinancement" name="source_financement" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editCommentaire">Commentaire</label>
                                    <textarea id="editCommentaire" name="commentaire" class="form-control"></textarea>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="editDateInscription">Date Inscription<span class="text-danger"> *</span></label>
                                    <input type="date" id="editDateInscription" name="date_inscription" class="form-control">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Update Participant</button>
                            <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideEditForm()"><i class="fas fa-times me-2"></i> Cancel</button>
                        </form>
                    </div>
                </div>


                <div id="addParticipantCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
                     style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
                    <div class="card-header bg-gradient-success text-white p-3">
                        <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i> Ajouter</h6>
                    </div>

                    <div class="card-body p-3">
                        <form id="addParticipantForm" method="post" action="addInscriptionMesure.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="addId">

                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label for="addTitre">Titre<span class="text-danger"> *</span></label>
                                    <input type="text" id="addTitre" name="titre" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addNom">Nom<span class="text-danger"> *</span></label>
                                    <input type="text" id="addNom" name="nom" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addPrenom">Prénom<span class="text-danger"> *</span></label>
                                    <input type="text" id="addPrenom" name="prenom" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addEmail">Email<span class="text-danger"> *</span></label>
                                    <input type="email" id="addEmail" name="email" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addTel">Téléphone<span class="text-danger"> *</span></label>
                                    <input type="text" id="addTel" name="tel" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addPost">Poste<span class="text-danger"> *</span></label>
                                    <input type="text" id="addPost" name="post" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addOrganisme">Organisme<span class="text-danger"> *</span></label>
                                    <input type="text" id="addOrganisme" name="organisme" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addPays">Pays<span class="text-danger"> *</span></label>
                                    <input type="text" id="addPays" name="pays" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addSource">Source<span class="text-danger"> *</span></label>
                                    <input type="text" id="addSource" name="source" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addFormationProposition">Formation Proposition<span class="text-danger"> *</span></label>
                                    <input type="text" id="addFormationProposition" name="formation_proposition" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addDateProposition">Date Proposition<span class="text-danger"> *</span></label>
                                    <input type="date" id="addDateProposition" name="date_proposition" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addNbrPersonne">Nombre de Personnes<span class="text-danger"> *</span></label>
                                    <input type="number" id="addNbrPersonne" name="nbr_personne" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addLieuProposition">Lieu de Proposition<span class="text-danger"> *</span></label>
                                    <input type="text" id="addLieuProposition" name="lieu_proposition" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addSourceFinancement">Source de Financement<span class="text-danger"> *</span></label>
                                    <input type="text" id="addSourceFinancement" name="source_financement" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addCommentaire">Commentaire</label>
                                    <textarea id="addCommentaire" name="commentaire" class="form-control"></textarea>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <label for="addDateInscription">Date Inscription<span class="text-danger"> *</span></label>
                                    <input type="date" id="addDateInscription" name="date_inscription" class="form-control" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Ajouter un Participant</button>
                            <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideAddForm()"><i class="fas fa-times me-2"></i> Cancel</button>
                        </form>
                    </div>
                </div>



                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


                <script>
                    function showDetails(data, id) {
                        console.log("Data:", data);
                        console.log("ID:", id);

                        document.getElementById("detailTitre").innerText = data.titre;
                        document.getElementById("detailNom").innerText = data.nom;
                        document.getElementById("detailPrenom").innerText = data.prenom;
                        document.getElementById("detailEmail").innerText = data.email;
                        document.getElementById("detailTel").innerText = data.tel;
                        document.getElementById("detailPost").innerText = data.post;
                        document.getElementById("detailOrganisme").innerText = data.organisme;
                        document.getElementById("detailPays").innerText = data.pays;
                        document.getElementById("detailSource").innerText = data.source;
                        document.getElementById("detailFormationProposition").innerText = data.formation_proposition;
                        document.getElementById("detailDateProposition").innerText = data.date_proposition;
                        document.getElementById("detailNbrPersonne").innerText = data.nbr_personne;
                        document.getElementById("detailLieuProposition").innerText = data.lieu_proposition;
                        document.getElementById("detailSourceFinancement").innerText = data.source_financement;
                        document.getElementById("detailCommentaire").innerText = data.commentaire;
                        document.getElementById("detailDateInscription").innerText = data.date_inscription;

                        // Handling status display


                        // Setting the image

                        // Show details card
                        document.getElementById("detailsCard").style.display = "block";

                        console.log("Sending AJAX request to update status");
                        var xhr = new XMLHttpRequest();
                        xhr.open("GET", "updateStatus.php?id=" + id, true);
                        xhr.onload = function () {
                            console.log("AJAX request completed");
                            if (xhr.status === 200) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.status === 'success') {
                                        console.log("Status updated to read");
                                        statusElement.innerText = "Read";
                                        statusElement.classList.remove('bg-warning');
                                        statusElement.classList.add('bg-success');
                                    } else {
                                        console.error('Failed to update status:', response.message);
                                    }
                                } catch (e) {
                                    console.error('Failed to parse response as JSON:', e);
                                }
                            }
                        };
                        xhr.send();
                    }

                    function hideDetails() {
                        document.getElementById("detailsCard").style.display = "none";
                    }
                </script>


                <script>
                    let currentParticipantId = null;

                    function showEditForm(button) {
                        // Retrieve data from the button's data attributes
                        const id = button.getAttribute('data-id');
                        const titre = button.getAttribute('data-titre');
                        const nom = button.getAttribute('data-nom');
                        const prenom = button.getAttribute('data-prenom');
                        const email = button.getAttribute('data-email');
                        const tel = button.getAttribute('data-tel');
                        const post = button.getAttribute('data-post');
                        const organisme = button.getAttribute('data-organisme');
                        const pays = button.getAttribute('data-pays');
                        const source = button.getAttribute('data-source');
                        const formationProposition = button.getAttribute('data-formation-proposition');
                        const dateProposition = button.getAttribute('data-date-proposition');
                        const nbrPersonne = button.getAttribute('data-nbr-personne');
                        const lieuProposition = button.getAttribute('data-lieu-proposition');
                        const sourceFinancement = button.getAttribute('data-source-financement');
                        const commentaire = button.getAttribute('data-commentaire');
                        const dateInscription = button.getAttribute('data-date-inscription');

                        // Populate the form fields with the existing participant details
                        document.getElementById('editId').value = id;
                        document.getElementById('editTitre').value = titre;
                        document.getElementById('editNom').value = nom;
                        document.getElementById('editPrenom').value = prenom;
                        document.getElementById('editEmail').value = email;
                        document.getElementById('editTel').value = tel;
                        document.getElementById('editPost').value = post;
                        document.getElementById('editOrganisme').value = organisme;
                        document.getElementById('editPays').value = pays;
                        document.getElementById('editSource').value = source;
                        document.getElementById('editFormationProposition').value = formationProposition;
                        document.getElementById('editDateProposition').value = dateProposition;
                        document.getElementById('editNbrPersonne').value = nbrPersonne;
                        document.getElementById('editLieuProposition').value = lieuProposition;
                        document.getElementById('editSourceFinancement').value = sourceFinancement;
                        document.getElementById('editCommentaire').value = commentaire;
                        document.getElementById('editDateInscription').value = dateInscription;

                        // Show the edit card
                        document.getElementById('editForm').action = 'updateInscrireMesure.php'; // Adjust the action if needed
                        document.getElementById('editCard').style.display = 'block';
                    }

                    function hideEditForm() {
                        // Hide the edit card
                        document.getElementById('editCard').style.display = 'none';
                    }

                    function showAddForm() {
                        document.getElementById('addParticipantCard').style.display = 'block';
                    }

                    // Hide the form
                    function hideAddForm() {
                        document.getElementById('addParticipantCard').style.display = 'none';
                    }
                </script>



                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

        <script>
            document.querySelectorAll('.column-toggle').forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    let colIndex = this.getAttribute('data-col');
                    let isChecked = this.checked;

                    document.querySelectorAll('table tr').forEach(row => {
                        let cell = row.children[colIndex - 1];
                        if (cell) cell.style.display = isChecked ? '' : 'none';
                    });
                });
            });
        </script>

        <script>
            const rowsPerPage = 7;  // Adjust the number of rows per page
            let currentPage = 1;
            const tableBody = document.querySelector('#table-body');
            const rows = tableBody.querySelectorAll('tr');
            let totalRows = rows.length;
            let totalPages = Math.ceil(totalRows / rowsPerPage);

            const prevPageBtn = document.querySelector('#prevPage');
            const nextPageBtn = document.querySelector('#nextPage');
            const pageInfo = document.querySelector('#pageInfo');
            const searchInput = document.querySelector('#searchInput');

            // Function to display the rows for the current page
            function displayTableRows() {
                const filteredRows = tableBody.querySelectorAll('tr:not(.hide)');
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                filteredRows.forEach((row, index) => {
                    if (index >= start && index < end) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });

                pageInfo.textContent = `Page ${currentPage} sur ${totalPages}`;
                prevPageBtn.disabled = currentPage === 1;
                nextPageBtn.disabled = currentPage === totalPages;
            }

            // Function to go to the next page
            nextPageBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    displayTableRows();
                }
            });

            // Function to go to the previous page
            prevPageBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayTableRows();
                }
            });

            // Function to search the table
            function searchTable() {
                const searchValue = searchInput.value.toLowerCase();

                // Reset the hide class from all rows
                rows.forEach((row) => {
                    const rowData = row.textContent.toLowerCase();
                    if (rowData.indexOf(searchValue) === -1) {
                        row.classList.add('hide');  // Hide rows that don't match the search term
                    } else {
                        row.classList.remove('hide');  // Show rows that match
                    }
                });

                // Recalculate total pages based on the visible rows
                const filteredRows = tableBody.querySelectorAll('tr:not(.hide)');
                totalRows = filteredRows.length;
                totalPages = Math.ceil(totalRows / rowsPerPage);
                currentPage = 1;  // Reset to the first page after a search

                // Display the rows on the first page based on the filtered results
                displayTableRows();
            }

            // Add event listener to the search input
            searchInput.addEventListener('input', searchTable);

            // Initial display of table rows
            displayTableRows();

        </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteInscription(participantId) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Voulez-vous vraiment supprimer ce participant ? Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make an AJAX request to delete the participant
                    fetch('deleteInscrireMesure.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: participantId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Supprimé!', data.message, 'success').then(() => {
                                    // Reload the page or remove the participant row from the table
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Erreur!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Erreur!', 'Une erreur s\'est produite lors de la suppression.', 'error');
                        });
                }
            });
        }
    </script>







    <script>

        // JavaScript function to filter participants by year
        function filterData() {
            let year = document.getElementById('yearFilter').value;
            let month = document.getElementById('monthFilter').value;
            let date = document.getElementById('dateFilter').value;

            let url = "inscriptionMesure.php?";
            let params = [];

            if (year) params.push("year=" + year);
            if (month) params.push("month=" + month);
            if (date) params.push("date=" + date);

            window.location.href = url + params.join("&");
        }
    </script>


    <?php
    include "footer.php";

    ?>
        <script src="script.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
