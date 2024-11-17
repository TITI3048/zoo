<?php
$servername = "mysql-tibzooarcadia.alwaysdata.net";
$username = "376784";
$password = "Joyce3048.";
$dbname = "tibzooarcadia_zoo"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Utiliser la base de données existante
$conn->select_db($dbname); 

// Créer la table animaux si elle n'existe pas déjà
$sql = "CREATE TABLE IF NOT EXISTS animaux (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(30) NOT NULL,
    habitat VARCHAR(50) NOT NULL,
    espece VARCHAR(30) NOT NULL,
    likes INT(6) NOT NULL DEFAULT 0
)";

if ($conn->query($sql) === TRUE) {
    echo "Table animaux créée avec succès ou déjà existante.";
} else {
    die("Erreur lors de la création de la table: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nom'])) {
    $nom = $conn->real_escape_string($_POST['nom']);
    $habitat = $conn->real_escape_string($_POST['habitat']);
    $espece = $conn->real_escape_string($_POST['espece']);

    $sql = "INSERT INTO animaux (nom, habitat, espece) VALUES ('$nom', '$habitat', '$espece')";
    if ($conn->query($sql) === TRUE) {
        echo "Nouvel enregistrement créé avec succès";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $conn->real_escape_string($_POST['delete_id']);

    $sql = "DELETE FROM animaux WHERE id='$delete_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Enregistrement supprimé avec succès";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }
}

$selected_habitat = isset($_POST['filter_habitat']) ? $conn->real_escape_string($_POST['filter_habitat']) : '';

$sql = "SELECT id, nom, habitat, espece, likes FROM animaux";
if ($selected_habitat) {
    $sql .= " WHERE habitat='$selected_habitat'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animaux</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<style>
    body {
        background-color: #2980b9 ;
    }
    .container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        margin-bottom: 20px
    }
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Mon Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="dashboard.php">Accueil Dashboard<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="employes.php">Employés</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="services.php">Services</a>
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
<div class="container">
    <h2 class="mt-5">Ajouter un Animal</h2>
    <form method="post" action="animaux.php">
        <div class="form-group">
            <label for="nom">Nom:</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="habitat">Habitat:</label>
            <input type="text" class="form-control" id="habitat" name="habitat" required>
        </div>
        <div class="form-group">
            <label for="espece">Espèce:</label>
            <input type="text" class="form-control" id="espece" name="espece" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>

    <h2 class="mt-5">Filtrer par Habitat</h2>
    <form method="post" action="animaux.php">
        <div class="form-group">
            <label for="filter_habitat">Habitat:</label>
            <select class="form-control" id="filter_habitat" name="filter_habitat">
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

    <h2 class="mt-5">Liste des Animaux</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Habitat</th>
                <th>Espèce</th>
                <th>Likes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"]. "</td>
                            <td>" . $row["nom"]. "</td>
                            <td>" . $row["habitat"]. "</td>
                            <td>" . $row["espece"]. "</td>
                            <td class='like-count' data-id='" . $row["id"]. "'>" . $row["likes"]. "</td>
                            <td>
                                <form method='post' action='animaux.php' style='display:inline;'>
                                    <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                    <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                                </form>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Aucun animal trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var cardId = this.getAttribute('data-id');
            var likeCountElement = document.querySelector('.like-count[data-id="' + cardId + '"]');
            var likeCount = parseInt(likeCountElement.textContent);

            fetch('update_likes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cardId: cardId, likeCount: likeCount + 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCountElement.textContent = likeCount + 1;
                } else {
                    alert('Erreur lors de la mise à jour des likes.');
                }
            });
        });
    });
});
</script>

</body>
</html>

<?php
$conn->close();
?>