
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
    $conditions[] = "YEAR(	
date_submission) = :year";
    $params[':year'] = $_GET['year'];
}

// Filter by month
if (!empty($_GET['month'])) {
    $conditions[] = "MONTH(date_submission) = :month";
    $params[':month'] = $_GET['month'];
}

// Filter by exact date
if (!empty($_GET['date'])) {
    $conditions[] = "DATE(date_submission) = :date";
    $params[':date'] = $_GET['date'];
}


// Query to fetch data from 'eform-inscription-newsletter'


// Construct the query with filters
$query = "SELECT * FROM  `eform-inscription-newsletter`";
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Fetch results
$submissions = $db->select($query,$params);
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
                    <li class="breadcrumb-item text-sm text-white active" aria-current="page">newsletters</li>
                </ol>
                <h6 class="font-weight-bolder text-white mb-0">liste des newsletters</h6>
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
        <link rel="stylesheet" type="text/css" href="style.css">

    <div class="container-fluid py-4">
        <main class="table ms-auto" id="customers_table">

            <section class="table__header">


                <div class="d-flex justify-content-md-end" >
                    <div class="text-center mt-4"> <!-- 'mt-3' adds top margin -->
                        <button class="btn btn-light btn-sm" onclick="showAddForm()">
                            <i class="fas fa-plus me-2"></i> ajouter une formateur
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
                            <li><label><input type="checkbox" class="column-toggle" data-col="1" checked> Email</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="2" checked> IP Address</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="3" checked> Submission Date</label></li>
                            <li><label><input type="checkbox" class="column-toggle" data-col="4" checked> Action</label></li>
                        </ul>
                    </div>


                    <!-- Search Input -->

                </div>



            </section>
            <section class="table__body">
                <table id="customers_table">

                    <thead>
                    <tr>
                        <th> Email <span class="icon-arrow">&UpArrow;</span></th>
                        <th> IP Address <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Submission Date <span class="icon-arrow">&UpArrow;</span></th>
                        <th> Action <span class="icon-arrow">&UpArrow;</span></th>

                    </tr>
                    </thead>
                    <tbody id="table-body">
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td> <?= htmlspecialchars($submission['email']) ?> </td>
                                <td> <?= htmlspecialchars($submission['ip']) ?> </td>
                                <td><?= date("Y-m-d", strtotime($submission['date_submission'])) ?></td>
                                <td>
                                    <!-- View Action -->
                                    <a href="javascript:void(0);" data-toggle="tooltip" title="Read"
                                       onclick='showDetails(<?php echo json_encode($submission, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                        <i class="fas fa-eye" style="color: #007bff;"></i>
                                    </a>
&nbsp;
                                    <!-- Edit Action -->
                                    <a href="javascript:void(0);" onclick="showEditForm(this)"
                                       data-id="<?php echo htmlspecialchars($submission['id']); ?>"
                                       data-email="<?php echo htmlspecialchars($submission['email']); ?>"
                                       data-ip="<?php echo htmlspecialchars($submission['ip']); ?>"
                                       data-date-submission="<?= date("Y-m-d", strtotime($submission['date_submission'])); ?>"
                                       data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-pencil-alt" style="color: #ffc107;"></i>
                                    </a>
