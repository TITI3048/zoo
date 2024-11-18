<?php
// Connexion à la base de données
$servername = "mysql-tibzooarcadia.alwaysdata.net";
$db_username = "376784";
$db_password = "Joyce3048.";
$dbname = "tibzooarcadia_zoo";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête pour les 3 animaux les plus likés
$sql = "SELECT animaux.nom AS nom_animal, COUNT(likes.id) AS total_likes
        FROM animaux
        LEFT JOIN likes ON animaux.id = likes.animal_id
        GROUP BY animaux.nom
        ORDER BY total_likes DESC
        LIMIT 3";
$result = $conn->query($sql);

$animal_data = [];
while ($row = $result->fetch_assoc()) {
    $animal_data[] = [
        'nom_animal' => $row['nom_animal'],
        'total_likes' => $row['total_likes']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/index.global.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        #calendar {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link" href="accueil.html">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container">
        <h1 class="text-center">Dashboard</h1>
        <div class="row">
            <!-- Graphique des animaux les plus likés -->
            <div class="col-md-6">
                <canvas id="likeChart"></canvas>
            </div>

            <!-- Calendrier -->
            <div class="col-md-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Script pour le graphique -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Données pour le graphique
            const ctx = document.getElementById("likeChart").getContext("2d");
            const likeChart = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: <?php echo json_encode(array_column($animal_data, 'nom_animal')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($animal_data, 'total_likes')); ?>,
                        backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56"],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Configuration du calendrier
            const calendarEl = document.getElementById("calendar");
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: "dayGridMonth",
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay"
                },
                events: [
                    // Exemple d'événements
                    { title: "Visite Vétérinaire", start: "2024-11-15" },
                    { title: "Inspection Zoo", start: "2024-11-18" },
                    { title: "Entretien Habitat", start: "2024-11-20" }
                ]
            });

            calendar.render();
        });
    </script>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
