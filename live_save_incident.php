<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) session_start();

require_once "includes/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "ok" => false,
        "msg" => "Not logged in"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$transcript = trim($_POST['text'] ?? '');
$risk_level = floatval($_POST['risk'] ?? 0);
$category   = trim($_POST['category'] ?? 'Normal');
$keywords   = trim($_POST['keywords'] ?? '');

if ($transcript === '') {
    echo json_encode([
        "ok" => false,
        "msg" => "Empty transcript"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$filename = "LIVE_CALL_" . date("Ymd_His") . ".live";

$kwArr = [];
if ($keywords !== '') {
    $kwArr = array_values(array_unique(array_map('trim', explode(',', $keywords))));
}
$suspicious_serial = json_encode($kwArr, JSON_UNESCAPED_UNICODE);

$audio_path = "";

$sql = "INSERT INTO analysis_history
        (user_id, upload_id, filename, transcript, suspicious_keywords, audio_path, result, risk_level, created_at)
        VALUES (?, NULL, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "ok" => false,
        "msg" => "Prepare failed: ".$conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param(
    "issssssd",
    $user_id,
    $filename,
    $transcript,
    $suspicious_serial,
    $audio_path,
    $category,
    $risk_level
);

$ok = $stmt->execute();

echo json_encode([
    "ok" => $ok,
    "msg" => $ok ? "Saved to queue" : ("DB error: ".$stmt->error),
    "id" => $ok ? $stmt->insert_id : null
], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
exit;
?>