<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "zoo_arcadia";

// Connexion à la base de données
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connexion échouée : ' . $conn->connect_error]));
}

// Récupérer les données JSON envoyées par le client
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['cardId'])) {
    $cardId = $data['cardId'];

    // Utilisation d'une requête préparée pour éviter les injections SQL
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

