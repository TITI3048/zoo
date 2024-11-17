<?php
header('Content-Type: application/json');

// Paramètres de connexion
$servername = "mysql-tibzooarcadia.alwaysdata.net";
$username = "376784";
$password = "Joyce3048.";
$dbname = "tibzooarcadia_zoo";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Échec de la connexion à la base de données.']));
}

// Récupération et validation des données JSON
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['cardId']) || !isset($data['likeCount'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes ou invalides.']);
    exit;
}

$cardId = filter_var($data['cardId'], FILTER_VALIDATE_INT);
$likeCount = filter_var($data['likeCount'], FILTER_VALIDATE_INT);

if ($cardId === false || $likeCount === false) {
    echo json_encode(['success' => false, 'message' => 'Les données ne sont pas valides.']);
    exit;
}

// Préparation et exécution de la requête
$sql = "UPDATE animaux SET likes = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $likeCount, $cardId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'likes_count' => $likeCount]);
} else {
    echo json_encode(['success' => false, 'message' => 'Échec de la mise à jour des likes.']);
}

// Fermeture des connexions
$stmt->close();
$conn->close();
?>
