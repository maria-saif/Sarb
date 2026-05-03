<?php
session_start();
include 'includes/db_connect.php';
$weights = require __DIR__ . '/keywords_weights.php';

if (!isset($_FILES['audio_chunk']) || $_FILES['audio_chunk']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["error"=>true,"message"=>"No audio chunk uploaded."]);
    exit;
}

$consent = isset($_POST['consent']) && $_POST['consent']==="1"?1:0;

$uploadDir = __DIR__ . '/uploads/live_chunks/';
if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);

$tmpName = $_FILES['audio_chunk']['tmp_name'];
$originalName = basename($_FILES['audio_chunk']['name']);
$ext = pathinfo($originalName,PATHINFO_EXTENSION);
$path = $uploadDir.uniqid("chunk_",true).".".$ext;
move_uploaded_file($tmpName,$path);

$pythonScript = __DIR__ . "/live_transcribe.py";
$command = escapeshellcmd("python3 ".escapeshellarg($pythonScript)." ".escapeshellarg($path));
$output = shell_exec($command);

$text = "";
if ($output){
    $result = json_decode($output,true);
    if (isset($result['transcript'])) $text = $result['transcript'];
}

require_once __DIR__ . '/analysis_functions.php';
$analysis = vs_analyze_text($text,$weights);

$risk_level = $analysis['risk'];
$category = $analysis['category'];
$found_keywords = $analysis['keywords'];
$actions = $analysis['actions'];

if ($consent==0 && file_exists($path)) @unlink($path);

$debug = [
    "filename"=>$originalName,
    "path"=>$path,
    "size"=>$_FILES['audio_chunk']['size'],
    "consent"=>$consent
];

$response = [
    "text"=>$text,
    "risk"=>$risk_level,
    "category"=>$category,
    "keywords"=>implode(", ",$found_keywords),
    "actions"=>$actions,
    "debug"=>$debug
];

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>