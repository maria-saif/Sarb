<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$targetDir = "uploads/";
$filename = basename($_FILES["audioFile"]["name"]);
$targetFile = $targetDir . $filename;

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

if (isset($_FILES["audioFile"]) && move_uploaded_file($_FILES["audioFile"]["tmp_name"], $targetFile)) {

    $_SESSION['uploaded_filename'] = $filename;
    $_SESSION['uploaded_audio_path'] = '/Sarb/' . $targetFile;

    require_once __DIR__ . "/db_connect.php";

    $filename_db = $filename;
    $filepath_db = $targetFile; 

    $stmt = $conn->prepare("INSERT INTO uploads (filename, filepath) VALUES (?, ?)");
    $stmt->bind_param("ss", $filename_db, $filepath_db);
    $stmt->execute();

    $upload_id = $stmt->insert_id;
    $_SESSION['upload_id'] = $upload_id;

    $escapedPath = escapeshellarg($targetFile);
    $ffmpeg_path = '/opt/homebrew/bin'; 
    $command = "PATH=$ffmpeg_path:\$PATH python3 vosk_transcribe.py $escapedPath";

    $descriptorspec = [1 => ["pipe", "w"], 2 => ["pipe", "w"]];
    $process = proc_open($command, $descriptorspec, $pipes);

    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        file_put_contents('debug_output.txt', "Output:\n$output\n\nError:\n$errorOutput");

        if (trim($output)) {
            $_SESSION['transcribed_text'] = trim($output);

            if (file_exists(__DIR__ . "/save_analysis.php")) {
                include __DIR__ . "/save_analysis.php";
            }

            header("Location: result.php");
            exit();
        } else {
            echo "<pre>❌ Error: No valid text output from Python.\n\n$errorOutput</pre>";
        }
    } else {
        echo "❌ Failed to run Python script.";
    }
} else {
    echo "❌ Failed to upload the audio file.";
}
?>