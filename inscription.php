<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "mysql-tibzooarcadia.alwaysdata.net";
$db_username = "376784"; // Remplacez par votre nom d'utilisateur réel
$db_password = "Joyce3048."; // Remplacez par votre mot de passe réel
$dbname = "tibzooarcadia_zoo";

// Connexion à la base de données
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Traitement des inscriptions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['inscrire'])) {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $validate_password = $_POST['validate_password'];
    $poste = $_POST['poste'];  // Récupérer le poste (employé ou vétérinaire)

    // Débogage - Affichage des variables pour vérifier leur contenu
    echo "Nom: $nom, Prénom: $prenom, Email: $email, Poste: $poste<br>";

    // Vérifier si les mots de passe correspondent
    if ($password !== $validate_password) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    // Vérifier si l'utilisateur existe déjà
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Adresse email déjà utilisée.";
    } else {
        // Insérer le nouvel utilisateur avec status 'pending' et le poste spécifié
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (nom, prenom, email, password, poste, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $nom, $prenom, $email, $hashed_password, $poste);

        // Vérifier si l'insertion a réussi
        if ($stmt->execute()) {
            echo "Inscription en attente de validation.";
        } else {
            // Afficher l'erreur MySQL si l'insertion échoue
            echo "Erreur lors de l'inscription : " . $stmt->error;
        }
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
    <title>Inscription</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url(/image/illustration-nature-motifs-feuilles-conception-plantes-abstraites-ia-generative_188544-12678.jpg);
            background-size: cover;
            background-position: center;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .lien {
            margin-top: 20px;
            text-align: center;
        }

        .lien a {
            color: #007bff;
            text-decoration: none;
        }

        .lien a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Inscription</h1>
        <form method="post" action="inscription.php">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nom">Nom :</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="prenom">Prénom :</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Mot de passe :</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="validate_password">Confirmer le mot de passe :</label>
                    <input type="password" class="form-control" id="validate_password" name="validate_password" required>
                </div>
            </div>
            <div class="form-group">
                <label for="poste">Poste occupé :</label>
                <select class="form-control" id="poste" name="poste" required>
                    <option value="employé">Employé</option>
                    <option value="vétérinaire">Vétérinaire</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block" name="inscrire">S'inscrire</button>
        </form>
        <div class="lien">
            <a href="index.html">Retour à l'accueil</a>
        </div>
    </div>
</body>

</html>
