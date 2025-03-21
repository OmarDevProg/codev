

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
    $conditions[] = "YEAR(date_debut_session) = :year";
    $params[':year'] = $_GET['year'];
}

// Filter by month
if (!empty($_GET['month'])) {
    $conditions[] = "MONTH(date_debut_session) = :month";
    $params[':month'] = $_GET['month'];
}

// Filter by exact date
if (!empty($_GET['date'])) {
    $conditions[] = "DATE(date_debut_session) = :date";
    $params[':date'] = $_GET['date'];
}

// Construct the query with filters
$query = "SELECT * FROM ci_participants";
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Fetch results
$participants = $db->select($query, $params);
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
                    <li class="breadcrumb-item text-sm text-white active" aria-current="page">Gestion des participants</li>
                </ol>
                <h6 class="font-weight-bolder text-white mb-0">Les participants</h6>
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


            <div class="d-flex justify-content-md-end" >
                <div class="text-center mt-4"> <!-- 'mt-3' adds top margin -->
                    <button class="btn btn-light btn-sm" onclick="showAddForm()">
                        <i class="fas fa-plus me-2"></i> ajouter un participant
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

                <div style="margin-left: 50px" >

                    <label for="yearFilter" class="text-xs font-weight-bold mb-0 text-uppercase text-secondary opacity-7">klpokokppk</label>
                    <select id="yearFilter" class="form-select form-select-sm" onchange="filterData()">
                        <option value=""> année</option>
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
                    <label for="monthFilter" class="text-xs font-weight-bold mb-0 text-uppercase text-secondary opacity-7">.</label>
                    <select id="monthFilter" class="form-select form-select-sm" onchange="filterData()">
                        <option value=""> mois</option>
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
                    <label for="dateFilter" class="text-xs font-weight-bold mb-1 text-uppercase text-secondary"></label>
                    <input type="date" id="dateFilter" class="form-control form-control-sm border-primary">
                </div>
                &nbsp;
                <!-- Filter Button -->
                <div>
                    <button class="btn btn-primary btn-sm mt-4 " onclick="filterData()">
                        <i class="fas fa-filter text-dark"></i> Filtrer
                    </button>
                </div>


                <!-- Export File Section -->

            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <!-- Dropdown Menu -->
                <div class="search-container ms-3">

                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher..." style="width: 250px;">
                </div>
                <div class="dropdown mt-4">
                    <button type="button" class="btn btn-transparent dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-eye" style="font-size: 1.6rem; color: darkslategray"></i> <!-- Icon only -->
                    </button>

                    <ul class="dropdown-menu p-3" style="background-color: white; min-width: 100px;">
                        <li><label><input type="checkbox" class="column-toggle" data-col="0" checked> Photo</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="1" checked> Nom</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="2" checked> Pays</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="3" checked> Date de Naissance</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="4" checked> Email</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="5" checked> Téléphone</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="6" checked> Fonction</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="7" checked> Date Début Session</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="8" checked> Date Fin Session</label></li>
                        <li><label><input type="checkbox" class="column-toggle" data-col="9" checked> Actions</label></li>
                    </ul>
                </div>



                <!-- Search Input -->

            </div>



        </section>
            <link rel="stylesheet" type="text/css" href="style.css">



            <section class="table__body">
                <table id="participants_table">
                    <thead>
                    <tr>
                        <th>Photo <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Nom <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Pays <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Date de Naissance <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Email <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Téléphone <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Fonction <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Date Début Session <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Date Fin Session <span class="icon-arrow">&UpArrow;</span></th>
                        <th>Actions <span class="icon-arrow">&UpArrow;</span></th>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    <?php foreach ($participants as $participant): ?>
                        <tr>
                            <td>
                                <img style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto;"
                                     src="./image/img_data-base/<?php echo $participant['id']; ?>.jpg" alt="Photo">
                            </td>
                            <td><?= htmlspecialchars($participant['nom']); ?></td>
                            <td><?= htmlspecialchars($participant['pays']); ?></td>
                            <td><?= htmlspecialchars($participant['date_naissance']); ?></td>
                            <td><?= htmlspecialchars($participant['email']); ?></td>
                            <td><?= htmlspecialchars($participant['tel']); ?></td>
                            <td><?= htmlspecialchars($participant['fonction']); ?></td>
                            <td><?= htmlspecialchars($participant['date_debut_session']); ?></td>
                            <td><?= htmlspecialchars($participant['date_fin_session']); ?></td>
                            <td>
                                <!-- View Action -->
                                <a href="javascript:void(0);" data-toggle="tooltip" title="Read"
                                   onclick='showDetails(<?php echo json_encode($participant, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo $participant['id']; ?>)'>
                                    <i class="fas fa-eye" style="color: #007bff;"></i>
                                </a>
                                &nbsp;
                                <!-- Edit Action -->
                                <a href="javascript:void(0);" onclick="showEditForm(this)"
                                   data-id="<?php echo htmlspecialchars($participant['id']); ?>"
                                   data-nom="<?php echo htmlspecialchars($participant['nom']); ?>"
                                   data-email="<?php echo htmlspecialchars($participant['email']); ?>"
                                   data-tel="<?php echo htmlspecialchars($participant['tel']); ?>"
                                   data-fonction="<?php echo htmlspecialchars($participant['fonction']); ?>"
                                   data-toggle="tooltip" title="Edit">
                                    <i class="fas fa-pencil-alt" style="color: #ffc107;"></i>
                                </a>
                                &nbsp;
                                <!-- Delete Action -->
                                <a href="javascript:void(0);" data-toggle="tooltip" title="Delete"
                                   onclick='deleteParticipant(<?php echo $participant['id']; ?>)'>
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

            <div id="detailsCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-info text-white p-3">
            <h6 class="mb-0"><i class="fas fa-user me-2"></i> Détails du participant </h6>
        </div>
        <div class="card-body p-3 d-flex">
            <!-- Text details section -->
            <div class="me-3 flex-grow-1">
                <p><strong>Nom:</strong> <span id="detailNom"></span></p>
                <p><strong>Organisation:</strong> <span id="detailOrg"></span></p>
                <p><strong>Lieu de Formation:</strong> <span id="detailLieuFormation"></span></p>
                <p><strong>Pays:</strong> <span id="detailPays"></span></p>
                <p><strong>Titre de Formation:</strong> <span id="detailTitreFormation"></span></p>
                <p><strong>Date de Début:</strong> <span id="detailDateDebutSession"></span></p>
                <p><strong>Date de Fin:</strong> <span id="detailDateFinSession"></span></p>
                <p><strong>Type de Formation:</strong> <span id="detailTypeFormation"></span></p>
                <p><strong>Date de Naissance:</strong> <span id="detailDateNaissance"></span></p>
                <p><strong>Email:</strong> <span id="detailEmail"></span></p>
                <p><strong>Téléphone:</strong> <span id="detailTel"></span></p>
                <p><strong>Adresse:</strong> <span id="detailAdresse"></span></p>
                <p><strong>Projet:</strong> <span id="detailProjet"></span></p>
                <p><strong>Fax:</strong> <span id="detailFax"></span></p>

                <p><strong>Fonction:</strong> <span id="detailFonction"></span></p>
                <p><strong>Status:</strong>
                    <span id="detailStatus" class="badge rounded-pill text-uppercase fw-bold"></span>
                </p>
            </div>

            <!-- Photo section -->
            <div>
                <img style="width: 410px; height: auto; object-fit: cover; border-radius: 20px;"
                     src="./image/img_data-base/<?php echo $id; ?>.jpg"
                     alt="Photo">

            </div>
        </div>
        <button class="btn btn-secondary w-100 mt-2" onclick="hideDetails()">
            <i class="fas fa-times me-2"></i> Fermer
        </button>
    </div>

    <div id="editCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-success text-white p-3">
            <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Edit Participant</h6>
        </div>

        <div class="card-body p-3">
            <form id="editForm" method="post" action="updateParticipant.php">
                <input type="hidden" name="id" id="editId">

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="editNom">Nom<span class="text-danger"> *</span></label>
                        <input type="text" id="editNom" name="nom" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editOrg">Organisation<span class="text-danger"> *</span></label>
                        <input type="text" id="editOrg" name="org" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editLieuFormation">Lieu de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="editLieuFormation" name="lieu_formation" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editPays">Pays<span class="text-danger"> *</span></label>
                        <input type="text" id="editPays" name="pays" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editTitreFormation">Titre de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="editTitreFormation" name="titre_formation" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editDateNaissance">Date de Naissance<span class="text-danger"> *</span></label>
                        <input type="date" id="editDateNaissance" name="date_naissance" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editEmail">Email<span class="text-danger"> *</span></label>
                        <input type="email" id="editEmail" name="email" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editTel">Téléphone<span class="text-danger"> *</span></label>
                        <input type="text" id="editTel" name="tel" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editAdresse">Adresse<span class="text-danger"> *</span></label>
                        <input type="text" id="editAdresse" name="adresse" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editProjet">Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="editProjet" name="projet" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editFax">Fax<span class="text-danger"> *</span></label>
                        <input type="text" id="editFax" name="fax" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editFonction">Fonction<span class="text-danger"> *</span></label>
                        <input type="text" id="editFonction" name="fonction" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editTypeFormation">Type de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="editTypeFormation" name="type_formation" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editDateDebutSession">Date Début Session<span class="text-danger"> *</span></label>
                        <input type="date" id="editDateDebutSession" name="date_debut_session" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editDateFinSession">Date Fin Session<span class="text-danger"> *</span></label>
                        <input type="date" id="editDateFinSession" name="date_fin_session" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editPreinscription">Preinscription<span class="text-danger"> *</span></label>
                        <input type="text" id="editPreinscription" name="preinscription" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editFacture">Facture<span class="text-danger"> *</span></label>
                        <input type="text" id="editFacture" name="facture" class="form-control">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="editPaiement">Paiement<span class="text-danger"> *</span></label>
                        <input type="text" id="editPaiement" name="paiement" class="form-control">
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
            <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i> Ajouter un Participant</h6>
        </div>

        <div class="card-body p-3">
            <form id="addParticipantForm" method="post" action="addParticipant.php" enctype="multipart/form-data">
                <input type="hidden" name="id" id="addId">

                <div class="row">
                    <!-- First column -->
                    <div class="col-md-4 mb-2">
                        <label for="addNom">Nom<span class="text-danger"> *</span></label>
                        <input type="text" id="addNom" name="nom" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addOrg">Organisation<span class="text-danger"> *</span></label>
                        <input type="text" id="addOrg" name="org" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addLieuFormation">Lieu de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="addLieuFormation" name="lieu_formation" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addPays">Pays<span class="text-danger"> *</span></label>
                        <input type="text" id="addPays" name="pays" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addTitreFormation">Titre de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="addTitreFormation" name="titre_formation" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addDateNaissance">Date de Naissance<span class="text-danger"> *</span></label>
                        <input type="date" id="addDateNaissance" name="date_naissance" class="form-control" required>
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
                        <label for="addAdresse">Adresse<span class="text-danger"> *</span></label>
                        <input type="text" id="addAdresse" name="adresse" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addProjet">Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="addProjet" name="projet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addFax">Fax<span class="text-danger"> *</span></label>
                        <input type="text" id="addFax" name="fax" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addFonction">Fonction<span class="text-danger"> *</span></label>
                        <input type="text" id="addFonction" name="fonction" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addTypeFormation">Type de Formation<span class="text-danger"> *</span></label>
                        <input type="text" id="addTypeFormation" name="type_formation" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addDateDebutSession">Date Début Session<span class="text-danger"> *</span></label>
                        <input type="date" id="addDateDebutSession" name="date_debut_session" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addDateFinSession">Date Fin Session<span class="text-danger"> *</span></label>
                        <input type="date" id="addDateFinSession" name="date_fin_session" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addPreinscription">Preinscription<span class="text-danger"> *</span></label>
                        <input type="text" id="addPreinscription" name="preinscription" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addFacture">Facture<span class="text-danger"> *</span></label>
                        <input type="text" id="addFacture" name="facture" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addPaiement">Paiement<span class="text-danger"> *</span></label>
                        <input type="text" id="addPaiement" name="paiement" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addPhoto">Photo<span class="text-danger"> *</span></label>
                        <input type="file" id="addPhoto" name="photo" class="form-control" accept="image/*" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Ajouter un Participant</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideAddForm()"><i class="fas fa-times me-2"></i> Cancel</button>
            </form>
        </div>
    </div>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


    <script>
        function showDetails(data, id) {
            console.log("Data:", data);
            console.log("ID:", id);

            document.getElementById("detailNom").innerText = data.nom;
            document.getElementById("detailOrg").innerText = data.org;
            document.getElementById("detailLieuFormation").innerText = data.lieu_formation;
            document.getElementById("detailPays").innerText = data.pays;
            document.getElementById("detailTitreFormation").innerText = data.titre_formation;
            document.getElementById("detailDateDebutSession").innerText = data.date_debut_session;
            document.getElementById("detailDateFinSession").innerText = data.date_fin_session;
            document.getElementById("detailTypeFormation").innerText = data.type_formation;
            document.getElementById("detailDateNaissance").innerText = data.date_naissance;
            document.getElementById("detailEmail").innerText = data.email;
            document.getElementById("detailTel").innerText = data.tel;
            document.getElementById("detailAdresse").innerText = data.adresse;
            document.getElementById("detailProjet").innerText = data.Projet;
            document.getElementById("detailFax").innerText = data.fax;
            document.getElementById("detailFonction").innerText = data.fonction;

            const statusElement = document.getElementById("detailStatus");
            if (data.lu == 1) {
                statusElement.innerText = "Read";
                statusElement.classList.add('bg-success', 'text-white', 'px-3', 'py-2', 'shadow-sm');
                statusElement.classList.remove('bg-warning');
            } else {
                statusElement.innerText = "Unread";
                statusElement.classList.add('bg-warning', 'text-dark', 'px-3', 'py-2', 'shadow-sm');
                statusElement.classList.remove('bg-success');
            }
            document.querySelector('#detailsCard img').src = './image/img_data-base/' + id + '.jpg';

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
        const nom = button.getAttribute('data-nom');
        const org = button.getAttribute('data-org');
        const lieuFormation = button.getAttribute('data-lieu-formation');
        const pays = button.getAttribute('data-pays');
        const titreFormation = button.getAttribute('data-titre-formation');
        const dateNaissance = button.getAttribute('data-date-naissance');
        const email = button.getAttribute('data-email');
        const tel = button.getAttribute('data-tel');
        const adresse = button.getAttribute('data-adresse');
        const projet = button.getAttribute('data-projet');
        const fax = button.getAttribute('data-fax');
        const fonction = button.getAttribute('data-fonction');
        const typeFormation = button.getAttribute('data-type-formation');
        const dateDebutSession = button.getAttribute('data-date-debut-session');
        const dateFinSession = button.getAttribute('data-date-fin-session');
        const preinscription = button.getAttribute('data-preinscription');
        const facture = button.getAttribute('data-facture');
        const paiement = button.getAttribute('data-paiement');

        // Populate the form fields with the existing participant details
        document.getElementById('editId').value = id;
        document.getElementById('editNom').value = nom;
        document.getElementById('editOrg').value = org;
        document.getElementById('editLieuFormation').value = lieuFormation;
        document.getElementById('editPays').value = pays;
        document.getElementById('editTitreFormation').value = titreFormation;
        document.getElementById('editDateNaissance').value = dateNaissance;
        document.getElementById('editEmail').value = email;
        document.getElementById('editTel').value = tel;
        document.getElementById('editAdresse').value = adresse;
        document.getElementById('editProjet').value = projet;
        document.getElementById('editFax').value = fax;
        document.getElementById('editFonction').value = fonction;
        document.getElementById('editTypeFormation').value = typeFormation;
        document.getElementById('editDateDebutSession').value = dateDebutSession;
        document.getElementById('editDateFinSession').value = dateFinSession;
        document.getElementById('editPreinscription').value = preinscription;
        document.getElementById('editFacture').value = facture;
        document.getElementById('editPaiement').value = paiement;

        // Show the edit card
        document.getElementById('editForm').action = 'updateParticipant.php'; // Adjust the action if needed
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





            <!-- jEditable -->
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteParticipant(participantId) {
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
                    fetch('deleteParticipant.php', {
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

        let url = "g_participants.php?";
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

