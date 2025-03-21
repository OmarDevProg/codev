
<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit; // Stop the script if the user is not logged in
}
?>


<?php
$conn = new PDO("mysql:host=localhost;dbname=codevfordb14", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['album'])) {
  die("Album not specified.");
}

$album_id = $_GET['album'];
$stmt = $conn->prepare("SELECT * FROM images WHERE album_id = :album_id");
$stmt->execute(['album_id' => $album_id]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
  // Upload image
  $targetDir = "uploads/";
  if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
  $fileName = time() . "_" . basename($_FILES['image']['name']);
  $targetFilePath = $targetDir . $fileName;
  if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
    $stmt = $conn->prepare("INSERT INTO images (album_id, image_path) VALUES (:album_id, :image_path)");
    $stmt->execute(['album_id' => $album_id, 'image_path' => $targetFilePath]);
    header("Location: album_details.php?album=" . $album_id);
    exit();
  }
}
?>
<?php include "navbar.php"?>
<main class="main-content position-relative border-radius-lg ">


  <style>
      .image-card {
          background-color: #fff;
          border-radius: 15px;
          box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
          position: relative;
          overflow: hidden;
          transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
      }

      .image-card:hover {
          transform: translateY(-5px);
          box-shadow: 0px 12px 25px rgba(0, 0, 0, 0.2);
      }

      .image-card img {
          width: 100%;
          height: 250px; /* Fixed height for consistent sizing */
          object-fit: cover;
          border-radius: 10px;
          transition: transform 0.3s ease-in-out;
      }

      .image-card:hover img {
          transform: scale(1.05); /* Slight zoom effect on hover */
      }

      .dropleft {
          position: absolute;
          top: 10px;
          right: 10px;
      }

      .dropleft .fa-ellipsis-v {
          font-size: 20px;
          color: #fff;
          transition: transform 0.2s ease-in-out;
      }

      .dropleft .fa-ellipsis-v:hover {
          transform: rotate(90deg); /* Smooth rotation effect on hover */
      }

      .input-group {
          max-width: 500px;
          margin: 0 auto;
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
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="card border-0 rounded-sm shadow-sm">
                <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center p-3">
                    <h6 class="mb-0"><i class="fas fa-camera me-2"></i> Images in Album</h6>
                </div>

                <!-- Image Upload Section -->
                <!-- Image Upload Section -->
                <form method="post" enctype="multipart/form-data" class="mb-3" id="uploadForm">
                    <input type="hidden" name="album_id" value="<?= $album_id ?>">

                    <!-- Hidden File Input -->
                    <input type="file" name="image" id="imageUpload" class="d-none" required>

                    <!-- Hot Icon Button -->
                    <button type="button" class="btn btn-link text-success" id="uploadButton">
                        <i class="fa fa-cloud-upload-alt fa-2x"></i> <!-- Upload Icon -->
                    </button>
                </form>

                <script>
                    // Trigger file input when the button is clicked
                    document.getElementById('uploadButton').addEventListener('click', function() {
                        document.getElementById('imageUpload').click();
                    });

                    // Automatically submit the form when the file is selected
                    document.getElementById('imageUpload').addEventListener('change', function() {
                        if (this.files.length > 0) {
                            // Submit the form once a file is selected
                            document.getElementById('uploadForm').submit();
                        }
                    });
                </script>



                <!-- Image Cards Section -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 py-2" id="image-container">
                    <?php foreach ($images as $image): ?>
                        <div class="col p-3 item">
                            <div class="image-card position-relative">
                                <img src="<?= $image['image_path'] ?>" class="img-thumbnail img-fluid" alt="Image" loading="lazy">
                                <div class="dropleft position-absolute top-0 end-0 p-2">
                                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="text-light">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit_image" data-id="<?= $image['id'] ?>" href="javascript:void(0)">
                                            <i class="fa fa-edit text-primary"></i> Modifier
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_image" data-id="<?= $image['id'] ?>" href="javascript:void(0)">
                                            <i class="fa fa-trash text-danger"></i> Effacer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Back to Albums Button -->
                <div class="text-center mt-3">
                    <a href="albums.php" class="btn btn-secondary">Retour aux galeries</a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handle deleting an image
        $(document).on('click', '.delete_image', function() {
            var imageId = $(this).data('id');

            // Confirm deletion
            if (confirm('Are you sure you want to delete this image?')) {
                // Make AJAX request to delete the image
                $.ajax({
                    url: 'delete_image.php',
                    method: 'POST',
                    data: { image_id: imageId },
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === 'success') {
                            // Remove the image from the page
                            $('[data-id="' + imageId + '"]').closest('.image-card').remove();
                        } else {
                            alert('Failed to delete image.');
                        }
                    }
                });
            }
        });
    });
    </script>

    <?php include "footer.php"?>


</html>