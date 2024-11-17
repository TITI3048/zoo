<?php
$servername = "mysql-tibzooarcadia.alwaysdata.net";
$username = "376784";
$password = "Joyce3048.";
$dbname = "tibzooarcadia_zoo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['service-title'])) {
    $title = $conn->real_escape_string($_POST['service-title']);
    $description = $conn->real_escape_string($_POST['service-description']);
    $image = $_FILES['service-image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifiez si le fichier est une image réelle
    $check = getimagesize($_FILES['service-image']['tmp_name']);
    if ($check !== false) {
        if (move_uploaded_file($_FILES['service-image']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO services (title, description, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $description, $image);
            if ($stmt->execute()) {
                echo "New service added successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "File is not an image.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-service'])) {
    $service_id = $conn->real_escape_string($_POST['service-id']);
    $stmt = $conn->prepare("DELETE FROM services WHERE id=?");
    $stmt->bind_param("i", $service_id);
    if ($stmt->execute()) {
        echo "Service deleted successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Services - Tableau de Bord</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/dashboard.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Mon Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Accueil Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="animaux.php">Animaux</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion.php">Déconnexion</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="index.html">Retour au Site</a>
                </li>
            </ul>
        </div>
    </nav>
    <header class="bg-primary text-white text-center py-3">
        <h1>Gestion des Services</h1>
    </header>

    <main class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <section class="add-service mb-4">
                    <h2>Ajouter un Service</h2>
                    <form action="services.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="service-title">Titre du Service:</label>
                            <input type="text" class="form-control" id="service-title" name="service-title" required>
                        </div>
                        <div class="form-group">
                            <label for="service-description">Description:</label>
                            <textarea class="form-control" id="service-description" name="service-description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="service-image">Image:</label>
                            <input type="file" class="form-control-file" id="service-image" name="service-image" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </section>
            </div>

            <div class="col-md-6">
                <section class="delete-service mb-4">
                    <h2>Supprimer un Service</h2>
                    <form action="services.php" method="post">
                        <div class="form-group">
                            <label for="service-id">ID du Service:</label>
                            <input type="text" class="form-control" id="service-id" name="service-id" required>
                        </div>
                        <button type="submit" class="btn btn-danger" name="delete-service">Supprimer</button>
                    </form>
                </section>
            </div>
        </div>

        <section class="current-services">
            <h2>Services Actuels</h2>
            <div class="row">
                
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>