<?php
// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <title>
        CODEV
    </title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check if current page is in the "Gestion des utilisateurs" or "Gestion des inscriptions"
            const currentPage = "<?php echo $current_page; ?>";

            // If the current page matches any of the "Gestion des utilisateurs" pages, expand the submenu
            if (currentPage === 'g_admin.php' || currentPage === 'g_participants.php' || currentPage === 'g_formateur.php') {
                document.getElementById('submenuUsers').classList.add('show');
            }

            // If the current page matches any of the "Gestion des inscriptions" pages, expand the submenu
            if (currentPage === 'inscriptionMesure.php' || currentPage === 'inscriptionCatalogue.php') {
                document.getElementById('submenuInscription').classList.add('show');
            }
        });
    </script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>

    <!-- Nucleo Icons -->
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- CSS Files -->
    <link id="pagestyle" href="assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />

</head>

<body class="g-sidenav-show   bg-gray-100">
<div class="min-height-300 bg-dark position-absolute w-100"></div>
<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-2 fixed-start ms-4" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="dashboard.php" target="_blank">
            <img src="assets/img/logo.png" width="500px" height="auto" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold"></span>
        </a>
    </div>
    <hr class="horizontal dark mt-4">
    <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Tableau de bord</span>
                </a>
            </li>

            <!-- User Management -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle <?php echo ($current_page == 'g_admin.php' || $current_page == 'g_participants.php' || $current_page == 'g_formateur.php') ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#submenuUsers" role="button" aria-expanded="false" aria-controls="submenuUsers">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-users text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Gestion des utilisateurs</span>
                </a>
                <div class="collapse" id="submenuUsers">
                    <ul class="nav flex-column ms-2">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'g_admin.php') ? 'active' : ''; ?>" href="g_admin.php">
                                <i class="fa fa-user text-danger text-sm opacity-10 me-2"></i>
                                Gestion des admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'g_participants.php') ? 'active' : ''; ?>" href="g_participants.php">
                                <i class="fa fa-users text-warning text-sm opacity-10 me-2"></i>
                                Gestion des participants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'g_formateur.php') ? 'active' : ''; ?>" href="g_formateur.php">
                                <i class="fa fa-chalkboard-teacher text-primary text-sm opacity-10 me-2"></i>
                                Gestion des formateurs
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Inscription Management -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle <?php echo ($current_page == 'inscriptionMesure.php' || $current_page == 'inscriptionCatalogue.php') ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#submenuInscription" role="button" aria-expanded="false" aria-controls="submenuInscription">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-file-signature text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Gestion des inscriptions</span>
                </a>
                <div class="collapse" id="submenuInscription">
                    <ul class="nav flex-column ms-2">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'inscriptionMesure.php') ? 'active' : ''; ?>" href="inscriptionMesure.php">
                                <i class="fa fa-user-plus text-primary text-sm opacity-10 me-2"></i>
                                Inscription sur mesure
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'inscriptionCatalogue.php') ? 'active' : ''; ?>" href="inscriptionCatalogue.php">
                                <i class="fa fa-th-list text-info text-sm opacity-10 me-2"></i>
                                Inscription sur catalogue
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Other Links -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'g_projet.php') ? 'active' : ''; ?>" href="g_projet.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-tasks text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Gestion des projets</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'newsletters.php') ? 'active' : ''; ?>" href="newsletters.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-newspaper text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Newsletters</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'evenement.php') ? 'active' : ''; ?>" href="evenement.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-bullet-list-67 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Evenements</span>
                </a>
            </li>

            <!-- More Links -->
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'albums.php') ? 'active' : ''; ?>" href="albums.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-images text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Galeries</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-address-book text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Liste des contacts</span>
                </a>
            </li>

            <!-- Account Pages -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account pages</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>" href="profile.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>" href="logout.php">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Se deconnecter</span>
                </a>
            </li>
        </ul>
    </div>
</aside>