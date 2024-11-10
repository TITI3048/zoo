<?php
session_start();


$servername = "mysql-tibzooarcadia.alwaysdata.net";
$username = "376784";
$password = "Joyce3048.";
$dbname = "tibzooarcadia_zoo";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

$query = 'SELECT * FROM animaux'; 

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Erreur dans la préparation de la requête : " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Erreur dans la requête : " . $conn->error);
}

$query_top3 = 'SELECT nom, likes FROM animaux ORDER BY likes DESC LIMIT 3';
$stmt_top3 = $conn->prepare($query_top3);
if (!$stmt_top3) {
    die("Erreur dans la préparation de la requête top3 : " . $conn->error);
}
$stmt_top3->execute();
$result_top3 = $stmt_top3->get_result();

$animaux = [];
$likes = [];

if ($result_top3->num_rows > 0) {
    while ($row = $result_top3->fetch_assoc()) {
        $animaux[] = $row['nom'];
        $likes[] = $row['likes'];
    }
} else {
    echo "0 results";
}

$stmt->close();
$stmt_top3->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/fr.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> 
</head>
<style>
    body {
        background-color: #2980b9;
    }

    .container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .content {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .chart-container {
        width: 50%;
        margin: auto;
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
                    <a class="nav-link" href="employes.php">Employés <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="animaux.php">Animaux</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="services.php">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion.php">Déconnexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="accueil.html">Retour au Site</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container content">
        <div class="container mb-5">
            <h1 class="mt-5">Top 5 des animaux les plus aimés</h1>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Habitat</th>
                        <th>Espèce</th>
                        <th>Likes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nom']); ?></td>
                            <td><?php echo htmlspecialchars($row['habitat']); ?></td>
                            <td><?php echo htmlspecialchars($row['espece']); ?></td>
                            <td><?php echo htmlspecialchars($row['likes']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="container mb-5 chart-container">
            <h2 class="mt-5">Top 3 des animaux les plus likés</h2>
            <canvas id="pieChart"></canvas>
        </div>

        <div class="container">
            <h2 class="mt-5">Calendrier</h2>
            <div id="calendar" class="mt-3 p-3 border rounded bg-light"></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                locale: 'fr',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: true,
                selectable: true,
                selectHelper: true,
                select: function(start, end) {
                    var title = prompt('Titre de l\'événement:');
                    var eventData;
                    if (title) {
                        eventData = {
                            title: title,
                            start: start,
                            end: end
                        };
                        $('#calendar').fullCalendar('renderEvent', eventData, true);
                    }
                    $('#calendar').fullCalendar('unselect');
                },
                eventClick: function(event) {
                    alert('Événement: ' + event.title + '\nDébut: ' + event.start.format('DD/MM/YYYY HH:mm') + '\nFin: ' + (event.end ? event.end.format('DD/MM/YYYY HH:mm') : 'N/A'));
                },
                events: [
                ]
            });

            var ctx = document.getElementById('pieChart').getContext('2d');
            var pieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode($animaux); ?>,
                    datasets: [{
                        data: <?php echo json_encode($likes); ?>,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw + ' likes';
                                }
                            }
                        }
                    }
                }
            });

            function sendReminder(event) {
                var now = moment();
                var eventStart = moment(event.start);
                if (eventStart.diff(now, 'days') === 1) {
                    alert('Rappel: Vous avez un événement demain - ' + event.title);
                }
            }

            setInterval(function() {
                var events = $('#calendar').fullCalendar('clientEvents');
                events.forEach(function(event) {
                    sendReminder(event);
                });
            }, 60000);
        });
    </script>
</body>

</html>