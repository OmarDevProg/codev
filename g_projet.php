

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
    $conditions[] = "YEAR(date_projet) = :year";
    $params[':year'] = $_GET['year'];
}

// Filter by month
if (!empty($_GET['month'])) {
    $conditions[] = "MONTH(date_projet) = :month";
    $params[':month'] = $_GET['month'];
}

// Filter by exact date
if (!empty($_GET['date'])) {
    $conditions[] = "DATE(date_projet) = :date";
    $params[':date'] = $_GET['date'];
}

// Construct the query with filters
$query = "SELECT * FROM projet";
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Fetch results
$projets= $db->select($query, $params);
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
                    <li class="breadcrumb-item text-sm text-white active" aria-current="page">Gestion des projets</li>
                </ol>
                <h6 class="font-weight-bolder text-white mb-0">Gestion des projets</h6>
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
                            <i class="fas fa-plus me-2"></i> ajouter une projet
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
                            <li><label><input type="checkbox" class="column-toggle" data-col="1" checked> Code Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="2" checked> Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="3" checked> Fin</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="4" checked> Pays Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="5" checked> Email Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="6" checked> Adresse Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="7" checked> Email Coordonnateur</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="8" checked> Thème Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="9" checked> Date Projet</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="10" checked> Site Projet</label></li>
                        </ul>
                    </div>


                    <!-- Search Input -->

                </div>



            </section>


            <link rel="stylesheet" type="text/css" href="style.css">



                        <section class="table__body">
                            <table id="customers_table">
                                <thead>
                                <tr>
                                    <th>Code Projet <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Projet <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Fin <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Pays Projet <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Email Projet <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Adresse Projet <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Email Coordonnateur <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Thème Projet <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Date Projet <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Site Projet <span class="icon-arrow">&UpArrow;</span></th>
                                    <th>Actions <span class="icon-arrow">&UpArrow;</span></th>
                                </tr>
                                </thead>
                                <tbody id="table-body">
                                <?php foreach ($projets as $projet): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($projet['code_projet']); ?></td>
                                        <td><?= nl2br(htmlspecialchars($projet['projet'])); ?></td>
                                        <td><?= htmlspecialchars($projet['Fin']); ?></td>
                                        <td><?= htmlspecialchars($projet['pays_projet']); ?></td>
                                        <td><?= htmlspecialchars($projet['email_projet']); ?></td>
                                        <td><?= htmlspecialchars($projet['adress_projet']); ?></td>
                                        <td><?= htmlspecialchars($projet['email_coorprojet']); ?></td>
                                        <td><?= htmlspecialchars($projet['theme_projet']); ?></td>
                                        <td><?= htmlspecialchars($projet['date_projet']); ?></td>
                                        <td><?= htmlspecialchars($projet['site_projet']); ?></td>
                                        <td>
                                            <!-- View Action -->
                                            <a href="javascript:void(0);" data-toggle="tooltip" title="Read"
                                               onclick='showDetails(<?php echo json_encode($projet, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo $projet['code_projet']; ?>)'>
                                                <i class="fas fa-eye" style="transition: transform 0.3s ease, color 0.3s ease; color: #007bff;"
                                                   onmouseover="this.style.transform='scale(1.2)'; this.style.color='#1e90ff';"
                                                   onmouseout="this.style.transform='scale(1)'; this.style.color='#007bff';"></i>
                                            </a>
                                            &nbsp;
                                            <!-- Edit Action -->
                                            <a href="javascript:void(0);" onclick="showEditForm(this)"
                                               data-code-projet="<?php echo htmlspecialchars($projet['code_projet']); ?>"
                                               data-projet="<?php echo htmlspecialchars($projet['projet']); ?>"
                                               data-fin="<?php echo htmlspecialchars($projet['Fin']); ?>"
                                               data-pays-projet="<?php echo htmlspecialchars($projet['pays_projet']); ?>"
                                               data-email-projet="<?php echo htmlspecialchars($projet['email_projet']); ?>"
                                               data-adress-projet="<?php echo htmlspecialchars($projet['adress_projet']); ?>"
                                               data-email-coorprojet="<?php echo htmlspecialchars($projet['email_coorprojet']); ?>"
                                               data-theme-projet="<?php echo htmlspecialchars($projet['theme_projet']); ?>"
                                               data-date-projet="<?php echo htmlspecialchars($projet['date_projet']); ?>"
                                               data-site-projet="<?php echo htmlspecialchars($projet['site_projet']); ?>"
                                               data-toggle="tooltip" title="Edit">
                                                <i class="fas fa-pencil-alt" style="color: #ffc107;"></i>
                                            </a>
                                            &nbsp;
                                            <!-- Delete Action -->
                                            <a href="javascript:void(0);" data-toggle="tooltip" title="Delete"
                                               onclick='deleteProjet(<?php echo $projet['code_projet'] ?>)'>
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
         style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-info text-white p-3">
            <h6 class="mb-0"><i class="fas fa-folder-open me-2"></i> Détails du projet </h6>
        </div>
        <div class="card-body p-3 d-flex">
            <!-- Text details section -->
            <div class="me-3 flex-grow-1">
                <p><strong>Code Projet:</strong> <span id="detailCodeProjet"></span></p>
                <p><strong>Nom du Projet:</strong> <span id="detailProjet"></span></p>
                <p><strong>Pays du Projet:</strong> <span id="detailPaysProjet"></span></p>
                <p><strong>Email du Projet:</strong> <span id="detailEmailProjet"></span></p>
                <p><strong>Adresse du Projet:</strong> <span id="detailAdressProjet"></span></p>
                <p><strong>Email Coordinateur:</strong> <span id="detailEmailCoorProjet"></span></p>
                <p><strong>Thème du Projet:</strong> <span id="detailThemeProjet"></span></p>
                <p><strong>Date du Projet:</strong> <span id="detailDateProjet"></span></p>
                <p><strong>Site Web du Projet:</strong> <span id="detailSiteProjet"></span></p>
                <p><strong>Statut:</strong>
                    <span id="detailStatus" class="badge rounded-pill text-uppercase fw-bold"></span>
                </p>
            </div>

            <!-- Image section -->
            <div>
                <img style="width: 410px; height: auto; object-fit: cover; border-radius: 20px;"
                     src="./image/img_projet/<?php echo $id; ?>.jpg"
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
            <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Edit Project</h6>
        </div>

        <div class="card-body p-3">
            <form id="editForm" method="post" action="updateProjet.php">
                <input type="hidden" name="code_projet" id="editCodeProjet">

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <!-- Projet -->
                        <label for="editProjet">Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="editProjet" name="projet" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <!-- Fin -->
                        <label for="editFin">Fin<span class="text-danger"> *</span></label>
                        <input type="text" id="editFin" name="fin" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <!-- Pays Projet -->
                        <label for="editPaysProjet">Pays Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="editPaysProjet" name="pays_projet" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <!-- Email Projet -->
                        <label for="editEmailProjet">Email Projet<span class="text-danger"> *</span></label>
                        <input type="email" id="editEmailProjet" name="email_projet" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <!-- Adresse Projet -->
                        <label for="editAdressProjet">Adresse Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="editAdressProjet" name="adress_projet" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <!-- Email Coordonnateur -->
                        <label for="editEmailCoorProjet">Email Coordonnateur<span class="text-danger"> *</span></label>
                        <input type="email" id="editEmailCoorProjet" name="email_coorprojet" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <!-- Thème Projet -->
                        <label for="editThemeProjet">Thème Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="editThemeProjet" name="theme_projet" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <!-- Date Projet -->
                        <label for="editDateProjet">Date Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="editDateProjet" name="date_projet" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <!-- Site Projet -->
                        <label for="editSiteProjet">Site Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="editSiteProjet" name="site_projet" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Update Project</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideEditForm()">
                    <i class="fas fa-times me-2"></i> Cancel
                </button>
            </form>
        </div>
    </div>


    <div id="addProjectCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-success text-white p-3">
            <h6 class="mb-0"><i class="fas fa-plus me-2"></i> Ajouter un Projet</h6>
        </div>

        <div class="card-body p-3">
            <form id="addProjectForm" method="post" action="addProjet.php" enctype="multipart/form-data">
                <input type="hidden" name="code_projet" id="addCodeProjet">

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="addProjet">Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="addProjet" name="projet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addFin">Fin<span class="text-danger"> *</span></label>
                        <input type="text" id="addFin" name="fin" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addPaysProjet">Pays Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="addPaysProjet" name="pays_projet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addEmailProjet">Email Projet<span class="text-danger"> *</span></label>
                        <input type="email" id="addEmailProjet" name="email_projet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addAdressProjet">Adresse Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="addAdressProjet" name="adress_projet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addEmailCoorProjet">Email Coordonnateur<span class="text-danger"> *</span></label>
                        <input type="email" id="addEmailCoorProjet" name="email_coorprojet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addThemeProjet">Thème Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="addThemeProjet" name="theme_projet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addDateProjet">Date Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="addDateProjet" name="date_projet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addSiteProjet">Site Projet<span class="text-danger"> *</span></label>
                        <input type="text" id="addSiteProjet" name="site_projet" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="addPhoto">Photo<span class="text-danger"> *</span></label>
                        <input type="file" id="addPhoto" name="photo" class="form-control" accept="image/*" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Ajouter un Projet</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideAddForm()">
                    <i class="fas fa-times me-2"></i> Cancel
                </button>
            </form>
        </div>
    </div>



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <script>
        function showDetails(data, code) {
            console.log("Data:", data);
            console.log("Code Projet:", code);

            document.getElementById("detailCodeProjet").innerText = data.code_projet;
            document.getElementById("detailProjet").innerText = data.projet;
            document.getElementById("detailPaysProjet").innerText = data.pays_projet;
            document.getElementById("detailEmailProjet").innerText = data.email_projet;
            document.getElementById("detailAdressProjet").innerText = data.adress_projet;
            document.getElementById("detailEmailCoorProjet").innerText = data.email_coorprojet;
            document.getElementById("detailThemeProjet").innerText = data.theme_projet;
            document.getElementById("detailDateProjet").innerText = data.date_projet;
            document.getElementById("detailSiteProjet").innerText = data.site_projet;

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
            document.querySelector('#detailsCard img').src = './image/img_projet/' + code + '.jpg';

            document.getElementById("detailsCard").style.display = "block";

            console.log("Sending AJAX request to update status");
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "updateStatus.php?code=" + code, true);
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
        let currentProjectCode = null;

        function showEditForm(button) {
            // Récupération des données à partir des attributs data du bouton
            const code = button.getAttribute('data-code-projet');
            const projet = button.getAttribute('data-projet');
            const fin = button.getAttribute('data-fin');
            const paysProjet = button.getAttribute('data-pays-projet');
            const emailProjet = button.getAttribute('data-email-projet');
            const adressProjet = button.getAttribute('data-adress-projet');
            const emailCoorProjet = button.getAttribute('data-email-coorprojet');
            const themeProjet = button.getAttribute('data-theme-projet');
            const dateProjet = button.getAttribute('data-date-projet');
            const siteProjet = button.getAttribute('data-site-projet');

            // Remplissage des champs du formulaire avec les détails existants du projet
            document.getElementById('editCodeProjet').value = code;
            document.getElementById('editProjet').value = projet;
            document.getElementById('editFin').value = fin;
            document.getElementById('editPaysProjet').value = paysProjet;
            document.getElementById('editEmailProjet').value = emailProjet;
            document.getElementById('editAdressProjet').value = adressProjet;
            document.getElementById('editEmailCoorProjet').value = emailCoorProjet;
            document.getElementById('editThemeProjet').value = themeProjet;
            document.getElementById('editDateProjet').value = dateProjet;
            document.getElementById('editSiteProjet').value = siteProjet;

            // Afficher le formulaire d'édition et définir l'action du formulaire
            document.getElementById('editForm').action = 'updateProjet.php';
            document.getElementById('editCard').style.display = 'block';
        }

        function hideEditForm() {
            // Masquer le formulaire d'édition
            document.getElementById('editCard').style.display = 'none';
        }

        function showAddForm() {
            document.getElementById('addProjectCard').style.display = 'block';
        }

        function hideAddForm() {
            document.getElementById('addProjectCard').style.display = 'none';
        }
    </script>

        <script>
            let currentFormateurCode = null;

            // Function to show the edit form with pre-filled data
            function showEditForm(button) {
                // Get data attributes from the button
                const code = button.getAttribute('data-code-formateur');
                const nom = button.getAttribute('data-nom');
                const prenom = button.getAttribute('data-prenom');
                const profession = button.getAttribute('data-profession');
                const email = button.getAttribute('data-email');
                const dateFormation = button.getAttribute('data-date-formation');
                const dateNaissance = button.getAttribute('data-date-naissance');
                const nationalite = button.getAttribute('data-nationalite');
                const cv = button.getAttribute('data-cv');
                const status = button.getAttribute('data-status');

                // Fill the form fields with existing data
                document.getElementById('editCodeFormateur').value = code;
                document.getElementById('editNom').value = nom;
                document.getElementById('editPrenom').value = prenom;
                document.getElementById('editProfession').value = profession;
                document.getElementById('editEmail').value = email;
                document.getElementById('editDateFormation').value = dateFormation;
                document.getElementById('editDateNaissance').value = dateNaissance;
                document.getElementById('editNationalite').value = nationalite;
                document.getElementById('editCv').value = cv;
                document.getElementById('editStatus').value = status;

                // Show the edit form
                document.getElementById('editForm').action = 'updateFormateur.php';
                document.getElementById('editCard').style.display = 'block';
            }

            // Function to hide the edit form
            function hideEditForm() {
                document.getElementById('editCard').style.display = 'none';
            }

            // Function to show the add form
            function showAddForm() {
                document.getElementById('addFormateurCard').style.display = 'block';
            }

            // Function to hide the add form
            function hideAddForm() {
                document.getElementById('addFormateurCard').style.display = 'none';
            }
        </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>



    <!-- DataTable Initialization Script -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteProjet(participantId) {
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
                    fetch('deleteProjet.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ code_projet: participantId })
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

            let url = "g_projet.php?";
            let params = [];

            if (year) params.push("year=" + year);
            if (month) params.push("month=" + month);
            if (date) params.push("date=" + date);

            window.location.href = url + params.join("&");
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


<?php
include "footer.php";

?>

        <script src="script.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
