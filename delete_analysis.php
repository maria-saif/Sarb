<?php
session_start();
include 'db_connect.php';

$uid = intval($_SESSION['user_id'] ?? 0);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM analysis_history WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $uid);

    if ($stmt->execute()) {
        header("Location: my_analysis.php?deleted=1");
        exit();
    } else {
        echo "Error deleting analysis.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>س