<?php
// Connexion à la base de données
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "zoo_arcadia";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête pour obtenir les employés
$sql = "SELECT id, nom, prenom, email, poste FROM employes";
$result = $conn->query($sql);

// Ajouter un employé
if (isset($_POST['add_employe'])) {
    $nom = $_POST['employe_name'];
    $prenom = $_POST['employe_firstname'];
    $email = $_POST['employe_email'];
    $poste = $_POST['employe_position'];

    // Vérification si l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "L'email fourni est invalide.";
    } else {
        // Vérification si l'email existe déjà
        $check_sql = "SELECT * FROM employes WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_message = "Cet email est déjà utilisé par un autre employé.";
        } else {
            // Ajouter l'employé
            $sql = "INSERT INTO employes (nom, prenom, email, poste) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $nom, $prenom, $email, $poste);
            $stmt->execute();
            $success_message = "Employé ajouté avec succès!";
            header("Location: employes.php");
            exit();  // Important : quitter après la redirection
        }
    }
}

// Retirer un employé
if (isset($_POST['delete_employe'])) {
    $employee_id = $_POST['employe_id'];
    $sql = "DELETE FROM employes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $employee_id);
    $stmt->execute();
    $success_message = "Employé retiré avec succès!";
    header("Location: employes.php");
    exit();  // Important : quitter après la redirection
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Employés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .chart-container {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #calendar {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            margin-bottom: 40px;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
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
                        <a class="nav-link" href="animaux.php">Animaux</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="employes.php">Employés</a>
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
        <h1 class="text-center mt-4">Gestion des Employés</h1>

        <!-- Affichage des messages de succès ou d'erreur -->
        <?php if (isset($success_message)) : ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)) : ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Ajouter un employé -->
            <div class="col-md-6">
                <h3>Ajouter un Employé</h3>
                <form method="post" action="employes.php">
                    <div class="form-group">
                        <label for="employe_name">Nom :</label>
                        <input type="text" class="form-control" id="employe_name" name="employe_name" required>
                    </div>
                    <div class="form-group">
                        <label for="employe_firstname">Prénom :</label>
                        <input type="text" class="form-control" id="employe_firstname" name="employe_firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="employe_email">Email :</label>
                        <input type="email" class="form-control" id="employe_email" name="employe_email" required>
                    </div>
                    <div class="form-group">
                        <label for="employe_position">Poste :</label>
                        <input type="text" class="form-control" id="employe_position" name="employe_position" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3" name="add_employe">Ajouter</button>
                </form>
            </div>

            <!-- Retirer un employé -->
            <div class="col-md-6">
                <h3>Retirer un Employé</h3>
                <form method="post" action="employes.php">
                    <div class="form-group">
                        <label for="employe_id">ID de l'employé :</label>
                        <input type="number" class="form-control" id="employe_id" name="employe_id" required>
                    </div>
                    <button type="submit" class="btn btn-danger mt-3" name="delete_employe">Retirer</button>
                </form>
            </div>
        </div>

        <h3 class="mt-4">Liste des Employés</h3>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Poste</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nom']; ?></td>
                        <td><?php echo $row['prenom']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['poste']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
