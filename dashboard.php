<?php
// Connexion à la base de données
$servername = "mysql-tibzooarcadia.alwaysdata.net";
$db_username = "376784"; // Votre nom d'utilisateur
$db_password = "Joyce3048."; // Votre mot de passe
$dbname = "tibzooarcadia_zoo";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupération des 3 animaux les plus likés
$sql = "SELECT animaux.nom, COUNT(likes.id) AS total_likes 
        FROM animaux 
        JOIN likes ON animaux.id = likes.animal_id 
        GROUP BY animaux.nom 
        ORDER BY total_likes DESC 
        LIMIT 3";
$result = $conn->query($sql);

$labels = [];
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['nom'];
        $data[] = $row['total_likes'];
    }
}

// Déconnexion temporaire pour protéger les données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <style>
        body {
            background: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">Tableau de bord</h1>

        <div class="row">
            <!-- Camembert des animaux les plus likés -->
            <div class="col-md-6">
                <h3 class="text-center">Top 3 des animaux les plus likés</h3>
                <canvas id="chart"></canvas>
            </div>

            <!-- Calendrier des rendez-vous -->
            <div class="col-md-6">
                <h3 class="text-center">Calendrier des rendez-vous</h3>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script>
        // Données pour le camembert
        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($labels); ?>, // Noms des animaux
                datasets: [{
                    label: 'Nombre de likes',
                    data: <?php echo json_encode($data); ?>, // Nombre de likes
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                    hoverOffset: 4
                }]
            }
        });

        // Calendrier interactif
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: 'api/events.php', // Endpoint pour récupérer les événements
                editable: true,
                selectable: true,
                dateClick: function (info) {
                    alert('Date sélectionnée : ' + info.dateStr);
                }
            });
            calendar.render();
        });
    </script>
</body>

</html>
