<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "zoo_arcadia";

// Créer la connexion à la base de données
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

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
                echo "Service ajouté avec succès.";
            } else {
                echo "Erreur: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Désolé, une erreur est survenue lors du téléchargement de l'image.";
        }
    } else {
        echo "Le fichier n'est pas une image.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-service'])) {
    $service_id = $conn->real_escape_string($_POST['service-id']);
    $stmt = $conn->prepare("DELETE FROM services WHERE id=?");
    $stmt->bind_param("i", $service_id);
    if ($stmt->execute()) {
        echo "Service supprimé avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }
    $stmt->close();
}

// Récupérer les services actuels depuis la base de données
$sql = "SELECT id, title, description, image FROM services";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Services - Tableau de Bord</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Arcadia Zoo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="animaux.php">Animaux</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="employes.php">Employés</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.html">Retour Accueil</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Header -->
    <header class="bg-primary text-white text-center py-4">
        <h1>Gestion des Services</h1>
        <p>Gérez les services disponibles dans le zoo Arcadia.</p>
    </header>

    <!-- Main Content -->
    <main class="container mt-4">
        <div class="row">
            <!-- Ajouter un Service -->
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

            <!-- Supprimer un Service -->
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

        <!-- Services Actuels -->
        <section class="current-services">
            <h2>Services Actuels</h2>
            <div class="row">
                <?php
                // Vérifier s'il y a des services et les afficher
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-md-4 mb-4">';
                        echo '<div class="card">';
                        echo '<img src="uploads/' . $row['image'] . '" class="card-img-top" alt="Image Service">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $row['title'] . '</h5>';
                        echo '<p class="card-text">' . $row['description'] . '</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Aucun service ajouté pour le moment.</p>';
                }
                ?>
            </div>
        </section>
    </main>

    <!-- Footer (optionnel) -->
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Arcadia Zoo. Tous droits réservés.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>


