
<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit; // Stop the script if the user is not logged in
}
?>

<?php
try {
    // Establish database connection
    $conn = new PDO("mysql:host=localhost;dbname=codevfordb14", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get albums
    $stmt = $conn->query("SELECT * FROM albums");

    // Fetch all albums
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if albums are fetched, otherwise set it as an empty array
    if (!$albums) {
        $albums = [];
    }
} catch (PDOException $e) {
    // Handle connection error
    echo "Connection failed: " . $e->getMessage();
    $albums = []; // Make sure $albums is an empty array in case of an error
}
function validate_image($path)
{
    // Define the path to your default image if the $path is empty or invalid
    $defaultImage = 'path/to/default/image.jpg';

    // If the $path is empty or invalid, return the default image
    if (empty($path) || !file_exists($path)) {
        return $defaultImage;
    }

    // If the path is valid, return the actual image path
    return $path;
}

?>

<?php
include "navbar.php";

?>


<main class="main-content position-relative border-radius-lg ">

    <style>
        .album-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .album-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 12px 25px rgba(0, 0, 0, 0.2);
        }

        .album-header h5 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            color: #333;
            letter-spacing: 0.5px;
        }

        .album-view {
            position: relative;
            height: 220px; /* Slightly higher image height */
            overflow: hidden;
            border-radius: 10px;
        }

        .album-view img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }

        .album-view:hover img {
            transform: scale(1.1); /* Slight zoom effect on hover */
        }

        .album-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6); /* Darker, more subtle background */
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 0 0 15px 15px; /* Rounded bottom corners */
            font-size: 14px;
        }

        .album-info span {
            font-weight: bold;
        }

        .album-info .dropleft {
            position: relative;
        }

        .album-info .fa-ellipsis-v {
            font-size: 20px;
            transition: transform 0.2s ease-in-out;
        }

        .album-info .fa-ellipsis-v:hover {
            transform: rotate(90deg); /* Smooth rotation effect on hover */
        }

        .item {
            justify-content: center;
            align-items: center;
        }

        .album-card .album-header {
            background-color: #f7f7f7;
            border-bottom: 1px solid #e0e0e0;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ddd;
        }

        #add-new {
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
            border-radius: 25px;
        }

        #add-new i {
            font-size: 18px;
            margin-right: 8px;
        }



    </style>
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-white active" aria-current="page">Galerie</li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">Gestion de galerie</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">

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


    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-md-12 mt-4">
                <div class="card border-0 rounded-sm shadow-sm">
                    <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center p-3">
                        <h6 class="mb-0"><i class="fas fa-camera me-2"></i> Gallerie</h6>
                        <button class="btn btn-flat btn-primary" id="add-new" type="button" data-bs-toggle="modal" data-bs-target="#addAlbumModal">
                            <i class="fa fa-plus"></i> Ajouter
                        </button>
                    </div>
                    <!-- Albums Section -->
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 py-2" id="album-container">
                        <?php if (count($albums) > 0): ?>
                            <?php foreach ($albums as $album): ?>
                                <div class="col p-3 item">
                                    <a href="album_details.php?album=<?= $album['id'] ?>" class="album-item">
                                        <div class="album-card">
                                            <div class="album-header text-center p-3">
                                                <h5 class="text-dark"><?= htmlspecialchars($album['name']) ?></h5>
                                            </div>
                                            <div class="album-view position-relative">
                                                <?php
                                                $imgs = $conn->query("SELECT * FROM `images` WHERE album_id = '{$album['id']}' AND delete_f = 0 ORDER BY unix_timestamp(date_created)");
                                                $img = []; // Initialize the image array
                                                while ($irow = $imgs->fetch(PDO::FETCH_ASSOC)) {
                                                    $img[] = $irow['image_path']; // Collect images
                                                }

                                                if (count($img) > 0) {
                                                    // Display up to 4 images
                                                    foreach ($img as $path): ?>
                                                        <img src="<?= validate_image($path) ?>" class="img-thumbnail img-fluid album-banner mb-2" alt="img" loading="lazy">
                                                    <?php endforeach;
                                                } else { ?>
                                                    <img src="<?= validate_image('') ?>" class="img-thumbnail img-fluid album-banner mb-2" alt="img" loading="lazy">
                                                <?php } ?>
                                            </div>
                                            <div class="album-info position-absolute bottom-0 w-100 p-3 bg-dark bg-opacity-75 text-white d-flex justify-content-between align-items-center">
                                                <div class="text-light">
                                                    <span><i class="fa fa-picture-o"></i> <?= count($img) ?> Images</span>
                                                </div>
                                                <div class="dropleft">
                                                    <a href="#" id="menus_<?= $album['id'] ?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="text-light">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a class="dropdown-item edit_album" data-id="<?= $album['id'] ?>" href="javascript:void(0)">
                                                            <i class="fa fa-edit text-primary"></i> Rename
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item delete_album" data-id="<?= $album['id'] ?>" href="javascript:void(0)">
                                                            <i class="fa fa-trash text-danger"></i> Remove
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="w-100 p-2 text-center" id="nData">
                                <b>No Albums Listed</b>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="modal" id="addAlbumModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAlbumModalLabel">creer nouveau album</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="create-album-form">
                        <div class="mb-3">
                            <label for="album_name" class="form-label">Album Name</label>
                            <input type="text" class="form-control" id="album_name" name="album_name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submit-album">Create Album</button>
                </div>
            </div>
        </div>
    </div>




    <script>
        $(document).ready(function() {
            $('#submit-album').click(function(e) {
                e.preventDefault(); // Empêche le rechargement par défaut

                const albumName = $('#album_name').val().trim();

                if (albumName === "") {
                    alert("Le nom de l'album est requis !");
                    return;
                }

                $.ajax({
                    url: "create_album.php",
                    type: "POST",
                    data: { album_name: albumName },
                    dataType: "json",
                    success: function(response) {
                        console.log(response); // Vérifier la réponse dans la console
                        if (response.status === 'success') {
                            alert("Album créé avec succès !");
                            location.reload(); // Recharger la page pour voir le nouvel album
                        } else {
                            alert("Erreur: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        console.log("Response:", xhr.responseText);
                    }
                });
            });
        });



        // Handle renaming an album
        $(document).on('click', '.edit_album', function() {
            var albumId = $(this).data('id');
            var currentName = $(this).closest('.album-card').find('.album-header h5').text().trim();

            // Prompt user to enter a new album name
            var newName = prompt('Enter a new name for the album:', currentName);
            if (newName && newName !== currentName) {
                // Make AJAX request to rename the album
                $.ajax({
                    url: 'rename_album.php',
                    method: 'POST',
                    data: {
                        album_id: albumId,
                        new_name: newName
                    },
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === 'success') {
                            // Update the album name on the page
                            $('[data-id="' + albumId + '"]').closest('.album-card').find('.album-header h5').text(newName);
                        } else {
                            alert('Failed to rename album.');
                        }
                    }
                });
            }
        });
        // Handle deleting an album
        $(document).on('click', '.delete_album', function() {
            var albumId = $(this).data('id');

            // Confirm deletion
            if (confirm('Are you sure you want to delete this album?')) {
                // Make AJAX request to delete the album
                $.ajax({
                    url: 'delete_album.php',
                    method: 'POST',
                    data: {
                        album_id: albumId
                    },
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === 'success') {
                            // Remove the album from the page
                            $('[data-id="' + albumId + '"]').closest('.col').remove();
                        } else {
                            alert('Failed to delete album.');
                        }
                    }
                });
            }
        });
    </script>



<?php
include "footer.php";

?>

