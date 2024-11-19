<?php
// Connexion à la base de données
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "zoo_arcadia";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erreur de connexion : ' . $conn->connect_error]));
}

// Vérifier que l'ID de l'animal est bien fourni
if (isset($_POST['cardId'])) {
    $cardId = $_POST['cardId'];

    // Requête SQL pour incrémenter le nombre de likes
    $stmt = $conn->prepare("UPDATE animaux SET likes = likes + 1 WHERE id = ?");
    $stmt->bind_param("i", $cardId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur SQL : ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
}

$conn->close();
?>


