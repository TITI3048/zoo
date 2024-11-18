<?php
$servername = "mysql-tibzooarcadia.alwaysdata.net";
$username = "376784";
$password = "Joyce3048.";
$dbname = "tibzooarcadia_zoo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connexion échouée']));
}

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['cardId'])) {
    $cardId = $conn->real_escape_string($data['cardId']);
    $sql = "UPDATE animaux SET likes = likes + 1 WHERE id = '$cardId'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur SQL']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
}

$conn->close();
?>