&nbsp;

                                    <!-- Delete Action -->
                                    <a href="javascript:void(0);" data-toggle="tooltip" title="Delete"
                                       onclick='deleteNewsletter(<?php echo $submission['id']; ?>)'>
                                        <i class="fas fa-trash" style="color: #dc3545;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No records found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </section>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

            <!-- Pagination Section -->
            <div class="pagination">
                <button id="prevPage" class="pagination-btn">Précédent</button>
                <span id="pageInfo"></span>
                <button id="nextPage" class="pagination-btn">Suivant</button>
            </div>
        </main>
    </div>
    <div id="detailsCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
        <div class="card-header bg-info text-white p-4 rounded-top" style="border-bottom: 1px solid #e0e0e0;">
            <!-- Card Header: Title of the Registration Details -->
            <h5 class="mb-0"><i class="fas fa-user me-2"></i> Détails de la newsletter</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <!-- Left Column: Submission Information -->
                <div class="col-md-6">
                    <!-- ID -->
                    <div class="mb-3">
                        <p><strong>ID:</strong> <span id="detailId" class="fw-normal text-muted"><?= htmlspecialchars($submission['id']) ?></span></p>
                    </div>
                    <!-- Email Address -->
                    <div class="mb-3">
                        <p><strong>Email:</strong> <span id="detailEmail" class="fw-normal text-muted"><?= htmlspecialchars($submission['email']) ?></span></p>
                    </div>
                    <!-- IP Address -->
                    <div class="mb-3">
                        <p><strong>IP Address:</strong> <span id="detailIp" class="fw-normal text-muted"><?= htmlspecialchars($submission['ip']) ?></span></p>
                    </div>
                </div>

                <!-- Right Column: Submission Date -->
                <div class="col-md-6">
                    <!-- Submission Date -->
                    <div class="mb-3">
                        <p><strong>Date de soumission:</strong> <span id="detailDateSubmission" class="fw-normal text-muted"><?= htmlspecialchars($submission['date_submission']) ?></span></p>
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
    <div id="editCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
         style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
        <div class="card-header bg-gradient-success text-white p-3">
            <h6 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Modifier Newsletter</h6>
        </div>

        <div class="card-body p-3">
            <form id="editForm" method="post" action="updateNewsletters.php">
                <input type="hidden" name="id" id="editId">

                <div class="row">
                    <!-- ID -->


                    <!-- Email Address -->
                    <div class="col-md-4 mb-2">
                        <label for="editEmail">Email<span class="text-danger"> *</span></label>
                        <input type="email" id="editEmail" name="email" class="form-control" required>
                    </div>

                    <!-- IP Address -->
                    <div class="col-md-4 mb-2">
                        <label for="editIp">IP Address</label>
                        <input type="text" id="editIp" name="ip" class="form-control" readonly>
                    </div>

                    <!-- Submission Date -->
                    <div class="col-md-4 mb-2">
                        <label for="editDateSubmission">Date de soumission<span class="text-danger"> *</span></label>
                        <input type="date" id="editDateSubmission" name="date_submission" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-2"></i> Validé</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideEditForm()"><i class="fas fa-times me-2"></i> Annuler</button>
            </form>
        </div>
    </div>
        <div id="addCard" class="card position-fixed top-50 start-50 translate-middle shadow-lg"
             style="display: none; max-width: 900px; width: 90%; background-color: #f8f9fa;">
            <div class="card-header bg-gradient-primary text-white p-3">
                <h6 class="mb-0"><i class="fas fa-plus me-2"></i> Ajouter Newsletter</h6>
            </div>

            <div class="card-body p-3">
                <form id="addForm" method="post" action="addNewsLetter.php">
                    <!-- No ID field since we are adding a new entry -->
                    <div class="row">
                        <!-- Email Address -->
                        <div class="col-md-4 mb-2">
                            <label for="addEmail">Email<span class="text-danger"> *</span></label>
                            <input type="email" id="addEmail" name="email" class="form-control" required>
                        </div>

                        <!-- IP Address -->
                        <div class="col-md-4 mb-2">
                            <label for="addIp">IP Address</label>
                            <input type="text" id="addIp" name="ip" class="form-control">
                        </div>

                        <!-- Submission Date -->
                        <div class="col-md-4 mb-2">
                            <label for="addDateSubmission">Date de soumission<span class="text-danger"> *</span></label>
                            <input type="date" id="addDateSubmission" name="date_submission" class="form-control" required>
                        </div>
                    </div>

                    <!-- Google reCAPTCHA -->
                    <div class="form-group mb-2">
                        <div class="g-recaptcha" data-sitekey="6LecePkqAAAAAE9DChlfk1rZiiQzxdpNZffTMxrL"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-check me-2"></i> Ajouter</button>
                    <button type="button" class="btn btn-secondary w-100 mt-2" onclick="hideAddForm()"><i class="fas fa-times me-2"></i> Annuler</button>
                </form>
            </div>
        </div>

        <!-- Add this script to load reCAPTCHA -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>



    <script>
        function showDetails(data, id) {
            console.log("Data:", data);
            console.log("ID:", id);

            // Populate the detail fields with data
            document.getElementById("detailId").innerText = data.id || '';           // Display ID
            document.getElementById("detailEmail").innerText = data.email || '';     // Display email
            document.getElementById("detailIp").innerText = data.ip || '';           // Display IP address
            document.getElementById("detailDateSubmission").innerText = data.date_submission || '';  // Display submission date

            // Show the details card
            document.getElementById("detailsCard").style.display = "block";

            // Sending AJAX request to update status (if required)
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
            const email = button.getAttribute('data-email');
            const ip = button.getAttribute('data-ip');
            const dateSubmission = button.getAttribute('data-date-submission');

            // Populate the form fields with the selected participant's details
            document.getElementById('editId').value = id;
            document.getElementById('editEmail').value = email;
            document.getElementById('editIp').value = ip;
            document.getElementById('editDateSubmission').value = dateSubmission;

            // Show the edit card
            document.getElementById('editForm').action = 'updateNewsletters.php'; // Adjust the action if needed
            document.getElementById('editCard').style.display = 'block';
        }

        function hideEditForm() {
            // Hide the edit card
            document.getElementById('editCard').style.display = 'none';
        }

        function showAddForm() {
            document.getElementById('addCard').style.display = 'block';
        }

        // Hide the form
        function hideAddForm() {
            document.getElementById('addCard').style.display = 'none';
        }
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

    <script src="script.js"></script>

    <script>
        // JavaScript function to filter participants by year
        function filterData() {
            let year = document.getElementById('yearFilter').value;
            let month = document.getElementById('monthFilter').value;
            let date = document.getElementById('dateFilter').value;

            let url = "newsletters.php?";
            let params = [];

            if (year) params.push("year=" + year);
            if (month) params.push("month=" + month);
            if (date) params.push("date=" + date);

            window.location.href = url + params.join("&");
        }
    </script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteNewsletter(participantId) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Voulez-vous vraiment supprimer ce newsletter ? Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make an AJAX request to delete the participant
                    fetch('deleteNewsletters.php', {
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