<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "includes/db_connection.php";

if (!isset($_SESSION['user_id'])) {
    error_log("save_analysis: user not logged in");
    return;
}

$user_id   = $_SESSION['user_id'];
$upload_id = $_SESSION['upload_id'] ?? null;

$transcript = $_SESSION['transcribed_text'] ?? '';
$filename   = $_SESSION['uploaded_filename'] ?? '';
$audio_path = "uploads/" . $filename;

if (trim($transcript) === '') {
    error_log("save_analysis: Empty transcript.");
    return;
}


require_once __DIR__ . "/risk_engine.php";
$weights = require __DIR__ . "/keywords_weights.php";

$analysis = vs_analyze_text($transcript, $weights);

$risk_level     = $analysis["risk"];
$result         = $analysis["category"];
$found_keywords = $analysis["keywords"];

$suspicious_serial = json_encode($found_keywords, JSON_UNESCAPED_UNICODE);


$sql = "INSERT INTO analysis_history 
        (user_id, upload_id, filename, transcript, suspicious_keywords, audio_path, result, risk_level, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iisssssd",
    $user_id,
    $upload_id,
    $filename,
    $transcript,
    $suspicious_serial,
    $audio_path,
    $result,
    $risk_level
);

if (!$stmt->execute()) {
    error_log("save_analysis: DB error - " . $stmt->error);
}


$_SESSION['risk_level']     = $risk_level;
$_SESSION['risk_label']     = $result;
$_SESSION['found_keywords'] = $found_keywords;
$_SESSION['actions']        = $analysis["actions"];

?>