<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "zoo_arcadia";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Créer la table animaux si elle n'existe pas déjà
$conn->query("CREATE TABLE IF NOT EXISTS animaux (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(30) NOT NULL,
    habitat VARCHAR(50) NOT NULL,
    espece VARCHAR(30) NOT NULL,
    likes INT(6) NOT NULL DEFAULT 0
)");

// Ajouter un animal
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nom'])) {
    $nom = $conn->real_escape_string($_POST['nom']);
    $habitat = $conn->real_escape_string($_POST['habitat']);
    $espece = $conn->real_escape_string($_POST['espece']);
    $conn->query("INSERT INTO animaux (nom, habitat, espece) VALUES ('$nom', '$habitat', '$espece')");
}

// Supprimer un animal
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM animaux WHERE id=$delete_id");
}

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filtrer les animaux par habitat
$selected_habitat = isset($_POST['filter_habitat']) ? $conn->real_escape_string($_POST['filter_habitat']) : '';
$sql = "SELECT id, nom, habitat, espece, likes FROM animaux";
if ($selected_habitat) {
    $sql .= " WHERE habitat='$selected_habitat'";
}
$sql .= " LIMIT $limit OFFSET $offset";
$paginated_result = $conn->query($sql);

// Nombre total d'animaux
$total_result = $conn->query("SELECT COUNT(*) AS count FROM animaux")->fetch_assoc();
$total_pages = ceil($total_result['count'] / $limit);
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Animaux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .pagination {
            justify-content: center;
        }
    </style>
    <script>
        function confirmDelete(form) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet animal ? Cette action est irréversible.')) {
        form.submit();
    }
}
    </script>
</head>

<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Arcadia Zoo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Animaux</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employes.php">Employés</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">Retour accueil</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container">
        <h1 class="text-center mb-4">Gestion des Animaux</h1>

        <!-- Ajouter un nouvel animal -->
        <h2>Ajouter un Animal</h2>
        <form method="post" action="animaux.php">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="mb-3">
                <label for="habitat" class="form-label">Habitat</label>
                <input type="text" class="form-control" id="habitat" name="habitat" required>
            </div>
            <div class="mb-3">
                <label for="espece" class="form-label">Espèce</label>
                <input type="text" class="form-control" id="espece" name="espece" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>

        <!-- Filtrer par habitat -->
        <h2 class="mt-5">Filtrer par Habitat</h2>
        <form method="post" action="animaux.php">
            <div class="mb-3">
                <label for="filter_habitat" class="form-label">Habitat</label>
                <select class="form-select" id="filter_habitat" name="filter_habitat">
                    <option value="">Tous</option>
                    <?php
                    $habitats = $conn->query("SELECT DISTINCT habitat FROM animaux");
                    while ($row = $habitats->fetch_assoc()) {
                        $selected = $row['habitat'] == $selected_habitat ? 'selected' : '';
                        echo "<option value='" . $row['habitat'] . "' $selected>" . $row['habitat'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>

        <!-- Liste des animaux -->
        <h2 class="mt-5">Liste des Animaux</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Habitat</th>
                    <th>Espèce</th>
                    <th>Likes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($paginated_result->num_rows > 0) {
                    while ($row = $paginated_result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nom']}</td>
                            <td>{$row['habitat']}</td>
                            <td>{$row['espece']}</td>
                            <td>{$row['likes']}</td>
                            <td>
                                <form method='post' action='animaux.php' style='display:inline;'>
                                    <input type='hidden' name='delete_id' value='{$row['id']}'>
                                    <button type='button' class='btn btn-danger btn-sm' onclick='confirmDelete(this.closest(\"form\"))'>Supprimer</button>
                                </form>
                            </td>
                        </tr>";
                    }
                } else {
                echo "<tr><td colspan='6'>Aucun animal trouvé.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i == $page) ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='animaux.php?page=$i'>$i</a></li>";
            }
            ?>
        </ul>
    </nav>
</div>

</body>

</html>

<?php
$conn->close();
?>